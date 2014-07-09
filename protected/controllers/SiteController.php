<?php

class SiteController extends Controller
{
	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
		);
	}
	
	public function beforeAction($action)
	{
		if($this->getIsMobile()) {
			Yii::app()->theme = 'mobile';
		}
		return $action;
	}

    /**
     * Installation action. To be done only once when the application is first being setup.
     */
    public function actionInstall()
    {
        # Let's destroy any sessions currently, just in case.
        @Yii::app()->session->destroy();
        
        if(isset($_REQUEST["yii"]) and $_REQUEST["yii"] == "installed") {
            Yii::app()->user->setFlash("success","Succssfully installed the Yii Framework! Continue the installation by filling in the form below.");
            $this->redirect("install");
            exit;
        }
        
        # Does the application need installing? Check if database exists.
        $config_ext = Yii::app()->basePath."\\config\\main-ext.php";
        if(is_file($config_ext)) {
            Yii::app()->user->setFlash("warning","This application is already installed. Please delete the main-ext.php file to re-install.");
            $this->redirect(Yii::app()->createUrl('index'));
        }
        
        # Submitted form. Technically only one stage but verifies form was submitted.
        if(isset($_REQUEST["stage"]) and $_REQUEST["stage"] == "init") {
            # Create a new System without initializing
            $system = new System(false);
            
            # Install the system
            if($system->install()) {
                # Redirect to main page, the system will catch that we have no tables installed and will install them.
                $this->redirect(Yii::app()->baseUrl);
                exit;
            }
            else {
                Yii::app()->user->setFlash("error","There was an error installing the application: ".$system->get_error());
            }
        }
        
        # Render the form interface
        $this->render("install");
    }

	/** Google Analytics **/
	protected function beforeRender($view)
	{
		$return = parent::beforeRender($view);
		// Yii::app()->googleAnalytics->render();
		return $return;
	}
	
	public function actionToStandard()
	{
		Yii::app()->user->setState("mobile",false);
		$this->redirect(Yii::app()->createUrl("index"));
		exit;
	}
	
	public function actionToMobile()
	{
		Yii::app()->user->setState("mobile",true);
		$this->redirect(Yii::app()->createUrl("index"));
		exit;
	}

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{
		// renders the view file 'protected/views/site/index.php'
		// using the default layout 'protected/views/layouts/main.php'
		$this->render('index');
	}
	
	public function actionRunOnce()
	{
		$this->renderPartial("runonce");
	}

	public function actionPlace()
	{
		$this->render('place');
	}
    
    public function actionEditPlace()
    {
    	$this->login_only();
    	$params["error"] = "";
        
        $place = new PlacesObj($_REQUEST["id"]);
		$place->load_metadata();
        $params["place"] = $place;
		
    	if(isset($_REQUEST["id"],$_REQUEST["placename"],$_REQUEST["placetypeid"],$_REQUEST["parentid"]))
		{
			
	        $place->placename = @$_REQUEST["placename"];
	        $place->placetypeid = @$_REQUEST["placetypeid"];
	        $place->parentid = @$_REQUEST["parentid"];
			
			if(!$place->save())
			{
				$params["error"] = $place->get_error();
			}
			
			if($params["error"]=="")
			{
				unset($_REQUEST["id"],$_REQUEST["placename"],$_REQUEST["placetypeid"],$_REQUEST["parentid"]);
				
				$data = $_REQUEST;
				foreach($data as $index=>$item)
				{
					$place->metadata->$index = $item;
				}
				
				if(!$place->metadata->save())
				{
					$params["error"] = $place->metadata->get_error();
				}
				else
				{
					$this->redirect(Yii::app()->createUrl('place')."?id=".$place->placeid);
					exit;
				}
			}
				
		}
		
        $this->render("editplace",$params);
    }
	
	public function actionNewInfoType()
	{
    	$this->login_only();
		if(!isset($_REQUEST["id"])) 
		{
		    Yii::app()->user->setFlash("warning","Could not add new info type.");
			$this->redirect(Yii::app()->createUrl('index'));
			exit;
		}
		
		$place = new PlacesObj($_REQUEST["id"]);
		$place->load_metadata();
		if($place->loaded) {
			if($place->metadata->loaded) {
				$metadata = $place->metadata->load_all_metadata_ext();
			}
		}
		
		$params["place"] = $place;
		$params["metadata"] = $metadata;
		
		$this->render('newinfotype',$params);
	}


	public function actionClassroom()
	{
		$this->render('classroom');
	}
	
	public function actionAddPlace()
	{
    	$this->login_only();
	    $params = array();
        $place = new PlacesObj();
		
        $place->placename = @$_REQUEST["placename"];
        $place->placetypeid = @$_REQUEST["placetypeid"];
        $place->parentid = @$_REQUEST["parentid"];
        
		if(isset($_REQUEST["placetype"]))
		{
			$place->get_placetype_id($_REQUEST["placetype"]);
		}
		$params["place"] = $place;
		
	    if(isset($_REQUEST["parentid"],$_REQUEST["placename"],$_REQUEST["placetypeid"]))
        {
            if(!$place->save())
            {
                Yii::app()->user->setFlash("error","Error saving place: ".$place->get_error());
            } else {
                $this->redirect(Yii::app()->createUrl('pictures')."?id=".$place->placeid);
                exit;
            }
        }
        
		$this->render('addplace',$params);
	}
	
    public function actionPictures()
    {
        $this->render('pictures');
    }
    
	public function actionSearch()
	{
		$this->render('search',$_REQUEST);
	}
	
    /**
     * The actions below are for AJAX calls only
     */
     
     public function action_add_uploaded_file()
     {
         $place = new PlacesObj($_REQUEST["placeid"]);
         $picture = new PictureObj();
         $picture->placeid = $_REQUEST["placeid"];
         if($place->placetype->machinecode == "classroom" or $place->placetype->machinecode == "lab") {
            $place->load_parent();
            $picture->picturename = $place->parent_->placename;
         } else {
            $picture->picturename = $place->placename;
         }
         $filename = preg_replace('/[^\w\._]+/', '_', $_REQUEST["filename"]);
         $dir = '/images/'.$place->placetype->name."/".str_replace(" ","_",$place->placename);
         $picture->path = $dir."/".$filename;
         $picture->type = strtolower(substr($_REQUEST["filename"],-3,3));
         
         if(!is_dir(getcwd().$dir)) {
             mkdir(getcwd().$dir);
         }
         
		 if(is_file(getcwd().$picture->path)) {
			 if(!$picture->save()) {
				 Yii::app()->user->setFlash("error","Could not save picture: ".$picture->get_error());
			 } 
			 else {
				$picture->make_thumb();
			 }
		 }
         else {
             Yii::app()->user->setFlash("error","Could not find file: ".getcwd().$picture->path);
         }
         
         return true;
     }
    
    public function action_upload_images()
    {
        // HTTP headers for no cache etc
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        
        $place = new PlacesObj($_REQUEST["placeid"]);
        
        // Settings
        $targetDir = getcwd().'/images/'.$place->placetype->name."/".str_replace(" ","_",$place->placename)."/";
        
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
     
     public function action_reorder_picture()
	 {
    	$this->login_only();
	 	if(!isset($_REQUEST))
	 		return print "Did not recieve any information.";
		
	 	$picture = new PictureObj($_REQUEST["pictureid"]);
		if(!$picture->loaded)
			return print "Could not find picture in the database.";
		
		$picture->sorder = $_REQUEST["sorder"];
		
		if(!$picture->save())
			return print $picture->get_error();
	 	
		return print 1;
	 	
	 }
	 
	 public function action_delete_place()
	 {
    	$this->login_only();
	 	if(!isset($_REQUEST))
	 		return print "Did not recieve any information.";
		
		$place = new PlacesObj($_REQUEST["id"]);
		if(!$place->loaded)
			return print "Could not find place in the database.";
		
		if(!$place->delete())
			return print $place->get_error();
	 	
		return print 1;
	 }
     
     public function action_change_picture_info()
	 {
    	$this->login_only();
	 	if(!isset($_REQUEST))
	 		return print "Did not recieve any information.";
		
	 	$picture = new PictureObj($_REQUEST["pictureid"]);
	 	$picture->picturename = $_REQUEST["picturename"];
	 	$picture->caption = $_REQUEST["caption"];
	 	$picture->description = $_REQUEST["description"];
		
		if(!$picture->save())
			return print $picture->get_error();
	 	
		$place = new PlacesObj($picture->placeid);
		$place->date_modified = date("Y-m-d H:i:s");
		$place->save();
		
		return print 1;
	 }
     
     public function action_delete_picture()
	 {
    	$this->login_only();
	 	if(!isset($_REQUEST))
	 		return print "Did not recieve any information.";
		
	 	$picture = new PictureObj($_REQUEST["pictureid"]);
		if(!$picture->delete())
			return print $picture->get_error();
		
		return print 1;
	 }
     
     public function action_update_infotype_sorder()
	 {
    	$this->login_only();
	 	if(!isset($_REQUEST))
	 		return print "Did not recieve any information.";
		
		$data = $_REQUEST;
		
		$metadata = new MetadataObj();
		$statuscode = $metadata->update_sorder($data);
		switch($statuscode)
		{
			case 1: return print 1;
			case -1: return print "Incorrect number of parameters passed.";
			case -2: return print "Could not update order.";
			return print $statuscode;
		}
		
		return print 1;
	 }
	 
	 public function action_delete_infotype()
	 {
    	$this->login_only();
	 	if(!isset($_REQUEST))
	 		return print "Did not recieve any information.";
		
		$data = $_REQUEST;
		
		$metadata = new MetadataObj();
		$statuscode = $metadata->delete_infotype($data);
		switch($statuscode)
		{
			case 1: return print 1;
			case -1: return print "Incorrect number of parameters passed.";
			case -2: return print "Could not delete infotype.";
			return print $statuscode;
		}
		
		return print 1;
	 }
     
     public function action_edit_infotype()
	 {
    	$this->login_only();
	 	if(!isset($_REQUEST)) 
	 		return print "Did not recieve any information.";
		
		$data = $_REQUEST;
		
		if(!isset($data["display_name"],$data["column_name"],$data["metatype"],$data["inputtype"]))
			return print "Did not recieve proper form data.";
		
		$metadata = new MetadataObj();
		$statuscode = $metadata->edit_infotype($data);
		switch($statuscode)
		{
			case 1: return print 1;
			case -1: return print "Could not alter table to add column.";
			case -2: return print "This metadata already exists for this place.";
			case -3: return print "Could not insert metadata into metadata extended table.";
			return print $statuscode;
		}
		
		return print 1;	
	 }
     
     public function action_add_infotype()
	 {
    	$this->login_only();
	 	if(!isset($_REQUEST)) 
	 		return print "Did not recieve any information.";
		
		$data = $_REQUEST;
		
		if(!isset($data["display_name"],$data["column_name"],$data["metatype"],$data["inputtype"]))
			return print "Did not recieve proper form data.";
		
		$metadata = new MetadataObj();
		$statuscode = $metadata->add_infotype($data);
		switch($statuscode)
		{
			case -1: return print "Could not alter table to add column.";
			case -2: return print "This metadata already exists for this place.";
			case -3: return print "Could not insert metadata into metadata extended table.";
		}
		
		ob_start();
		?>
		<tr class="actual-row">
			<td class="calign mover" style="width:30px;cursor:move;min-height:36px;height:25px;">
				<?=StdLib::load_image("move.png","16px");?>
			</td>
			<td class="calign display_name" value="<?=$data["display_name"];?>" style="width:243px;"><?=$data["display_name"];?></td>
			<td class="calign column_name" value="<?=$data["column_name"];?>"  style="width:243px;"><?=$data["column_name"];?></td>
			<td class="calign metatype" value="<?=$data["metatype"];?>"  style="width:179px;"><?php
				switch($data["metatype"])
				{
					case "none": echo "None"; break;
					case "students": echo "Students"; break;
					case "teachers": echo "Teachers"; break;
					case "both": echo "Both Teachers and Students"; break;
					default: echo $data["metatype"]; break;
				}
			?></td>
			<td class="calign inputtype" value="<?=$data["inputtype"];?>"  style="width:141px;"><?php
				switch($data["inputtype"])
				{
					case "textarea": echo "Long Text"; break;
					case "text": echo "Short Text"; break;
					default: echo $data["inputtype"]; break;
				}
			?></td>
			<td class="calign" style="width:166px;">
				<div class="admin-bar">
					<span class="edit-buttons" style="display:none;">
					    <div class="admin-button ui-widget-header active save-changes" title="Save Infotype">
					        <?=StdLib::load_image("save.png","16px");?>
					    </div>
					    <div class="admin-button ui-widget-header active cancel-changes" title="Cancel Changes">
					        <?=StdLib::load_image("close_delete.png","16px");?>
					    </div>
				    </span>
				    
					<span class="main-buttons">
					    <div class="admin-button ui-widget-header active edit-infotype" title="Edit Infotype">
					        <?=StdLib::load_image("pencil_edit.png","16px");?>
					    </div>
					    <div class="admin-button ui-widget-header active delete-infotype" title="Delete this Infotype">
					        <?=StdLib::load_image("close_delete_2.png","16px");?>
					    </div>
					</span>
				</div>
			</td>
		</tr>
		
		<?php
		$contents = ob_get_contents();
		ob_end_clean();
		
		return print $contents;
	 }
     
     public function action_save_google_map()
	 {
    	$this->login_only();
	 	$place = new PlacesObj($_REQUEST["id"]);
		$place->load_metadata();
		$place->metadata->googlemap = $_REQUEST["googlemap"];
		if(!$place->metadata->save())
			return print $place->metadata->get_error();
			
		$place->date_modified = date("Y-m-d H:i:s");
		$place->save();
		
		return print 1;
	 }
     
     public function action_load_places_by_placetype()
     {
    	$this->login_only();
        $placetypeid = $_REQUEST["placetypeid"];
        $placetype = new PlaceTypesObj($placetypeid);
        $placetype = new PlaceTypesObj($placetype->parentid);
        $placesmanager = new Places;
        $places = $placesmanager->load_places($placetype->machinecode);
        
        ob_start();
        echo "<option value='0'></option>";
        foreach($places as $place):
            echo "<option value='".$place->placeid."'>".$place->placename."</option>";
        endforeach;
        $contents = ob_get_contents();
        ob_end_clean();
        
        return print $contents;
     }
    

    public function action_add_photo()
    {
    	$this->login_only();
        $picture = new PictureObj();
        $picture->placeid = urldecode($_REQUEST["placeid"]);
        $picture->path = urldecode($_REQUEST["path"]);
        $picture->picturename = urldecode($_REQUEST["picturename"]);
        $picture->description = urldecode($_REQUEST["description"]);
        $picture->caption = urldecode($_REQUEST["caption"]);
		$picture->type = str_replace(".","",substr($picture->path,-4,4));
        
        if(!$picture->save())
        {
            return print $picture->get_error();
        }
		$picture->make_thumb();
		
		$place = new PlacesObj($picture->placeid);
		$place->date_modified = date("Y-m-d H:i:s");
		$place->save();
		
        return print 1;
    }
    
	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
		$this->render('error', array("error"=>Yii::app()->errorHandler->error));
	}
	
	/**
	* Displays the login page
	*/
	public function actionLogin()
	{
		ini_set("display_errors",1);
		error_reporting(E_ALL);
		if(!Yii::app()->user->isGuest) Yii::app()->user->logout();
		$this->makeSSL();
		$model = new LoginForm;
		$redirect = (isset($_REQUEST["redirect"])) ? $_REQUEST["redirect"] : "index";
		$error = "";
		// collect user input data
		if (isset($_POST['username']) and isset($_POST["password"])) {
				$model->username = $_POST["username"];
				$model->password = $_POST["password"];
				// validate user input and redirect to the previous page if valid
				if ($model->validate() && $model->login())
				{
					$user = new UserObj($model->username);
					$user->login();
					$this->redirect($redirect);
				}
			else
				$error = "We could not find you. Either you typed in your username/password wrong or you do not exist. Please exist.";
		}
		// display the login form
		$this->render('login', array('model' => $model,"error"=>$error));
	}
	
	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}

	private function makeSSL()
	{
		if($_SERVER['SERVER_PORT'] != 443) {
			header("HTTP/1.1 301 Moved Permanently");
			header("Location: https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
			exit();
		}
	}

	private function makeNonSSL()
	{
		if($_SERVER['SERVER_PORT'] == 443) {
			header("HTTP/1.1 301 Moved Permanently");
			header("Location: http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
			exit();
		}
	}
	
	private function login_only()
	{
		if(Yii::app()->user->isGuest)
		{
			$this->redirect(Yii::app()->createUrl('login')."?redirect=".urlencode("http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]));
			exit;
		}
	}
}