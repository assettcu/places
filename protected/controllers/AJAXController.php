<?php
header('Access-Control-Allow-Origin: *');

require "BaseController.php";

class AJAXController extends BaseController
{
    
    public function actionLoadClasses() {
        $rest = new RestServer();
        $request = RestUtils::processRequest();
        $required = array("id","yt");
        $keys = array_keys($request);
        if(count(array_intersect($required, $keys)) != count($required)) {
            return RestUtils::sendResponse(308);
        }
        
        # array("id"=>"#", "yt"=>"#")
        extract($request);
        $place = new PlacesObj($id);
        $place->load_metadata();
        
        # Load classes for a building
        if($place->placetype->machinecode == "building") {
            $classes = StdLib::external_call(
                "http://assettdev.colorado.edu/ascore/api/buildingclasses",
                array(
                    "building"  => $place->metadata->data["building_code"]["value"],
                    "term"      => $yt, # Semester/Year to lookup
                )
            );
        }
        # Load classes for a classroom
        else if($place->placetype->machinecode == "classroom") {
            $classes = StdLib::external_call(
                "http://assettdev.colorado.edu/ascore/api/classroomclasses",
                array(
                    "classroom" => $place->placename,
                    "term"      => $yt, # Semester/Year to lookup
                )
            );
        }
        # Don't load classes if other
        else {
            $classes = array();
        }
        
        # Load the child place names
        $childplace_names = array();
        $childplaces = $place->get_children();
        foreach($childplaces as $child) {
            $childplace_names[] = $child->placename;
        }
        
        ob_start();
        if(empty($classes)) {
            ?>
            <tr>
                <td class="empty" colspan="7">
                    There are no classes in this <?php echo strtolower($place->placetype->singular); ?> currently for <?php echo $yt; ?>.
                </td>
            </tr>
            <?php
        }
        else {
           $count=0; foreach($classes as $class): $count++; ?>
                <?php
                # Do some processing before displaying
                $starttime  = $class["timestart"];
                $endtime    = $class["timeend"];
                $datetime = new DateTime($starttime);
                $starttime = $datetime->format("g:i a");
                $datetime = new DateTime($endtime);
                $endtime = $datetime->format("g:i a");
                
                $catalog_term = "2013-14";
                ?>
            <tr class="<?php echo ($count%2==0) ? 'odd' : 'even'; ?>">
                <td>
                    <a href="http://www.colorado.edu/catalog/<?php echo $catalog_term; ?>/courses?subject=<?php echo $class["subject"]; ?>&number=<?php echo $class["course"]; ?>" target="_blank">
                        <?php echo $class["subject"]; ?> <?php echo $class["course"]; ?>
                    </a>
                </td>
                <td class="calign"><?php echo substr("00".$class["section"],-3,3); ?></td>
                <td><?php echo $class["title"]; ?></td>
                <?php if($place->placetype->machinecode == "building" and in_array($class["building"]." ".$class["roomnum"],$childplace_names,FALSE)): ?>
                <td class="calign">
                    <a href="<?php echo Yii::app()->createUrl("place"); ?>?id=<?php echo $class["building"]." ".$class["roomnum"]; ?>" ref="external"><?php echo $class["building"]." ".$class["roomnum"]; ?></a>
                </td>
                <?php else: ?>
                <td class="calign"><?php echo $class["building"]." ".$class["roomnum"]; ?></td>
                <?php endif; ?>
                <td class="calign"><?php echo $class["meetingdays"]; ?> <?php echo @$starttime." - ".@$endtime; ?></td>
            </tr>
            <?php endforeach;
        }
        $return = ob_get_contents();
        ob_end_clean();
        
        return print $return;
    }
    
    public function actionFBLookup() 
    {
        $rest = new RestServer();
        $request = RestUtils::processRequest();
        $required = array("q");
        $keys = array_keys($request);
        if(count(array_intersect($required, $keys)) != count($required)) {
            return RestUtils::sendResponse(308);
        }
        
        # The Directory we're connecting with is the Active Directory for the Campus 
        # (not to be confused with this application's name)
        $ldap = new ADAuth("directory");
        $ldap->bind_anon();
        $info = $ldap->lookup_user($request["q"]);
        
        if($info["count"] == 0) {
            return print json_encode(array());
        }
        
        return print json_encode(array($request["attribute"] => @$info[0][$request["attribute"]][0]));
    }
}
