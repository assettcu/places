<?php $this->pageTitle=Yii::app()->name; ?>
<?php
$type = @$_REQUEST["type"];
if($type == "") $type="building";

$organizer = new Places();
$places = $organizer->load_places($type);

<<<<<<< HEAD
?>
=======
$table_width = 1130;
$cols = 5;
$colsize = $table_width / $cols - 10;
$count=0;

$marker = StdLib::load_image_source("marker.png");
$imager = new Imager($marker);
$imager->width = "36px";
$imager->height = "36px";
$imager->styles["float"] = "left";
$imager->styles["margin-top"] = "-6px";
$imager->styles["margin-right"] = "6px";

?>
<style>
div.admin-bar {
	text-align:right;
	width:100%;
	padding:0;
	margin:0;
}
div.admin-button {
	border:1px solid #69f;
	padding:3px;
	width:20px;
	border-radius:5px;
	display:inline-block;
}
div.spacer {
	display:inline-block;
	padding:3px;
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
</style>
<div class="breadcrumb-bar ui-widget-header" style="font-size:13px;font-weight:normal;">
  &gt; <a href="<?=Yii::app()->baseUrl;?>">Buildings</a>
</div>
>>>>>>> bf5750938e5a9043950a9246ab69b3afbc9ad332

<?php if(isset(Yii::app()->user) and !Yii::app()->user->isGuest): ?>
<div class="admin-bar">
	<div class="admin-button ui-widget-header active add-building" title="Add New Building">
<<<<<<< HEAD
		<?=StdLib::load_image("plus","20px");?>
	</div>
	<div class="admin-button ui-widget-header active" title="Download Entire Album">
		<?=StdLib::load_image("arrow_down","20px");?>
	</div>
	<div class="admin-button ui-widget-header active reorder" title="Reorder Buildings">
		<?=StdLib::load_image("wizard","20px");?>
=======
		<?=StdLib::load_image("plus.png","20px");?>
	</div>
	<div class="admin-button ui-widget-header active" title="Download Entire Album">
		<?=StdLib::load_image("arrow_down.png","20px");?>
	</div>
	<div class="admin-button ui-widget-header active reorder" title="Reorder Buildings">
		<?=StdLib::load_image("wizard.png","20px");?>
>>>>>>> bf5750938e5a9043950a9246ab69b3afbc9ad332
	</div>
	<div class="spacer">
		
	</div>
	<div class="admin-button ui-widget-header active configure" title="Configure System">
<<<<<<< HEAD
		<?=StdLib::load_image("options_2","20px");?>
=======
		<?=StdLib::load_image("options_2.png","20px");?>
>>>>>>> bf5750938e5a9043950a9246ab69b3afbc9ad332
	</div>
</div>
<?php endif; ?>

<<<<<<< HEAD
<ul class="rig columns-4">
    <?php foreach($places as $place):
              $image = $place->load_first_image();
              if(!$image->loaded)
                $image = new PictureObj(1);
                $thumb = $image->get_thumb();
    ?>
    <li>
        <a href="<?php echo Yii::app()->createUrl('place'); ?>?id=<?php echo $place->placeid; ?>">
            <div class="image-container">
                <img src="<?php echo $thumb; ?>" width="100%" height="100%" />
            </div>
            <h3><?php echo $place->placename; ?></h3>
            <?php if(isset($place->description) and !empty($place->description)): ?>
            <p><?php echo $place->description; ?></p>
            <?php endif; ?>
        </a>
    </li>
    <?php endforeach; ?>
</ul>

<script>
jQuery(document).ready(function() {
   $("ul.rig li").hover(
       function(){
           $(this).fadeTo("fast",1);
       },
       function(){
           $(this).fadeTo("fast",0.8);
       }
   );
=======
<table class="imgtable">
	<tbody>
	<?php foreach($places as $place): ?>
		<?php 
		  $image = $place->load_first_image();
          if(!$image->loaded)
            $image = new PictureObj(1);
		  	$thumb = $image->get_thumb();
        ?>
		<?php if($count%$cols==0): ?>
		<tr>
		<?php endif; ?>
			<td class="img-holder" width="<?=100/$cols;?>%" height="150px" name="<?=$place->placename;?>" placeid="<?=$place->placeid;?>" style="padding:10px;border-radius:5px;max-height:150px;">
				<a href="<?=Yii::app()->createUrl('place');?>?id=<?=$place->placeid;?>" style="position:relative;margin:0;padding:0;width:100%;height:100%;">
                	<div class="img-container" style="position:relative;margin:0;padding:0;width:100%;height:100%;">
    					<img src="<?=$thumb;?>" width="100%" height="100%" />
    					<div class="image"><?=$place->placename;?></div>
    				</div>
    				<div class="cell-overlay hide" style="position:absolute;top:0;left:0;width:100%;height:100%;background-color:#ccc;opacity: .5;z-index:1000;"></div>
				</a>
			</td>
			<?php if(count($places)==$count+1 and count($places)<$cols): ?>
			<?php for($count;$count<$cols-1;$count++): ?>
				<td class="empty ui-state-disabled" width="<?=100/$cols;?>%" style="padding:10px;">&nbsp;</td>
			<?php endfor; ?>
			<?php endif; ?>
		<?php if($count%$cols==$cols-1): ?>
		</tr>
		<?php endif; ?>
	<?php $count++; endforeach; ?>
	</tbody>
</table>

<link rel="stylesheet" href="http<?=(isset($_SERVER["HTTPS"]) and $_SERVER["HTTPS"]=="on")?"s":"";?>://assettdev.colorado.edu/libraries/javascript/jquery/modules/tiptip/tipTip.css" type="text/css" />
<script src="http<?=(isset($_SERVER["HTTPS"]) and $_SERVER["HTTPS"]=="on")?"s":"";?>://assettdev.colorado.edu/libraries/javascript/jquery/modules/tiptip/jquery.tipTip.js"></script>
<script>
jQuery(document).ready(function($){
	$(".img-container").hover(
		function(){
			$(this).find("img").stop().css("opacity",1);
		},
		function(){
			$(this).find("img").stop().css("opacity",0.5);
		}
	);
	
	$(".img-container").click(function(){
		var loc = $(this).parent().parent().attr("placeid");
		window.location = "<?=Yii::app()->createUrl('place');?>?id="+loc;
		return false;
	});
	
	$("div.admin-button").fadeTo('fast',.5);
	
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
		defaultPosition: 	"top",
		delay:				150,
	});
	
	$(".admin-button.reorder").click(function(){
		if($(this).is(".selected"))
		{
			$("div.admin-button").removeClass("disabled");
			$(this).removeClass('selected');
			$(this).css("border","1px solid #69f");
			$("table tr td.img-holder").css("border","none");
			$("table tr td.img-holder").css("cursor","pointer");
			$(".cell-overlay").hide();
			$("table tbody").sortable("disable");
		}
		else
		{
			$("div.admin-button").addClass("disabled");
			$(this).addClass("selected");
			$(this).fadeTo("fast",1);
			$(this).css("border","1px dashed #000");
			$("table tr td.img-holder").css("border","1px dashed #000");
			$("table tr td.img-holder").css("cursor","move");
			$(".cell-overlay").show();
			$("table tbody").sortable("enable");
		}
	});
	
	$(".admin-button.active").mousedown(function(){
		$(this).css("border","1px solid #00f");
	});
	$(".admin-button.active").mouseup(function(){
		$(this).css("border","1px solid #69f");
	});
	
	$("table tbody").sortable({
        items: "td:not(.ui-state-disabled)",
		disabled: true,
		placeholder: "ui-state-highlight",
	});
	
	$(".admin-button.add-building").click(function(){
		window.location = "<?=Yii::app()->createUrl('addplace');?>";
		return false;
	});
>>>>>>> bf5750938e5a9043950a9246ab69b3afbc9ad332
});
</script>