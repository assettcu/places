<?php
    $buildings = load_places("building");
?>

<div data-role="page" id="pageone">
    <div data-role="header">
        <img id="places-logo" alt="University of Colorado - Places" src="<?php echo StdLib::load_image_source("mobile-logo-large"); ?>" height="42" />
        <a href="<?php echo Yii::app()->baseUrl;?>/search" id="search-button" class="ui-btn ui-btn-right ui-btn-icon-right"><span class="icon icon-search"> </span> Search</a>
    </div>
    <div data-role="main" class="ui-content">
    	<ul data-role="listview" data-filter="true" data-input="#myFilter">
    		<?php foreach($buildings as $building): ?>
    		<li value="<?php echo $building->placeid;?>">
    			<a href="<?php echo Yii::app()->createUrl('place');?>?id=<?=$building->placeid;?>" data-transition="slide" data-ajax="false" >
                     <img src="<?php echo $building->get_thumb_path($building->load_first_image(), "href") ?>" width="500" height="500">
    				<strong><?php echo $building->placename;?></strong>
    			</a>
    		</li>
    		<?php endforeach; ?>
    	</ul>
    </div>
    <div data-role="footer">
        <p class="footer">
            Developed by <a href="http://assett.colorado.edu" rel="external" >ASSETT</a> | Copyright &copy; <?php echo date("Y"); ?><br/>
            All Rights Reserved | <a href="http://www.colorado.edu" rel="external" >University of Colorado Boulder</a> | <a href="<?php echo Yii::app()->createUrl('ToStandard'); ?>" rel="external" data-ajax="false">Full Site</a>
        </p>
    </div>
</div>
