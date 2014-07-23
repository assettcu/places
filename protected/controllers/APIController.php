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
        
        $markers = array(
            "markers"   => array(
                array(
                    "latitude"  => $place->metadata->data["latitude"]["value"],
                    "longitude" => $place->metadata->data["longitude"]["value"],
                    "title"     => $place->placename,
                    "content"   => "Content about place here"
                )
            )
        );
        
        return print json_encode($markers);
        # json_encode($classes);
    }
}
