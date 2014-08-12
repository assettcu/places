<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/mobile.css" media="screen, projection" />
	
    <link rel="stylesheet" href="//code.jquery.com/mobile/1.4.2/jquery.mobile-1.4.2.min.css" />
    <script src="//code.jquery.com/jquery-1.10.2.min.js"></script>
    <script src="//code.jquery.com/mobile/1.4.2/jquery.mobile-1.4.2.min.js"></script>
    <link rel="stylesheet" href="<?php echo Yii::app()->baseUrl; ?>/library/fonts/icomoon/style.css" />

	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
	
    
    <link rel="stylesheet" href="<?php echo WEB_LIBRARY_PATH; ?>/jquery/modules/photoswipe/1.0.11/photoswipe.css" />
    <script type="text/javascript" src="<?php echo WEB_LIBRARY_PATH; ?>/jquery/modules/photoswipe/1.0.11/lib/simple-inheritance.min.js"></script>
    <script type="text/javascript" src="<?php echo WEB_LIBRARY_PATH; ?>/jquery/modules/photoswipe/1.0.11/code-photoswipe-jQuery-1.0.11.min.js"></script>
    <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script> 
    <script type="text/javascript" src="<?php echo WEB_LIBRARY_PATH; ?>/jquery/modules/map/ui/min/jquery.ui.map.min.js"></script>
    
    <link rel="stylesheet" href="<?php echo WEB_LIBRARY_PATH; ?>/jquery/modules/responsiveslides/responsiveslides.css">
    <script src="<?php echo WEB_LIBRARY_PATH; ?>/jquery/modules/responsiveslides/responsiveslides.min.js"></script>
    
    <script type="text/javascript">
        jQuery(document).on("pagecreate",function(event){
          
            jQuery("#map_canvas").css({'height':$(window).height()-170});
            if(window.orientation == 0) { 
                jQuery("span.nav-text").hide();
            }
            else {
                jQuery("span.nav-text").show();
            } 
            jQuery(window).on("orientationchange",function(){
                
                jQuery("#map_canvas").css({'height':$(window).height()-100});
                if(window.orientation == 0)
                { 
                    jQuery("span.nav-text").hide();
                }
                else {
                    jQuery("span.nav-text").show();
                } 
            });
            
        });
        $(document).on("pagecreate","#classes",function(){
            $("#yt").on('change',function(){
                $.ajax({
                   "url":   "<?php echo Yii::app()->baseUrl; ?>/ajax/loadclasses/id/<?php echo @$_REQUEST["id"]; ?>/yt/"+$("#yt").val(),
                   "success": function(data) {
                       $("#myTable tbody").html(data).parent().enhanceWithin().refresh();
                   } 
                });
            });
        });
        $(function(){
            $('#map_canvas').gmap().bind('init', function() { 
                // This URL won't work on your localhost, so you need to change it
                // see http://en.wikipedia.org/wiki/Same_origin_policy
                $.getJSON( '<?php echo Yii::app()->baseUrl; ?>/api/placemap?id=<?php echo @$_REQUEST["id"]; ?>', function(data) { 
                    $.each( data.markers, function(i, marker) {
                        $('#map_canvas').gmap('addMarker', { 
                            'position': new google.maps.LatLng(marker.latitude, marker.longitude), 
                            'bounds': true,
                        }).click(function() {
                            $('#map_canvas').gmap('openInfoWindow', { 'content': marker.content }, this);
                        });
                        $('#map_canvas').gmap('option','zoom',18);
                    });
                });
            });
        });
    </script>
</head>

<body>

<div class="container" id="page">

	<?php echo $content; ?>
</div><!-- page -->

</body>
</html>
