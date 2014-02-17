<?php

if(isset($_REQUEST["id"])) {
	$place = new PlacesObj($_REQUEST["id"]);
	if(!$place->loaded) {
		$this->redirect(Yii::app()->createUrl('index'));
		exit;
	}
} 
else {
	$this->redirect(Yii::app()->createUrl('index'));
	exit;
}
$image = $place->load_first_image();
$imager = new Imager(getcwd()."/".$image->path);
?>
<div class="ui-body ui-body-d">
	<h1><?=$place->placename;?></h1>
	<p><?=@$place->description;?></p>
	
	<?php $imager->render(); ?>
	
</div>