<?php
$buildings = load_places("building");
?>

<div data-role="page" id="pageone">
    <div data-role="header">
        <h1>Places!</h1>
    </div>
    <div data-role="main" class="ui-content">
    	<ul data-role="listview" data-filter="true" data-input="#myFilter">
    		<?php foreach($buildings as $building): ?>
    		<li value="<?=$building->placeid;?>">
    			<a href="<?=Yii::app()->createUrl('place');?>?id=<?=$building->placeid;?>" data-transition="slide" data-ajax="false" >
    			    <?php $building->render_first_image("auto","auto","thumb"); ?>
    				<?php echo $building->placename;?>
    			</a>
    		</li>
    		<?php endforeach; ?>
    	</ul>
    </div>
</div>