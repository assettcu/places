<?php
$buildings = load_places("building");
?>

<div data-role="page" id="pageone">
    <div data-role="header">
        <img alt="" style="display:inline;padding:5px 10px;" src="//places.colorado.edu/library/images/mobile-logo-large.png" height="42" />
        <a href="<?=Yii::app()->baseUrl;?>/search" id="search-button" class="ui-btn ui-btn-right ui-btn-icon-right"><span class="icon icon-search"> </span> Search</a>
    </div>
    <div data-role="main" class="ui-content">
    	<ul data-role="listview" data-filter="true" data-input="#myFilter">
    		<?php foreach($buildings as $building): ?>
    		<li value="<?=$building->placeid;?>">
    			<a href="<?=Yii::app()->createUrl('place');?>?id=<?=$building->placeid;?>" data-transition="slide" data-ajax="false" >
    			    <?php $building->render_first_image("auto","auto","thumb"); ?>
    				<strong><?php echo $building->placename;?></strong>
    			</a>
    		</li>
    		<?php endforeach; ?>
    	</ul>
    </div>
    <div data-role="footer">
        <p class="footer">
            Developed by <a href="http//assett.colorado.edu">ASSETT</a> | Copyright &copy; <?php echo date("Y"); ?><br/>
            All Rights Reserved | <a href="http//colorado.edu">University of Colorado Boulder</a> | <a href="<?php echo Yii::app()->createUrl('ToStandard'); ?>"data-ajax="false">Full Site</a>
        </p>
    </div>
</div>

