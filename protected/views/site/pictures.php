<?php
$place = new PlacesObj($_REQUEST["id"]);
if(!$place->loaded)
{
    $this->redirect(Yii::app()->baseUrl);
    exit;
}
$images = $place->load_images();

$flashes = new Flashes();
$flashes->render();
?>
<style>
table tr td {
	vertical-align: top;
	text-align:left;
	font-size:14px;
	padding:3px;
	padding-left:13px;
	padding-right:13px;
}
table tr td.label {
	border-right:1px solid #ccc;
	width:35px;
}
ul#gallery {
	list-style:none;
	margin:0;
	padding:0;
}
ul#gallery li {
	margin-bottom:5px;
	margin-top:5px;
	padding:5px;
	border:1px solid #ddd;
	background-color:#f5f5f5;
}
label {
	font-weight:bold;
}
img.slideshow-img {
	display:none;
}
div.admin-bar {
    float:right;
    padding:0;
    margin-top:-25px;
}
div.admin-button {
    border:1px solid #69f;
    padding:3px;
    width:20px;
    border-radius:5px;
    display:inline-block;
    opacity:0.5;
}
.active {
    cursor:pointer;
}
.disabled {
    cursor:default;
}
.selected {
    cursor:pointer;
}
.editfield {
	margin:0px;
	letter-spacing:1px;
	padding:2px;
	font-size:13px;
	width:220px;
}
</style>
<!-- Load Queue widget CSS and jQuery -->
<style type="text/css">@import url(//<?php echo Yii::app()->params["LOCALAPP_SERVER"]; ?>/libraries/javascript/jquery/modules/plupload/js/jquery.plupload.queue/css/jquery.plupload.queue.css);</style>

<!-- Load plupload and all it's runtimes and finally the jQuery queue widget -->
<script type="text/javascript" src="//<?php echo Yii::app()->params["LOCALAPP_SERVER"]; ?>/libraries/javascript/jquery/modules/plupload/js/plupload.full.js"></script>
<script type="text/javascript" src="//<?php echo Yii::app()->params["LOCALAPP_SERVER"]; ?>/libraries/javascript/jquery/modules/plupload/js/jquery.plupload.queue/jquery.plupload.queue.js"></script>


<div style="float:right;width:400px;text-align:right;"><button id="return">Return to <?=$place->placename;?></button></div>
<h1>Manage Pictures</h1>

<div class="ui-widget-content" style="padding:6px;font-size:13px;margin-bottom:10px;">Manage pictures of the <?=$place->placename." ".$place->placetype->singular;?>.</div>

<div class="ui-state-error ui-corner-all <?=(isset($error))?"":"hide";?>" style="padding:6px;font-size:13px;margin-bottom:10px;"><span class="error"><?=@$error;?></span></div>

<center>
<div id="upload-container">
    <div style="padding-right:25px;padding-top:15px;padding-left:15px;text-align:left;">
        <div id="html5_uploader">You browser doesn't support native upload. Try Firefox 3 or Safari 4.</div>
    </div>
    <div style="width:300px;" id="photo-queue"></div>
</div>
</center>
<div id="photo-gallery">
    <ul id="gallery">
    	<?php foreach($images as $image): ?>
    	    <?php if(!$image->loaded or !$image->has_file()) continue; ?>
    		<li pictureid="<?=$image->pictureid;?>">
    			<table>
					<td class="calign mvalign mover" style="width:26px;cursor:move;">
						<?=StdLib::load_image("move","26px");?>
					</td>
	    			<td class="calign mvalign" style="width:190px;height:125px;"><?=$image->render_boxfit_y("200");?></td>
	    			<td>
						<table class="imglist">
							<tr>
								<td class="ralign label"><label>Title</label></td>
								<td class="lalign edit-field" fieldtype="picturename" style="padding-left:5px;height:25px;"><?=$image->picturename;?></td>
							</tr>
							<tr>
								<td class="ralign label"><label>Caption</label></td>
								<td class="lalign edit-field" fieldtype="caption" style="padding-left:5px;height:25px;"><?=$image->caption;?></td>
							</tr>
							<tr>
								<td class="ralign label"><label>Description</label></td>
								<td class="lalign edit-field" fieldtype="description" style="padding-left:5px;height:25px;"><?=$image->description;?></td>
							</tr>
						</table>
	    			</td>
    			</table>
				<?php if(isset(Yii::app()->user) and !Yii::app()->user->isGuest): ?>
				<div class="admin-bar">
					<span class="edit-buttons" style="display:none;">
					    <div class="admin-button ui-widget-header active save-changes" title="Save Picture">
					        <?=StdLib::load_image("Save","20px");?>
					    </div>
					    <div class="admin-button ui-widget-header active cancel-changes" title="Cancel Changes">
					        <?=StdLib::load_image("close_delete","20px");?>
					    </div>
				    </span>
				    
					<span class="main-buttons">
					    <div class="admin-button ui-widget-header active edit" title="Edit Picture">
					        <?=StdLib::load_image("pencil_edit","20px");?>
					    </div>
					    <div class="admin-button ui-widget-header active delete-picture" title="Delete Picture">
					        <?=StdLib::load_image("trash_box","20px");?>
					    </div>
				   </span>
				</div>
				<?php endif; ?>
    		</li>
    	<?php endforeach; ?>
    </ul>
</div>
<br class="clear" />

<link rel="stylesheet" href="http<?=(isset($_SERVER["HTTPS"]) and $_SERVER["HTTPS"]=="on")?"s":"";?>://assettdev.colorado.edu/libraries/javascript/jquery/modules/tiptip/tipTip.css" type="text/css" />
<script src="http<?=(isset($_SERVER["HTTPS"]) and $_SERVER["HTTPS"]=="on")?"s":"";?>://assettdev.colorado.edu/libraries/javascript/jquery/modules/tiptip/jquery.tipTip.js"></script>

<div id="saving-pictures" title="Saving pictures...">
     <center>
         Please don't navigate away while pictures are being saved.<br/>
         <br/>
         <?php echo StdLib::load_image("ajax-loader","16px"); ?> 
     </center>
</div>

<script>
var semaphore = 0;
var semaphore_interval;
function semaphoreCheck()
{
    /*
	if(semaphore<=0) window.location.reload();
	else console.log("Semaphore count: "+semaphore);
	*/
}
var uploader;
$(document).ready(function() {
	$("button").button();
	
	$("#return").click(function(){
		window.location = "<?=Yii::app()->createUrl('place');?>?id=<?=$place->placeid;?>";
		return false;
	});
	
	$("#saving-pictures").dialog({
	   "autoOpen":         false,
	   "width":            450,
	   "height":           140,
	   "modal":            true,
	   "draggable":        false,
	   "resizable":        false,
	});
	
    /****** UPLOADER *******/
    uploader = $('#html5_uploader').pluploadQueue({
       runtimes:        'html5',
       container:       'html5_uploader',
       url:             '<?php echo Yii::app()->createUrl('_upload_images'); ?>?placeid=<?php echo $place->placeid; ?>',
       max_file_size:   '20mb',
       chunk_size:      '1mb',
       unique_names:    false,
       browse_button:   'Select Images',
       filters:         [{
            title:          "Image Files",
            extensions:     "jpg,gif,png,jpeg,bmp"
       }]
    });
    
    $(".ui-dialog-titlebar button.ui-dialog-titlebar-close").hide();
    
    var uploader = $('#html5_uploader').pluploadQueue();
    uploader.bind('UploadProgress',function(){
        $("#saving-pictures").dialog("open");
        return false;
    });
    uploader.bind('UploadComplete', function(up,files){
        // $('form')[0].submit();
        files.forEach(function(element, index, array){
            if(element.status == 5) {
                semaphore = semaphore + 1;
                $.ajax({
                    url:        "<?php echo Yii::app()->createUrl('_add_uploaded_file'); ?>",
                    data:       "filename="+escape(element["name"])+"&size="+escape(element["size"])+"&placeid=<?php echo $place->placeid; ?>",
                    success:    function(){
                        semaphore = semaphore - 1;
                    },
                    error:      function(){
                        semaphore = semaphore - 1;
                    }
                });
            }
        });
        setInterval(function(){if(semaphore==0){ 
            // window.location.reload(); 
            }},1000);
    });
    
    $("div.admin-button.active").hover(
        function(){
            if($(this).is(".disabled")) return false;
            $(this).stop().fadeTo('fast',1);
        },
        function(){
            if($(this).is(".disabled")) return false;
            $(this).stop().fadeTo('fast',0.5);
        }
    );
    
    $("div.admin-button").tipTip({
        defaultPosition:    "top",
        delay:              150,
    });
    
    $(document).on("click","div.admin-button.save-changes",function(){
		var table = $(this).parent().parent().parent().find("table.imglist");
		var pictureid = $(this).parent().parent().parent().attr("pictureid");
		var picname = table.find("td[fieldtype=picturename] input").val();
		var caption = table.find("td[fieldtype=caption] input").val();
		var desc = table.find("td[fieldtype=description] input").val();
		
		$.ajax({
			"url":  		"<?=Yii::app()->createUrl('_change_picture_info');?>",
			"data": 		"pictureid="+pictureid+"&picturename="+escape(picname)+"&caption="+escape(caption)+"&description="+escape(desc),
			"success": 		function(data)
			{
				if(data!=1)
				{
                   $("span.error").html(data).parent().show('blind');
				}
			}
		});
		
		table.find("td[fieldtype=picturename]").html(picname);
		table.find("td[fieldtype=caption]").html(caption);
		table.find("td[fieldtype=description]").html(desc);
		
		$(this).parent().parent().find("span.edit-buttons").hide();
		$("ul#gallery").sortable("option","disabled",false);
		$("span.main-buttons").show();
    });
    
    $(document).on("click","div.admin-button.cancel-changes",function(){
		$(this).parent().parent().find("span.edit-buttons").hide();
		$("ul#gallery").sortable("option","disabled",false);
		$("span.main-buttons").show();
    	
    });
    
    $(document).on("click","div.admin-button.delete-picture",function(){
		var ret = confirm("Are you sure you wish to delete this picture?");
		var pictureid = $(this).parent().parent().parent().attr("pictureid");
		if(ret)
		{
			$.ajax({
				"url": 		"<?=Yii::app()->createUrl('_delete_picture');?>",
				"data": 	"pictureid="+pictureid,
				"success": 	function(data){
					if(data!=1)
					{
                       $("span.error").html(data).parent().show('blind');	
					}
				}
			});
			$(this).parent().parent().parent().hide("fade","slow",function(){$(this).remove();});
		}
    	return false;
    });
    
    $(document).on("click","div.admin-button.edit",function(){
    	
		$(this).parent().parent().find("span.edit-buttons").show();
		$("span.main-buttons").hide();
		$("ul#gallery").sortable("option","disabled",true);
		
		var table = $(this).parent().parent().parent().find("table.imglist");
		var picname = table.find("td[fieldtype=picturename]").html();
		var caption = table.find("td[fieldtype=caption]").html();
		var desc = table.find("td[fieldtype=description]").html();
		
		table.find("td[fieldtype=picturename]").html("<input type='text' name='picturename' value='"+picname+"' class='editfield' />");
		table.find("td[fieldtype=caption]").html("<input type='text' name='caption' value='"+caption+"' class='editfield' />");
		table.find("td[fieldtype=description]").html("<input type='text' name='description' value='"+desc+"' class='editfield' />");
    });
    
    $(document).on("div.admin-button.save","click",function(){
		$(this).parent().parent().find("span.edit-buttons").hide();
		$("ul#gallery").sortable("option","disabled",false);
		$("span.main-buttons").show();
    	
		var table = $(this).parent().parent().parent().find("table.imglist");
		var picname = table.find("td[fieldtype=picturename]").html();
		var caption = table.find("td[fieldtype=caption]").html();
		var desc = table.find("td[fieldtype=description]").html();
    });
    
    $(".mover").mousedown(function(){
    	$(this).parent().parent().css("border-color","#09f;");
    });
    
    $(".mover").mouseup(function(){
    	$(this).parent().parent().css("border-color","#ddd");
    });
    
    $("ul#gallery").sortable({
		helper: fixHelper,
		handle: ".mover",
		revert: true,
		placeholder: "ui-state-highlight",
		forcePlaceholderSize: true,
        update : function () { 
			$("ul#gallery li").each(function(index,value){
				var pictureid = $(value).attr('pictureid');
				$.ajax({
					"url": 		"<?=Yii::app()->createUrl('_reorder_picture');?>",
					"data": 	"pictureid="+pictureid+"&sorder="+index,
					"success":  function(data)
					{
						// Do nothing
					}
				});
			});
            // $("#info").load("process-sortable.php?"+order); 
        } 
	}).disableSelection();
});
var fixHelper = function(e, ui) {
	ui.children().each(function() {
		$(this).width($(this).width());
	});
	return ui;
};
</script>