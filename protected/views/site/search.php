<?php

$places_manager = new Places();
$places = $places_manager->search($_REQUEST["q"]);

?>
<style>

</style>

<div class="search-query">
	You searched for <span style="color:#00f;"><?=$_REQUEST["q"];?></span>. Found <span style="color:#00f;"><?=count($places);?></span> results.
</div>

<div class="search-results" style="margin-top:10px;">
	<?php foreach($places as $place): ?>
		<div placeid="<?=$place->placeid;?>" style="margin-bottom:14px;">
			<?php 
				$image = $place->load_first_image();
				if(!$image->loaded)
					$image = new PictureObj(1);
				$thumb = $image->get_thumb();
				switch($place->placetype->machinecode)
				{
					case "building": $image = "school.png"; break;
					case "classroom": $image = "chalkboard.png"; break;
					default: $image = "school.png"; break;
				}
				$marker = StdLib::load_image_source($image);
				$icon = new Imager($marker);
				$icon->width = "36px";
				$icon->height = "36px";
				$icon->styles["float"] = "left";
				$icon->styles["margin-top"] = "-6px";
				$icon->styles["margin-right"] = "6px";
							  
	        ?>
	        <table>
	        	<tr>
	        		<td width="215px">
	        			<div style="width:200px;height:150px;display:inline-block;">
	        				<img src="<?=$thumb;?>" height="100%" width="100%" style="border:1px solid #000;" />
	        			</div>
	        		</td>
	        		<td class="lalign tvalign">
	        			<div class="place-info">
							<h1 style="margin-top:10px;margin-left:5px;">
								<?=$icon->render(); ?> <a href="<?=Yii::app()->createUrl('place');?>?id=<?=$place->placeid;?>"><?=$place->placename;?></a> <span style="font-weight:normal;font-size:12px;color:#ccc;">(<?=$place->placetype->singular;?>)</span>
							</h1>
							<div class="metadata" style="padding-left:15px;">
								<?php if($place->parent_->placetype->machinecode=="building"): ?>
									Belongs to building <a href="<?=Yii::app()->createUrl('place');?>?id=<?=$place->parent_->placeid;?>"><?=$place->parent_->placename;?></a>
								<?php endif; ?>
							</div>
	        			</div>
	        		</td>
	        	</tr>
	        </table>
		</div>	
	<?php endforeach; ?>
</div>
