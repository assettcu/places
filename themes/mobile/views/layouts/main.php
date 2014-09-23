<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<title><?php echo CHtml::encode($this->pageTitle); ?></title>

    <!-- <link rel="stylesheet" href="//code.jquery.com/mobile/1.4.4/jquery.mobile-1.4.4.min.css" /> -->
    <link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jquerymobile/1.4.3/jquery.mobile.min.css" />
    <link rel="stylesheet" href="<?php echo Yii::app()->baseUrl; ?>/library/fonts/icomoon/style.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/mobile.css" media="screen, projection" />
    <link rel="stylesheet" href="<?php echo WEB_LIBRARY_PATH; ?>/jquery/modules/photoswipe/1.0.11/photoswipe.css" />
    <link rel="stylesheet" href="<?php echo WEB_LIBRARY_PATH; ?>/jquery/modules/responsiveslides/responsiveslides.css">
    <link rel="stylesheet" href="<?php echo WEB_LIBRARY_PATH; ?>/jquery/modules/responsiveslides/demo/demo.css">
</head>
<body>
    <div class="container" id="page">
        <?php echo $content; ?>
    </div>

    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jquerymobile/1.4.3/jquery.mobile.min.js"></script>

    <!-- <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
    <script src="//code.jquery.com/mobile/1.4.4/jquery.mobile-1.4.4.min.js"></script> -->
    <script type="text/javascript" src="<?php echo WEB_LIBRARY_PATH; ?>/jquery/modules/map/ui/min/jquery.ui.map.min.js"></script>
    <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script> 

    <script type="text/javascript" src="<?php echo WEB_LIBRARY_PATH; ?>/jquery/modules/photoswipe/1.0.11/lib/simple-inheritance.min.js"></script>
    <script type="text/javascript" src="<?php echo WEB_LIBRARY_PATH; ?>/jquery/modules/photoswipe/1.0.11/code-photoswipe-jQuery-1.0.11.min.js"></script>

    <script src="<?php echo WEB_LIBRARY_PATH; ?>/jquery/modules/responsiveslides/responsiveslides.min.js"></script>
    <script type="text/javascript">
        jQuery(document).on("pagechange",function(event,data){
            var page = data.toPage[0].id;

            if(page == "map") {
                jQuery("#map_canvas").css({'height':$(window).height()-170});
                jQuery('#map_canvas').gmap('refresh');
            }
            else if(page == "images") {
                jQuery("#slider2").css({'height':$(window).height()-170});
            }
        });

        jQuery(document).on("pagebeforechange",function(event,data){
            if(typeof(data.toPage[0]) != "undefined") {
                $('.place-navbar ul li a').removeClass("ui-btn-active ui-btn:active");
                $('#place-navbar-' + data.toPage[0].id + ' a').addClass("ui-btn-active");
            }
        });
        
        jQuery(document).on("pageinit","#images",function(event){
            // Slideshow 2
            jQuery("#slider2").responsiveSlides({
                auto: false,
                pager: true,
                nav:true,
                speed: 300,
                maxwidth: $(window).width(),
                namespace: "centered-btns",
                pagination: [{
                    position: 'T_C',
                }]
            });
            jQuery("#slider2 img").css({'height':$(window).height()-170});
        });
        
        $(function(){
            if($('#map_canvas').length != 0) {
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
                            $('#map_canvas').gmap('openInfoWindow', { 'content': marker.content }, this);
                        });
                    });
                });
            }
        });
        
        $(document).on("pagecreate","#classes",function(){
            $("#yt-select").on('change',function(){
                $("#courses-listview").html("<div style='text-align: center;'>Loading term " + $("#yt-select option:selected").text() + "</div>");
                $.ajax({
                   "url":   "<?php echo Yii::app()->baseUrl; ?>/ajax/loadclasses/id/<?php echo @$_REQUEST["id"]; ?>/yt/"+$("#yt-select").val(),
                   "success": function(data) {
                       $("#courses-listview").html(data).parent().enhanceWithin().refresh();
                   } 
                });
            });
        });
    </script>
</body>
</html>