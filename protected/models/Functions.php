<?php

function load_all_places() {
    $places = array();

    $conn = Yii::app() -> db;
    $query = "
        SELECT      places.placeid
        FROM        {{places}}
        INNER JOIN  {{placetypes}}
        ON          places.placetypeid = placetypes.placetypeid
        ORDER BY    placetypes.machinecode ASC, places.placename ASC;
    ";
    $result = $conn -> createCommand($query) -> queryAll();

    if (!$result or empty($result))
        return array();

    foreach ($result as $row)
        $places[] = new PlacesObj($row["placeid"]);

    return $places;

}

function load_places($placetype = "place") 
{
    $places = array();

    $conn = Yii::app() -> db;
    $query = "
        SELECT      places.placeid
        FROM        {{places}}
        INNER JOIN  {{placetypes}}
        ON          places.placetypeid = placetypes.placetypeid
        AND         placetypes.machinecode = :machinecode
        ORDER BY    places.placename ASC;
    ";
    $command = $conn -> createCommand($query);
    $command -> bindParam(":machinecode", $placetype);
    $result = $command -> queryAll();

    if (!$result or empty($result))
        return array();

    foreach ($result as $row)
        $places[] = new PlacesObj($row["placeid"]);

    return $places;
}

function load_places_types() 
{
    $places = array();

    $conn = Yii::app() -> db;
    $query = "
        SELECT        placetypeid
        FROM          {{placetypes}}
        WHERE         1=1
        ORDER BY      parentid ASC;
    ";
    $result = $conn -> createCommand($query) -> queryAll();

    if (!$result or empty($result))
        return array();

    foreach ($result as $row) {
        $placestypes[] = new PlaceTypesObj($row["placetypeid"]);
    }
    
    return $placestypes;
}

function search($search) 
{
    $search_params = explode(" ", $search);

    $query = array();

    $placetypes = load_places_types();
    $placetype_flag = false;

    foreach ($search_params as $param) {
        $param = strtolower($param);
        foreach ($placetypes as $placetype) {
            if (strtolower($placetype->name) == $param or strtolower($placetype->singular) == $param) {
                $placetype_query = "placetypeid = (SELECT placetypeid FROM {{placetypes}} WHERE machinecode LIKE '$param' OR name LIKE '$param' LIMIT 1)";
                $placetype_flag = true;
                break;
            }
        }
        $query[] = "(parentid = (SELECT placeid FROM {{places}} WHERE placename LIKE '%$param%' LIMIT 1) OR placename LIKE '%$param%')";
    }

    $q = implode(" AND ", $query);

    if ($placetype_flag)
        $q = $placetype_query . " AND (" . $q . ")";

    $q = "SELECT placeid FROM {{places}} WHERE " . $q;

    $q = $q . " ORDER BY placetypeid = (SELECT placetypeid FROM {{placetypes}} WHERE machinecode = 'building' LIMIT 1) DESC, placename ASC";

    $conn = Yii::app()->db;
    $result = $conn->createCommand($q)->queryAll();

    $placeids = array();
    foreach ($result as $row) {
        $placeids[] = $row["placeid"];
    }
    
    $placeids = array_unique($placeids);
    $places = array();
    foreach ($placeids as $placeid) {
        $places[] = new PlacesObj($placeid);
    }
    
    if(empty($places)) {
        $places = search_classes($search);
        $search_type = "classes";
    }
    else {
        $search_type = "placename";
    }
    
    return array("search_type"=>$search_type,"places"=>$places);
}

function search_classes($search)
{
    # Load classes for a building
    $classes = StdLib::external_call(
        "http://assettdev.colorado.edu/ascore/api/searchclasses",
        array(
            "search"  => $search,
        )
    );
    $places = array();
    foreach($classes as $loc) {
        $result = Yii::app()->db->createCommand()
            ->select("placeid")
            ->from("places")
            ->where("placename = :placename",
                array(
                    ":placename"    => $loc["building"]." ".$loc["roomnum"]
                )
            )
            ->queryRow();
       
       if(!$result or empty($result)) {
           continue;
       }
       $place = new PlacesObj($result["placeid"]);
       $place->yearterm = $loc["yearterm"];
       $place->class = $loc["title"];
       $places[] = $place;
    }
    
    return $places;
}
    
function load_child_places($parentid)
{
    $result = Yii::app()->db->createCommand()
        ->select("placeid")
        ->from("places")
        ->where("parentid = :parentid", array(":parentid"=>$parentid))
        ->order("placetypeid ASC, sorder ASC")
        ->queryAll();
        
    if(!$result or empty($result)) {
        return array();
    }
    
    $places = array();
    foreach($result as $row) {
        $places[] = new PlacesObj($row["placeid"]);
    }
    
    return $places;
}

function yearterm_code_to_display($code) {
    $term = substr($code,-1,1);
    switch($term) {
        case "1": return "Spring ".substr($code,0,4);
        case "4": return "Summer ".substr($code,0,4);
        case "7": return "Fall ".substr($code,0,4);
        default: return substr($code,0,4);
    }
}
