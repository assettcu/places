<?php
/**
 * API Controller
 *
 * The API Controller will act as the Application Programming Interface for this application.
 * It will have functions that will serve up application specific information. The information
 * will be "public" in nature, which means anyone who is not authorized can still view that information.
 * 
 * Private information such as user accounts will not be available for API calls.
 * 
 * This class conforms to the Model-View-Controller paradigm.
 * 
 */
 
header('Access-Control-Allow-Origin: *');

require "BaseController.php";

class APIController extends BaseController
{
    /**
     * This will return classes given a building and a year/term.
     */
    public function actionPlaceMap()
    {
        $rest = new RestServer();
        $request = RestUtils::processRequest();
        $required = array("id");
        $keys = array_keys($request);
        if(count(array_intersect($required, $keys)) != count($required)) {
            return RestUtils::sendResponse(308);
        }
        
        $place = new PlacesObj($request["id"]);
        $place->load_metadata();
        $place->load_images();
        
        if(!empty($place->images)) {
            $image = "<img src='".$place->get_thumb_path($place->images[0],"href")."' width='50px' height='50px' style='vertical-align:top;margin-right:6px;margin-bottom:6px;' />";
        }
        else {
            $image = "";
        }
        ob_start();
        ?>
        <div class="content-box" style="font-size:15px;width:230px;min-height:50px;">
            <?php echo $image; ?>
            <span class="title" style="font-size:18px;"><?php echo $place->placename; ?></span><br/>
            <span class="description"><?php echo $place->description; ?></span>
            <div style="margin-top:10px;"><a href="#">Get Directions To Here</a></div>
        </div>
        <?php
        $contents = ob_get_contents();
        ob_end_clean();
        
        $markers = array(
            "markers"   => array(
                array(
                    "latitude"  => $place->metadata->data["latitude"]["value"],
                    "longitude" => $place->metadata->data["longitude"]["value"],
                    "title"     => $place->placename,
                    "content"   => $contents,
                )
            )
        );
        
        return print json_encode($markers);
    }
}
