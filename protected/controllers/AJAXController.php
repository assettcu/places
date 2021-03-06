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
                "http://compass.colorado.edu/ascore/api/buildingclasses",
                array(
                    "building"  => $place->metadata->data["building_code"]["value"],
                    "term"      => $yt, # Semester/Year to lookup
                    )
                );
        }
        # Load classes for a classroom
        else if($place->placetype->machinecode == "classroom") {
            $parent = $place->get_parent();
            $parent->load_metadata();
            $building_code = $parent->metadata->data["building_code"]["value"];
            $classes = StdLib::external_call(
                "http://compass.colorado.edu/ascore/api/classroomclasses",
                array(
                    "building"  => $building_code,
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
        ?> <ul data-role="listview" data-inset="true" data-filter="true" data-filter-placeholder="Filter classes" class="course-listing ui-icon-alt"> <?php
        if(count($classes) > 0): ?>
            <?php 
              $count=0; 
              foreach($classes as $class): 
                $count++; 
                $starttime  = $class["timestart"];
                $endtime    = $class["timeend"];
                $datetime = new DateTime($starttime);
                $starttime = $datetime->format("g:i a");
                $datetime = new DateTime($endtime);
                $endtime = $datetime->format("g:i a");
                $catalog_term = "2013-14";
            ?>

            <li>
              <a href="<?php echo Yii::app()->createUrl("place"); ?>?id=<?php echo $class["building"]." ".$class["roomnum"]; ?>" ref="external">
                <h2>
                  <?php echo $class["subject"]; ?> <?php echo $class["course"]; ?>-<span style="font-weight:normal;"><?php echo substr("00".$class["section"],-3,3); ?></span>
                </h2>
                <span class="ui-li-aside">
                  <?php echo $class["building"]." ".$class["roomnum"]; ?>
                </span>
                <p style="font-style:italic; font-size: 0.8rem;"><?php echo $class["title"]; ?></p>
                <p><?php echo $class["meetingdays"]; ?> <?php echo @$starttime." - ".@$endtime; ?></td></p>
              </a>
            </li>
          <?php endforeach; ?>
        <?php else : ?>
          <li>
            <p style="white-space:normal; text-align:center;">There are no classes in this <?php echo strtolower($place->placetype->singular); ?> currently for <?php echo $yt; ?>.</p>
          </li>
        <?php endif;
        ?> </ul> <?php
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
    
    public function actionLoadParents()
    {
        $rest = new RestServer();
        $request = RestUtils::processRequest();
        $required = array("placetypeid");
        $keys = array_keys($request);
        if(count(array_intersect($required, $keys)) != count($required)) {
            return RestUtils::sendResponse(308);
        }
        
        $results = Yii::app()->db->createCommand()
            ->select("placeid, placename")
            ->from("places")
            ->where("placetypeid = (SELECT parentid FROM placetypes WHERE placetypeid = :placetypeid)",
                array("placetypeid"=>$request["placetypeid"])
            )
            ->order("placename ASC")
            ->queryAll();
        
        if(empty($results)) {
            $output = "<option value='0'>No Parent</option>";
        }
        else {
            ob_start();
            foreach($results as $row) {
                echo "<option value='".$row["placeid"]."'>".$row["placename"]."</option>";
            }
            $output = ob_get_contents();
            ob_end_clean();
        }
        return print $output;
    }

    public function actionLoadPlaces()
    {
        $rest = new RestServer();
        $request = RestUtils::processRequest();
        $required = array("placetypeid");
        $keys = array_keys($request);
        if(count(array_intersect($required, $keys)) != count($required)) {
            return RestUtils::sendResponse(308);
        }
        
        $results = Yii::app()->db->createCommand()
            ->select("placeid, placename, parentid")
            ->from("places")
            ->where("placetypeid = :placetypeid",
                array("placetypeid"=>$request["placetypeid"])
            )
            ->order("parentid ASC, placename ASC")
            ->queryAll();
        
        if(empty($results)) {
            $output = "<option value='0'></option>";
        }
        else {
            ob_start();
            $curparentid = -1;
            $header = false;
            foreach($results as $row) {
                if($curparentid != $row["parentid"] and $header) {
                    echo "</optgroup>";
                }
                if($curparentid != $row["parentid"]) {
                    $header = true;
                    $parent = new PlacesObj($row["parentid"]);
                    if($parent->loaded) {
                        $curparentid = $row["parentid"];
                        echo "<optgroup label='".$parent->placename."'>";
                    }
                }
                echo "<option value='".$row["placeid"]."'>".$row["placename"]."</option>";
            }
            $output = ob_get_contents();
            ob_end_clean();
        }
        return print $output;
    }

    public function actionUploadImages()
    {
        
        /**
         * upload.php
         *
         * Copyright 2009, Moxiecode Systems AB
         * Released under GPL License.
         *
         * License: http://www.plupload.com/license
         * Contributing: http://www.plupload.com/contributing
         */
        
        // HTTP headers for no cache etc
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        
        // Settings
        $targetDir = getcwd().'/temp/'.Yii::app()->user->name."/";
        
        $cleanupTargetDir = true; // Remove old files
        $maxFileAge = 5 * 3600; // Temp file age in seconds
        
        // 5 minutes execution time
        @set_time_limit(5 * 60);
        
        // Uncomment this one to fake upload time
        // usleep(5000);
        
        // Get parameters
        $chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
        $chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;
        $fileName = isset($_REQUEST["name"]) ? $_REQUEST["name"] : '';
        
        // Clean the fileName for security reasons
        $fileName = preg_replace('/[^\w\._]+/', '_', $fileName);
        
        // Make sure the fileName is unique but only if chunking is disabled
        if ($chunks < 2 && file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName)) {
            $ext = strrpos($fileName, '.');
            $fileName_a = substr($fileName, 0, $ext);
            $fileName_b = substr($fileName, $ext);
        
            $count = 1;
            while (file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName_a . '_' . $count . $fileName_b))
                $count++;
        
            $fileName = $fileName_a . '_' . $count . $fileName_b;
        }
        
        $filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;
        
        // Create target dir
        if (!file_exists($targetDir))
            @mkdir($targetDir);
        
        // Remove old temp files    
        if ($cleanupTargetDir) {
            if (is_dir($targetDir) && ($dir = opendir($targetDir))) {
                while (($file = readdir($dir)) !== false) {
                    $tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;
        
                    // Remove temp file if it is older than the max age and is not the current file
                    if (preg_match('/\.part$/', $file) && (filemtime($tmpfilePath) < time() - $maxFileAge) && ($tmpfilePath != "{$filePath}.part")) {
                        @unlink($tmpfilePath);
                    }
                }
                closedir($dir);
            } else {
                die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');
            }
        }   
        
        // Look for the content type header
        if (isset($_SERVER["HTTP_CONTENT_TYPE"]))
            $contentType = $_SERVER["HTTP_CONTENT_TYPE"];
        
        if (isset($_SERVER["CONTENT_TYPE"]))
            $contentType = $_SERVER["CONTENT_TYPE"];
        
        // Handle non multipart uploads older WebKit versions didn't support multipart in HTML5
        if (strpos($contentType, "multipart") !== false) {
            if (isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
                // Open temp file
                $out = @fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");
                if ($out) {
                    // Read binary input stream and append it to temp file
                    $in = @fopen($_FILES['file']['tmp_name'], "rb");
        
                    if ($in) {
                        while ($buff = fread($in, 4096))
                            fwrite($out, $buff);
                    } else
                        die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
                    @fclose($in);
                    @fclose($out);
                    @unlink($_FILES['file']['tmp_name']);
                } else
                    die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
            } else
                die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
        } else {
            // Open temp file
            $out = @fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");
            if ($out) {
                // Read binary input stream and append it to temp file
                $in = @fopen("php://input", "rb");
        
                if ($in) {
                    while ($buff = fread($in, 4096))
                        fwrite($out, $buff);
                } else
                    die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
        
                @fclose($in);
                @fclose($out);
            } else
                die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
        }
        
        // Check if file has been uploaded
        if (!$chunks || $chunk == $chunks - 1) {
            // Strip the temp .part suffix off 
            rename("{$filePath}.part", $filePath);
        }
        
        die('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');

    } 

    public function actionRemovePicture()
    {
        $rest = new RestServer();
        $request = RestUtils::processRequest();
        $required = array("pictureid");
        $keys = array_keys($request);
        if(count(array_intersect($required, $keys)) != count($required)) {
            return RestUtils::sendResponse(308);
        }
        
        $picture = new PictureObj($request["pictureid"]);
        $picture->delete();
        
        return true;
    }
    
    public function actionMakeCoverPhoto()
    {
        $rest = new RestServer();
        $request = RestUtils::processRequest();
        $required = array("pictureid");
        $keys = array_keys($request);
        if(count(array_intersect($required, $keys)) != count($required)) {
            return RestUtils::sendResponse(308);
        }
        
        $picture = new PictureObj($request["pictureid"]);
        
        Yii::app()->db->createCommand()
            ->update("placepictures", array(
                'coverphoto' => 0
            ), 'placeid=:placeid', array(':placeid'=>$picture->placeid));
            
        Yii::app()->db->createCommand()
            ->update("placepictures", array(
                'coverphoto' => 1
            ), 'pictureid=:pictureid', array(':pictureid'=>$picture->pictureid));
        
        return true;
    }
    
    public function actionHasChildren()
    {
        $rest = new RestServer();
        $request = RestUtils::processRequest();
        $required = array("placeid");
        $keys = array_keys($request);
        if(count(array_intersect($required, $keys)) != count($required)) {
            return RestUtils::sendResponse(308);
        }
        
        $place = new PlacesObj($request["placeid"]);
        return print json_encode($place->has_children());
    }
    
    public function actionDeletePlace()
    {
        $rest = new RestServer();
        $request = RestUtils::processRequest();
        $required = array("placeid");
        $keys = array_keys($request);
        if(count(array_intersect($required, $keys)) != count($required)) {
            return RestUtils::sendResponse(308);
        }
        
        $place = new PlacesObj($request["placeid"]);
        $place->delete();
        
        Yii::app()->user->setFlash('success',"Successfully deleted ".$place->placename." from places.");
        return print true;
    }
}
