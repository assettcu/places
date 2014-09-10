<?php

require_once "BaseController.php";

class BackendController extends BaseController
{
    /**
     * Displays the login page
     */
    public function actionLogin()
    {
        # Force log out
        if(!Yii::app()->user->isGuest) Yii::app()->user->logout();
        
        # Force SSL
        $this->makeSSL();
        
        # Initialize variables and Login model
        $params = array();
        $model = new LoginForm;
        $redirect = (isset($_REQUEST["redirect"])) ? $_REQUEST["redirect"] : Yii::app()->createUrl('site/index');
        $error = "";
        
        # Collect user input data
        if (isset($_POST['username']) and isset($_POST["password"])) {
            $model->username = $_POST["username"];
            $model->password = $_POST["password"];
            # Validate user input and redirect to the previous page if valid
            if ($model->validate() && $model->login()) {
                $user = new UserObj($model->username);
                Yii::app()->user->setFlash("success","Logged In! Welcome, ".$user->name."!");
                $this->redirect($redirect);
            } else {
                $errors = $model->getErrors();
                Flashes::create_flash("error",$errors);
            }
        }
        
        $params["model"] = $model;
        $params["error"] = $error;
        
        # Display the login form
        $this->render('login',$params);
    }

    public function actionIndex()
    {
        $this->noGuest();
        
        $this->render("index");
    }
    
    public function actionNew()
    {
        $this->noGuest();
        $error = "";
        
        try {
            $place = new PlacesObj();
            
            # If user submitted form...
            if(isset($_REQUEST["placeform-submitted"])) {
                
                # Set property values from form
                $place->placename       = $_POST["placename"];
                $place->placetypeid     = $_POST["placetypeid"];
                $place->parentid        = $_POST["parentid"];
                $place->description     = $_POST["description"];
                $place->tags            = $_POST["tags"];
                
                # Save post
                if(!$place->save()) {
                    $error = $place->error_field;
                    throw new Exception($property->get_error());
                }
                $place->load();
                $place->load_metadata();
                $place->metadata->save();
                
                # Save the image files
                $count = 0;
                
                $destdir = "/images/".$place->placetype->name."/".str_replace(" ","_",$place->placename)."/";
                $actualdir = "C:\\web\\places.colorado.edu/images/".$place->placetype->name."/".str_replace(" ","_",$place->placename)."/";
                
                if(!is_dir($actualdir)) {
                    @mkdir($actualdir);
                }
                
                $scanned_dirs = scandir($actualdir);
                
                # Find the highest numbered image and increment by one
                $counter = 0;
                foreach($scanned_dirs as $index=>$file) {
                    if($file == "." or $file == "..") {
                        continue;
                    }
                    if(preg_match("/\_[0-9]+\./",$file,$matches)) {
                        $counter = ($matches[0] > $counter) ? $matches[0] : $counter;
                    }
                }
                $counter++;
                
                while(isset($_POST["html5_uploader_".$count."_tmpname"])) {
                    
                    # Load up local vars
                    $tempfile = getcwd()."/temp/".Yii::app()->user->name."/".$_POST["html5_uploader_".$count."_tmpname"];
                    $type = substr($_POST["html5_uploader_".$count."_tmpname"],-3,3);
                    
                    $filename = str_replace(" ","_",$place->placename)."_".$counter.".".$type;
                    $destfile = $destdir.$filename;
                    $actualfile = $actualdir.str_replace(" ","_",$place->placename)."_".$counter.".".$type;
                    
                    $counter++;
                
                    # Move the temp file into the proper folder
                    if(is_file($tempfile)) {
                        if(!is_dir($actualdir)) {
                            @mkdir($actualdir);
                        }
                        copy($tempfile,$actualfile);
                        unlink($tempfile);
                    }
                    # If image doesn't exist then continue past this picture
                    else {
                        $count++;
                        continue;
                    }
                    
                    # Create picture object to insert into the database
                    $picture = new PictureObj();
                    $picture->picturename = $place->placename;
                    $picture->placeid = $place->placeid;
                    $picture->path = $destfile;
                    $picture->sorder = $count;
                    $picture->caption = $place->placename;
                    $picture->description = "";
                    $picture->type = $type;
                    $picture->who_uploaded = Yii::app()->user->name;
                    $picture->date_uploaded = date("Y-m-d H:i:s");
                    
                    if(!$picture->save()) {
                        throw new Exception("Error saving picture: ".$picture->get_error());
                    }
                    $count++;
                }
                    
                # If no errors occured then run cron, set flash, and redirect home
                if(!Yii::app()->user->hasFlash("error")) {
                    # Run Cron which will update every user's watchlists
                    # Cron::run_cron();                   
                    # Success message and redirect!                   
                    Yii::app()->user->setFlash("success","Successfully added place.");
                    $this->redirect(Yii::app()->createUrl('backend/editplace')."?id=".$place->placename);
                    exit;
                }
            }
        }
        catch(Exception $e) {
            Yii::app()->user->setFlash("error",$e->getMessage());
        }
        
        $params["error"] = $error;
        $params["place"] = $place;
        
        $this->render('new',$params);
    }
    
    public function actionManagePlaces()
    {
        $this->noGuest();
        
        $this->render("manageplaces");
    }
    
    public function actionEditPlace()
    {
        $this->noGuest();
        
        if(!isset($_REQUEST["id"])) {
            Flashes::create_flash("warning","For some reason, a place was not chosen to be edited. An ID must be passed in.");
            $this->redirect("manageplaces");
            exit;
        }
        
        $place = new PlacesObj($_REQUEST['id']);
        if(!$place->loaded) {
            Flashes::create_flash("warning","This place does not exist.");
            $this->redirect(Yii::app()->createUrl('backend/manageplaces'));
            exit;
        }
        $place->load_images();
        $place->load_parent();
        $place->load_metadata();
        $params["place"] = $place;
        
        if(isset($_REQUEST["editplace-form"])) {
            
            $oldplacename = $place->placename;
            
            $place->placename = $_REQUEST["placename"];
            $place->description = $_REQUEST["description"];
            $place->tags = $_REQUEST["tags"];
            
            foreach($place->metadata->data as $index=>$values) {
                if(isset($_REQUEST[$index])) {
                    $place->metadata->$index = $_REQUEST[$index];
                }
            }
            
            if(!$place->save()) {
                Flashes::create_flash("error","Error: Could not save place.<br/>".$place->get_error());
            }
            else {
                Flashes::create_flash("success","Successfully saved place.");
            }
        
            # Save the image files
            $count = 0;
            
            # Define destination and actual directories
            $destdir = "/images/".$place->placetype->name."/".str_replace(" ","_",$place->placename)."/";
            $actualdir = "C:\\web\\places.colorado.edu/images/".$place->placetype->name."/".str_replace(" ","_",$place->placename)."/";
                
            
            if($oldplacename != $_POST["placename"] or (is_dir("C:\\web\\places.colorado.edu/images/".$place->placetype->name."/".str_replace(" ","_",$oldplacename))) and !is_dir($actualdir)) {
                $imagecopy = false;
                @rmdir($actualdir);
                @rename("C:\\web\\places.colorado.edu/images/".$place->placetype->name."/".str_replace(" ","_",$oldplacename),$actualdir);
                
                $images = $place->load_images();
                foreach($images as $image) {
                    $image->path = preg_replace("/\/".str_replace(" ","_",$oldplacename)."\//","/".$place->placename."/",$image->path);
                    $image->save();
                }
            }
            else if(!is_dir($actualdir)) {
                @mkdir($actualdir);
            }
            
            # Find the highest numbered image and increment by one
            $counter = 0;
            $scanned_dirs = scandir($actualdir);
            foreach($scanned_dirs as $index=>$file) {
                if($file == "." or $file == "..") {
                    continue;
                }
                if(preg_match("/(?<=\_)[0-9]+(?=\.)/",$file,$matches)) {
                    $counter = ($matches[0] > $counter) ? $matches[0] : $counter;
                }
            }
            
            while(isset($_POST["html5_uploader_".$count."_tmpname"])) {
                
                # Load up local vars
                $tempfile = getcwd()."/temp/".Yii::app()->user->name."/".$_POST["html5_uploader_".$count."_tmpname"];
                $type = substr($_POST["html5_uploader_".$count."_tmpname"],-3,3);
                
                # Increment the counter
                $counter++;
                
                $filename = str_replace(" ","_",$place->placename)."_".$counter.".".$type;
                $destfile = $destdir.$filename;
                $actualfile = $actualdir.str_replace(" ","_",$place->placename)."_".$counter.".".$type;
                
                /**
                $list = new WidgetList();
                $list->addListItem(
                    array(
                        "Temp File Exists" => (is_file($tempfile)) ? "TRUE" : "FALSE",
                        "Temp File"     => $tempfile,
                        "Dest Dir"      => $destdir,
                        "Dest File"     => $destfile,
                        "Actual Dir"    => $actualdir,
                        "Actual File"   => $actualfile
                    )
                );
                
                $list->render();
                die();
                **/
                
                # Move the temp file into the proper folder
                if(is_file($tempfile)) {
                    if(!is_dir($actualdir)) {
                        @mkdir($actualdir);
                    }
                    copy($tempfile,$actualfile);
                    unlink($tempfile);
                }
                # If image doesn't exist then continue past this picture
                else {
                    $count++;
                    continue;
                }
                
                # Create picture object to insert into the database
                $picture = new PictureObj();
                $picture->picturename = $place->placename;
                $picture->placeid = $place->placeid;
                $picture->path = $destfile;
                $picture->sorder = $count;
                $picture->caption = $place->placename;
                $picture->description = "";
                $picture->type = $type;
                $picture->who_uploaded = Yii::app()->user->name;
                $picture->date_uploaded = date("Y-m-d H:i:s");
                
                if(!$picture->save()) {
                    throw new Exception("Error saving picture: ".$picture->get_error());
                }
                $count++;
            }
            $this->redirect(Yii::app()->createUrl('backend/editplace')."?id=".$place->placename);
            exit;
        }

        $place->load_images();
        $place->load_metadata();
        $params["place"] = $place;
        
        $this->render("editplace",$params);
    }
    
    /**
     * Checks to see if a user is logged into the application.
     * If not then it will redirect to the login page with a warning.
     */
    protected function noGuest()
    {
        if(Yii::app()->user->isGuest) {
            Yii::app()->user->setFlash("warning","You must be signed in to access this page.");
            $this->redirect(Yii::app()->createUrl('backend/login')."?redirect=".urlencode("https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']));
            exit;
        }
    }
}
    