<?php
// Theme name from Jquery UI themes
$theme = "cupertino";
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

	<script src="//compass.colorado.edu/libraries/javascript/jquery/modules/cookie/jquery.cookie.js" type="text/javascript"></script>
	<link rel="stylesheet" href="//compass.colorado.edu/libraries/javascript/jquery/jquery-ui/themes/<?=$theme?>/jquery-ui.css" type="text/css" />
	
	<link rel="stylesheet" href="//compass.colorado.edu/libraries/javascript/jquery/modules/tiptip/tipTip.css" type="text/css" />
	<script src="//compass.colorado.edu/libraries/javascript/jquery/modules/tiptip/jquery.tipTip.js"></script>
	

	<script>
		jQuery(document).ready(function($){
			$("#search-submit").button();
			$("#advanced-search").tipTip();
			$("#search-submit").click(function(){
				if($("#q").val().trim()=="") return false;
				else return true;
			});
			
		});
	</script>
</head>

<body>
<div id="colorado-header" style="width:100%;height:120px;z-index:1000;border-bottom:2px solid #ccc;">
	<a href="http://www.colorado.edu">
		<div id="page-header" class="cu-header" style="background-color:#d9d9d9;height:0px;"></div>
	</a>
</div>
<br/>
<div class="container" id="page">

	<div id="header">
		<a href="<?=Yii::app()->createUrl('index');?>">
			<div class="places-logo"></div>
		</a>
        
	
       <div id="cu-search">
			<div id="cu-links">
				CU:
				<a href="http://www.colorado.edu/">Home</a>
				•
				<a href="http://www.colorado.edu/atoz">A to Z</a>
				•
				<a href="http://www.colorado.edu/campusmap">Campus Map</a>
			</div>
			<div id="cu-search-form-wrapper" class="clearfix">
				<form id="cu-search-form" action="http://www.colorado.edu/search/custom/searchdirect.cgi">
					<div id="cu-search-input-wrapper">
						<label for="cu-search-input">Enter search query</label>
						<input id="cu-search-input" type="text" onfocus="this.value=''" value="Search CU-Boulder" name="searchstring">
						<label for="cu-search-submit">Submit search</label>
						<input id="cu-search-submit" type="submit" style="background-image: url('images/submit.png')" value="Search">
					</div>
				</form>
			</div>
		</div>

	</div>
	<br class="clear" />

	<?php echo $content; ?>

	<div class="clear"></div>

    
	<div id="footer">
		<a href="http://assett.colorado.edu"><div class="whtassett-logo"></div></a><br/>
		<a href="http://colorado.edu">University of Colorado Boulder</a><br/>
		<a href="http://www.colorado.edu/legal-trademarks-0">Legal &amp; Trademark </a> | <a href="http://www.colorado.edu/policies/privacy-statement">Privacy</a><br/>
		<a href="https://www.cu.edu/regents/">&copy; <?php echo date('Y'); ?> Regents of the University of Colorado</a><br/>
		<p style=" display:block; text-align:right;margin-top:-40px;"> 
			Application Designer <a href="http://assett.colorado.edu/contact-us/web-team#ryan">Ryan Carney-Mogan</a><br/>
			Developed by the <a href="http://assett.colorado.edu">ASSETT program</a><br/>
			<?php if(Yii::app()->user->isGuest): ?>
			[ <a href="<?=Yii::app()->createUrl('login');?>" style="text-decoration:none;">login</a> ]
			<?php else: ?>
			Welcome, <span class="blue"><?=Yii::app()->user->name;?></span>! | [ <a href="<?=Yii::app()->createUrl('logout');?>">logout</a> ]
			<?php endif; ?>
		</p>        
	</div><!-- footer -->

</div><!-- page -->

</body>
</html>
