<?php
$places = new Places;
$buildings = $places->load_places("building");

?>

<div data-role="collapsible">
	<h3>Buildings</h3>
	<ul data-role="listview">
		<?php foreach($buildings as $building): ?>
		<li value="<?=$building->placeid;?>">
			<a href="<?=Yii::app()->createUrl('place');?>?id=<?=$building->placeid;?>">
				<?=$building->placename;?>
			</a>
		</li>
		<?php endforeach; ?>
	</ul>
</div>