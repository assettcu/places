<?php
$placeid = $_REQUEST["id"];

$place = new PlacesObj($placeid);
if(!$place->loaded){
	$this->redirect(Yii::app()->createUrl('error'));
	exit;
}

$place->load_pictures();

$table_width = 1130;
$cols = 5;
$colsize = $table_width / $cols - 10;
$count=0;

$marker = StdLib::load_image_source("chalkboard.png");
$icon = new Imager($marker);
$icon->width = "36px";
$icon->height = "36px";
$icon->styles["float"] = "left";
$icon->styles["margin-top"] = "-6px";
$icon->styles["margin-right"] = "6px";

$list_height = $imager->height - 28;

?>

<div class="breadcrumb-bar ui-widget-header" style="font-size:13px;font-weight:normal;">
   &gt; <a href="<?=Yii::app()->baseUrl;?>">Buildings</a> &gt; 
   <a href="<?=Yii::app()->createUrl('building');?>?id=<?=$place->parent_->placeid;?>"><?=$place->parent_->placename;?></a> &gt;  
   <a href="<?=Yii::app()->createUrl('classroom');?>?id=<?=$place->placeid;?>"><?=$place->placename;?></a>
</div>

<h1 style="margin-top:10px;margin-left:5px;">
	<?=$icon->render(); ?> <?=$place->placename;?> <span style="font-weight:normal;font-size:12px;color:#ccc;">(classroom)</span>
	<span class="date-taken" style="font-size:15px;color:#89f;">Last Updated: January 30, 2011</span>
</h1>

<div class="tablinks">
	<a href="#" id="details-link" class="tablink selected">Details</a>
</div>


<style>
div#details div.header {
    padding:6px;
    width:97%;
    font-size:15px;
    font-weight:bold;
    margin-bottom:10px;
}
div#details {
    margin-bottom:25px;
}
</style>

<div id="details" class="hide">
    <div class="ui-widget-header header">Details</div>
	<div class="obj-container" style="width:95%;margin:auto;margin-bottom:30px;">
		
		<div style="width:100%;text-align:right;margin-bottom:5px;">
			View information as for:
			<select>
				<option value="both">Both Students and Teachers</option>
				<option value="students">Students</option>
				<option value="teachers">Teachers</option>
			</select>
		</div>
		
		<div class="" style="width:600px;float:left;">
			<div id="galleria">
				<?php $place->render_pictures(); ?>
			</div>
			<br class="clear" />
		</div>
		
		<div class="" style="float:left;border-left:1px solid #ccc;width:450px;padding-left:10px;margin-right:10px;">
			<div class="ui-widget-header" style="padding:4px;font-size:15px;">
				Classroom Information
			</div>
			<div class="rooms-list" style="overflow-y:scroll;height:370px;">
				<div class="students">
					<?php foreach($place->metadata->data as $index=>$data): ?>
						<?php if($data["metatype"]!="students") continue; ?>
						<div class="info-box" style="padding:7px;padding-right:0px;border-bottom:1px solid #cdf;background-color:#09f;color:#fff;font-weight:bold;">
							<?=$data["display_name"];?>
						</div>
						<div class="room-link" style="padding:7px;padding-right:0px;border-bottom:1px solid #cdf;">
							<?=$data["value"];?>
						</div>
					<?php endforeach; ?>
				</div>
				<div class="teachers">
					<?php foreach($place->metadata->data as $index=>$data): ?>
						<?php if($data["metatype"]!="teachers") continue; ?>
						<div class="info-box" style="padding:7px;padding-right:0px;border-bottom:1px solid #cdf;background-color:#09f;color:#fff;font-weight:bold;">
							<?=$data["display_name"];?>
						</div>
						<div class="room-link" style="padding:7px;padding-right:0px;border-bottom:1px solid #cdf;">
							<?=$data["value"];?>
						</div>
					<?php endforeach; ?>
				</div>
				<div class="both">
					<?php foreach($place->metadata->data as $index=>$data): ?>
						<?php if($data["metatype"]!="both") continue; ?>
						<div class="info-box" style="padding:7px;padding-right:0px;border-bottom:1px solid #cdf;background-color:#09f;color:#fff;font-weight:bold;">
							<?=$data["display_name"];?>
						</div>
						<div class="room-link" style="padding:7px;padding-right:0px;border-bottom:1px solid #cdf;">
							<?=$data["value"];?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
		<br class="clear" />
	</div>
</div>

<script src="http<?=(isset($_SERVER["HTTPS"]) and $_SERVER["HTTPS"]=="on")?"s":"";?>://<?=$_SERVER["SERVER_NAME"];?>/libraries/javascript/jquery/modules/galleria/galleria-1.2.8.js"></script>
<script src="http<?=(isset($_SERVER["HTTPS"]) and $_SERVER["HTTPS"]=="on")?"s":"";?>://<?=$_SERVER["SERVER_NAME"];?>/libraries/javascript/jquery/modules/jnotes/js/jquery-notes_1.0.8_min.js"></script>

<link type="text/css" rel="stylesheet" href="http<?=(isset($_SERVER["HTTPS"]) and $_SERVER["HTTPS"]=="on")?"s":"";?>://<?=$_SERVER["SERVER_NAME"];?>/libraries/javascript/jquery/modules/galleria/themes/classic/galleria.classic.css">
<link type="text/css" rel="stylesheet" href="http<?=(isset($_SERVER["HTTPS"]) and $_SERVER["HTTPS"]=="on")?"s":"";?>://<?=$_SERVER["SERVER_NAME"];?>/libraries/javascript/jquery/modules/jnotes/css/style.css">

<script>
Galleria.loadTheme('http<?=(isset($_SERVER["HTTPS"]) and $_SERVER["HTTPS"]=="on")?"s":"";?>://<?=$_SERVER["SERVER_NAME"];?>/libraries/javascript/jquery/modules/galleria/themes/classic/galleria.classic.min.js');
jQuery(document).ready(function($){
	
	init();
	
	$("td.img-holder").hover(
		function(){
			$(this).find("img").stop().css("opacity",1);
		},
		function(){
			$(this).find("img").stop().css("opacity",0.5);
		}
	);
	
	$("td.img-holder").click(function(){
		if($(this).is(".empty")) return false;
		var loc = $(this).attr("name");
		window.location = "<?=Yii::app()->createUrl('classroom');?>?id="+loc;
		return false;
	});
	
	$(".room-link").click(function(){
		window.location = "<?=Yii::app()->createUrl('classroom');?>?b=<?=$building?>&room="+$(this).attr('roomname');
		return false;
	});
	
	$("select").change(function(){
		var val = $(this).val();
		if(val=="students")
		{
			$("div.teachers").hide();
			$("div.students").show();
		}
		if(val=="teachers")
		{
			$("div.students").hide();
			$("div.teachers").show();
		}
		if(val=="both")
		{
			$("div.students:hidden").show();
			$("div.teachers:hidden").show();
		}
	});

	$("#places-link").click(function(){
		if($(this).is(".selected")) return;
		
		// Highlight currently active tab link
		$("div.tablinks a").removeClass('selected');
		$(this).addClass('selected');
		
		$("#details").hide('fade','fast',function(){
			$("#places").show('fade');
		});
		return false;
	});
	
	$("#details-link").click(function(){
		if($(this).is(".selected")) return;
		
		// Highlight currently active tab link
		$("div.tablinks a").removeClass('selected');
		$(this).addClass('selected');
		
		$("#places").hide('fade','fast',function(){
			Galleria.run("#galleria", {
				lightbox: true,
			});
			$("#details").show('fade');
		});
		return false;
	});
	
});

function init()
{
	Galleria.run("#galleria", {
		lightbox: true,
	});
	$("#details").show('fade');
}
</script>


