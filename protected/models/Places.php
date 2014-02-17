<?php

class Places
{
  
  public function __construct()
  {
    
  }
  
  public function load_all_places()
  {
    $places = array();
    
    $conn = Yii::app()->db;
    $query = "
      SELECT      places.placeid
      FROM        {{places}}
      INNER JOIN  {{placetypes}}
      ON          places.placetypeid = placetypes.placetypeid
      ORDER BY    placetypes.machinecode ASC, places.placename ASC;
    ";
    $result = $conn->createCommand($query)->queryAll();
    
    if(!$result or empty($result)) return array();
    
    foreach($result as $row)
      $places[] = new PlacesObj($row["placeid"]);
      
    return $places;
    
  }
  
  public function load_places($placetype="place")
  {
    $places = array();
    
    $conn = Yii::app()->db;
    $query = "
      SELECT      places.placeid
      FROM        {{places}}
      INNER JOIN  {{placetypes}}
      ON          places.placetypeid = placetypes.placetypeid
      AND         placetypes.machinecode = :machinecode
      ORDER BY    places.sorder ASC;
    ";
    $command = $conn->createCommand($query);
    $command->bindParam(":machinecode",$placetype);
    $result = $command->queryAll();
    
    if(!$result or empty($result)) return array();
    
    foreach($result as $row)
      $places[] = new PlacesObj($row["placeid"]);
      
    return $places;
  }
  
  public function load_places_types()
  {
    $places = array();
    
    $conn = Yii::app()->db;
    $query = "
      SELECT        placetypeid
      FROM          {{placetypes}}
      WHERE         1=1
      ORDER BY      parentid ASC;
    ";
    $result = $conn->createCommand($query)->queryAll();
    
    if(!$result or empty($result)) return array();
    
    foreach($result as $row)
      $placestypes[] = new PlaceTypesObj($row["placetypeid"]);
      
    return $placestypes;
  }
  
  public function search($search)
  {
  	$search_params = explode(" ",$search);
  	
  	$query = array();
  	
  	$placetypes = $this->load_places_types();
  	$placetype_flag = false;
	
  	foreach($search_params as $param)
	{
		$param = strtolower($param);
		foreach($placetypes as $placetype)
		{
			if(strtolower($placetype->name) == $param or strtolower($placetype->singular) == $param)
			{
				$placetype_query = "placetypeid = (SELECT placetypeid FROM {{placetypes}} WHERE machinecode LIKE '$param' OR name LIKE '$param' LIMIT 1)";
				$placetype_flag = true;
				break;
			}
		}
		$query[] = "(parentid = (SELECT placeid FROM {{places}} WHERE placename LIKE '%$param%' LIMIT 1) OR placename LIKE '%$param%')";
	}

	$q = implode(" AND ",$query);
	
	if($placetype_flag)
		$q = $placetype_query . " AND (" . $q . ")";
	
	$q = "SELECT placeid FROM {{places}} WHERE ".$q;
	
	$q = $q . " ORDER BY placetypeid = (SELECT placetypeid FROM {{placetypes}} WHERE machinecode = 'building' LIMIT 1) DESC, placename ASC";
	
	$conn = Yii::app()->db;
	$result = $conn->createCommand($q)->queryAll();
	
	
	$placeids = array();
	foreach($result as $row)
		$placeids[] = $row["placeid"];
		
	$placeids = array_unique($placeids);
	foreach($placeids as $placeid)
		$places[] = new PlacesObj($placeid);
	
	return $places;
  }
 
}