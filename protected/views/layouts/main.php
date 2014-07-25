<?php
// Theme name from Jquery UI themes
$theme = "base";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />

	<!-- blueprint CSS framework -->
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/screen.css" media="screen, projection" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/print.css" media="print" />
	<!--[if lt IE 8]>
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/ie.css" media="screen, projection" />
	<![endif]-->

	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/form.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/buttons.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/images.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/table.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/breadcrumb.css" />
    
    <link rel="shortcut icon" href="<?php echo Yii::app()->request->baseUrl; ?>/library/images/favicon.ico" />
    <link rel="stylesheet" href="<?php echo Yii::app()->baseUrl; ?>/library/fonts/icomoon/style.css" />
    
	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
	
	<script type="text/javascript">

	  var _gaq = _gaq || [];
	  _gaq.push(['_setAccount', 'UA-7054410-2']);
	  _gaq.push(['_trackPageview']);
	
	  (function() {
	    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
	    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	  })();
	
	</script>
	
    <script src="//ajax.googleapis.com/ajax/libs/jquery/<?php echo  Yii::app()->params["LOCALAPP_JQUERY_VER"]; ?>/jquery.min.js" type="text/javascript"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jqueryui/<?php echo Yii::app()->params["LOCALAPP_JQUERYUI_VER"]; ?>/jquery-ui.min.js" type="text/javascript"></script>

	<script src="<?php echo WEB_LIBRARY_PATH; ?>/jquery/modules/cookie/jquery.cookie.js" type="text/javascript"></script>
    <script src="<?php echo WEB_LIBRARY_PATH; ?>/jquery/modules/sticky/jquery.sticky.js" type="text/javascript"></script>
	<link rel="stylesheet" href="<?php echo WEB_LIBRARY_PATH; ?>/jquery/themes/<?=$theme?>/jquery-ui.css" type="text/css" />
	
	<link rel="stylesheet" href="//compass.colorado.edu/libraries/javascript/jquery/modules/tiptip/tipTip.css" type="text/css" />
	<script src="//compass.colorado.edu/libraries/javascript/jquery/modules/tiptip/jquery.tipTip.js"></script>
	

	<script>
		jQuery(document).ready(function($){
			$("#search").click(function(){
			    $("form#search-form").submit();
			    return true;
			});
		});
	</script>
</head>

<body>

<div class="container" id="page">
	<div id="header">
	    <div class="logo">
		    <a href="<?=Yii::app()->createUrl('index');?>">
                <img src="<?php echo StdLib::load_image_source("university_of_colorado_places_white"); ?>" alt="University of Colorado - Places" />
            </a>
		</div>
		<?php if(Yii::app()->controller->action->id == "place"): ?>
		<div class="search sticky" sticky="150">
		<?php else: ?>
        <div class="search">
		<?php endif; ?>
		    <form method="get" action="search" id="searchform">
		        <label for="searchbar" class="hide">Search Places</label>
    		    <input type="text" id="searchbar" name="q" value="<?php echo @$_REQUEST["q"]; ?>" />
    		    <a href="#" onclick="javascript:document.forms[0].submit();" id="search" class="button grey">Search</a>
    		    <div id="search-hint" style="text-align:left;"><span class="icon icon-info"> </span>Search for buildings, classrooms, or classes. </div>
		    </form>
		</div>
	</div>
	<br class="clear" />

	<?php echo $content; ?>

	<div class="clear"></div>

</div><!-- page -->

    <div id="footer">
        <div class="left-footer">Developed by <a href="http//assett.colorado.edu">ASSETT</a>.</div>
        <div class="right-footer">Copyright &copy; <?php echo date("Y"); ?></div>
        <div class="mid-footer">All Rights Reserved <span class="footer-divide">|</span> <div class="footer-culink"><a href="http//colorado.edu">University of Colorado Boulder</a></div></div>
    </div><!-- footer -->

</body>
</html>
