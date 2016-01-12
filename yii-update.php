<?php
# This may take a while depending on the system
set_time_limit(500);

# Define some directory/file variables
$frameworkdir   = dirname(__FILE__).'/framework/';
$frameworkzip   = $frameworkdir.'yii.zip';
$installed      = is_file($frameworkdir."yii-master/framework/yiic.php");
$error_msg      = "";

# Make the directory if it doesn't exist
if(!is_dir($frameworkdir)) {
    mkdir($frameworkdir);
}

# Someone pressed the install/upgrade button
if(isset($_REQUEST["upgrade"])) {

    # function to extract the framework from the Yii zip file
    function extractZip($zipfile,$frameworkdir)
    {
        global $frameworkzip, $frameworkdir;
        
        # If we have the yii framework zip file lets unpack it!
        if(file_exists($zipfile)) {
            
            $zip = new ZipArchive;
            $files = array();
            
            # Open the zip file for extraction
            if($zip->open($zipfile) === TRUE) {
                # Let's iterate through the files and only pull out the ones in the "framework" directory
                # There are other folders which are not used by the system, such as "demos" and "requirements"
                for($i = 0; $i < $zip->numFiles; $i++) {
                    $entry = $zip->getNameIndex($i);
                    if (
                        (strcmp(substr($entry, 0, strlen("yii-master/requirements/")),"yii-master/requirements/"))==0 or 
                        (strcmp(substr($entry, 0, strlen("yii-master/framework/")),"yii-master/framework/")==0)
                    ) {
                        $files[] = $entry;
                    }
                }
                
                # Feed $files array to extractTo() to get only the files we want
                $success = $zip->extractTo($frameworkdir, $files);
                $zip->close();
                
                # Return the status of the extraction (true or false)
                return $success;
            }
        }
        # If we got to here, something went wrong so return false
        return false;
    }
    
    # Encapsulate upgrade in a function call
    function upgradeYii() 
    {
        global $frameworkzip, $frameworkdir;
        
        # Not all Apache instances use .htaccess
        $htaccess = dirname(__FILE__)."/.htaccess";
        $contents = file_get_contents($htaccess);
        $apploc = str_replace("yii-update.php","",$_SERVER["SCRIPT_NAME"]);
        
        # If maintenance mode exists in .htaccess then don't re-add
        if(!stristr($contents,"#maintenance-mode")) {
            $contents .= "\n\n";
            $contents .= "#maintenance-mode\n";
            $contents .= "Redirect 301 ".$apploc."index.php ".$apploc."maintenance.php";
            file_put_contents($htaccess,$contents);
        }
    
        # Check conditions for downloading new Yii framework zip
        if(!file_exists($frameworkzip) or isset($_REQUEST["force-download"]) or (file_exists($frameworkzip) and filemtime($frameworkzip) < strtotime("-1 day"))) {
            # Re-download latest Yii version
            unlink($frameworkzip);
            file_put_contents($frameworkzip, fopen("http://github.com/yiisoft/yii/archive/master.zip","r"));
        }
        
        if(extractZip($frameworkzip,$frameworkdir)) {
            $contents = substr($contents,0,strpos($contents,"\n\n#maintenance-mode"));
            file_put_contents($htaccess,$contents);
            return true;
        }
        
        # If we get here then we weren't supposed to
        return false;
    }

    # Perform the actual function call
    $status = upgradeYii();
    
    # Check status and redirect
    if($status === TRUE and $installed === TRUE) {
        header("Location: index.php?upgrade=true");
        exit;
    }
    else if($status === TRUE and $installed === FALSE) {
        header("Location: install?yii=installed");
        exit;
    }
    else {
        # Error message for not completing an install/upgrade
        $error_msg = "Could not upgrade Yii. Please check the code or contact an administrator.";
    }
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="language" content="en" />

    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js" type="text/javascript"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js" type="text/javascript"></script>

    <!-- blueprint CSS framework --><!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">

    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="/places/library/bootstrap/css/docs.min.css" />

    <title>Yii Framework <?php echo ($installed===TRUE) ? "Upgrade" : "Install"; ?></title>

</head>

<body>
<div class="container" id="page">

    <div id="content">
        <?php if(isset($error_msg) and !empty($exrror_msg)) : ?>
        <p class="bg-danger">
            <div class='message-icon'><img src="library/images/flag_mark_yellow.png" width="16px" height="16px" /></div>
            <?php echo $error_msg; ?>
        </p>
        <?php endif; ?>
        <h1><?php echo ($installed===TRUE) ? "Upgrade" : "Install"; ?> Yii Framework</h1>
        <div style="padding:5px;font-size:15px;">
            <?php if($installed == FALSE): ?>
            Welcome! You first need to install a Yii framework before using this application.
            <?php else: ?>
            A Yii framework installation was found. Click the "Upgrade Yii Framework" button below to upgrade to the latest Yii version.
            <?php endif; ?>
            Check out the <a href="/places/framework/yii-master/requirements/index.php">Requirements Page</a>
        </div>


        <?php if(file_exists($frameworkzip) and filemtime($frameworkzip) > strtotime("-1 day")): ?>
            <div class="bs-callout bs-callout-info">
                <h4>You have already downloaded the Yii framework today.</h4>
                <p>If you wish to force download then check the "Force Download Yii Framework" checkbox.
                    Otherwise the last downloaded framework will be used in the install/upgrade.<br/><br/>
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="force-download" id="force-download" /> Force Download Yii Framework
                        </label>
                    </div>
                </p>
            </div>
            <script>
                jQuery(document).ready(function($){
                    $("button").attr("disabled","disabled");
                    $("#force-download").change(function(){
                        if($(this).is(":checked")) {
                            $("button").removeAttr("disabled");
                        }
                        else {
                            $("button").attr("disabled","disabled");
                        }
                    });
                });
            </script>
        <?php endif; ?>
        <button class="btn btn-default" type="submit"><?php echo ($installed===FALSE) ? "Install" : "Upgrade"; ?> Yii Framework &gt;</button>
        <script>
        jQuery(document).ready(function($){
            $("button").click(function(){
                $(this).blur();
                $(this).removeClass("ui-state-hover");
                $(this).addClass("disabled");
                $(this).find("span").text("<?php echo ($installed===FALSE) ? "Installing" : "Upgrading"; ?> ...");
                window.location = "yii-update.php?upgrade=go";
                return false;
            });
        });
        </script>
    </div>

    <hr />
    <div id="footer">
        <a id="assettlogo" href="http://assett.colorado.edu"></a>
        <div style="padding-top:10px;">
            Copyright &copy; <?php echo date('Y'); ?> by the University of Colorado Boulder.<br/>
            Developed by the <a href="http://assett.colorado.edu">ASSETT program</a><br/>
        </div>
    </div><!-- footer -->

</div><!-- page -->

</body>
</html>