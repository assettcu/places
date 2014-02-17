<?php $this->pageTitle=Yii::app()->name; ?>
<style>
	table {
		border-spacing:5px;
	}
	table tr td {
		padding:0;
		margin:0;
		padding-right:5px;
		padding-left:5px;
	}
	table tr td:hover {
		cursor: pointer;
	}
	table tr td {
		color: #000;
		text-shadow: 0 0 0.3em #DEF, 0 0 0.3em #DEF;
		font-size:18px;
		white-space: pre-line;
		text-align: center;
		vertical-align:middle;
		position: relative;
	}
	table tr td div.image {
		background-color:#fff;
		padding:1px;
		padding-left:8px;
		padding-right:5px;
		line-height: 1.5em;
		border-radius:5px;
		position:absolute;
		left:0;
		top:50px;
		text-align: left;
	}
	table tr td img {
		opacity: .5;
	}
</style>
<?php

if ($handle = opendir(getcwd()."/images/thumbs/cu-classrooms")) {
	while (false !== ($entry = readdir($handle))) {
		if ($entry != "." && $entry != "..") {
			if ($subhandle = opendir(getcwd()."/images/thumbs/cu-classrooms/".$entry."/large")) {
				$imagename = "";
				while (false !== ($image = readdir($subhandle))) {
					if ($entry != "." && $entry != "..") {
						if (substr($image,-3,3)=="jpg") {
							$imagename = $image;
							break;
						}
					}
				}
				if($entry == "unfiled") continue;
				$name = str_replace("-"," ",$entry);
				$name = ucwords($name);
				$dirs[] = array(
					"name"=>$name,
					"thumbname"=>Yii::app()->baseurl."/images/thumbs/cu-classrooms/".$entry."/large/".$imagename,
					"machinename"=>strtolower(str_replace(" ","-",$name)),
				);
			}
		}
	}
	closedir($handle);
}

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
	div.tablinks {
		margin-left:45px;
	}
	a.tablink {
		font-size:13px;
		letter-spacing:1px;
		font-weight:normal;
		text-decoration:none;
		color:#79f;
	}
	a.tablink:hover {
		text-decoration:underline;
	}
	a.tablink.selected {
		color:#c0c0c0;
	}
	a.tablink.selected:hover {
		text-decoration:none;
	}
	div.breadcrumb-bar {
		margin:-11px;
		border:1px solid #09f;
		margin-bottom:15px;
		padding:5px;
	}
</style>

<div class="breadcrumb-bar ui-widget-header" style="font-size:13px;font-weight:normal;">
  &gt; <a href="<?=Yii::app()->baseUrl;?>">Places</a>
</div>

<h1 style="margin-top:10px;margin-left:5px;"><?=$imager->render(); ?> Places</h1>

<div class="tablinks">
	<a href="#" class="tablink selected">Places</a> | 
	<a href="#" class="tablink">Details</a>
</div>

<table>
	<?php foreach($dirs as $image): ?>
		<?php if($count%$cols==0): ?>
		<tr>
		<?php endif; ?>
			<td width="<?=100/$cols;?>%" height="150px" name="<?=$image["machinename"];?>">
				<img src="<?=$image["thumbname"];?>" width="100%" height="100%" />
				<div class="image"><?=$image["name"];?></div>
			</td>
		<?php if($count%$cols==$cols-1): ?>
		</tr>
		<?php endif; ?>
	<?php $count++; endforeach; ?>
</table>

<script>
jQuery(document).ready(function($){
	$("td").hover(
		function(){
			$(this).find("img").stop().css("opacity",1);
		},
		function(){
			$(this).find("img").stop().css("opacity",0.5);
		}
	);
	
	$("td").click(function(){
		var loc = $(this).attr("name");
		window.location = "<?=Yii::app()->createUrl('building');?>?b="+loc;
		return false;
	});
});
</script>