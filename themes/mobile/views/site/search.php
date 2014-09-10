<?php 
  $validQuery = (isset($_REQUEST["q"]) && !empty($_REQUEST["q"])) ? true : false; 
  $query = ($validQuery) ? $_REQUEST["q"] : "";
?>

<div data-role="page" id="pageone">
    <div data-role="header">
    <a href="<?php echo Yii::app()->baseUrl; ?>" class="ui-btn ui-btn-left ui-btn-icon-left"><span class="icon icon-home"> </span> Home</a>
        <h1>Search</h1>
    </div>
    <div data-role="main" class="ui-content">
    <div class="search-form">
      <form action="/places/search" method="get">
      <div class="search-button-holder" style="float:right; height: 10px">
        <input type="submit" value="Search" style="float: right" />
        </div>
        <div style="overflow: hidden; padding: 0em 0.5em; margin: 0em -0.5em;">
          <input type="text" style="width: 100%;" name="q" placeholder="Search CU Boulder" value="<?=$query?>" />
        </div>
      </form>
      <?php if($validQuery) : ?>
        <div class="search-query"><?php echo count($places);?> results found for &quot;<?=$query?>&quot;.<br><br></div>
      <?php endif; ?>
    </div>

      <?php if($validQuery) : ?>

<!--  -->
      <ul data-role="listview" data-filter="true" data-input="#myFilter">
        <?php foreach($places as $building): ?>
        <li value="<?=$building->placeid;?>">
          <a href="<?=Yii::app()->createUrl('place');?>?id=<?=$building->placeid;?>" data-transition="slide" data-ajax="false" >
            <?php $building->render_first_image("auto","auto","thumb"); ?>
            <?=preg_replace('/('.$query.')/i', '<strong>$0</strong>', $building->placename);?>
            <br><span class="placetype-<?php echo $building->placetype->machinecode; ?>"><?php echo $building->placetype->singular; ?></span>
          </a>
        </li>
        <?php endforeach; ?>
      </ul>

      <? else : ?>
        <div class="no-query-text">Please enter a search query.</div>
      <? endif; ?>
      </div>
    <div data-role="footer">
        <p class="footer">
            Developed by <a href="http//assett.colorado.edu">ASSETT</a> | Copyright &copy; <?php echo date("Y"); ?><br/>
            All Rights Reserved | <a href="http//colorado.edu">University of Colorado Boulder</a> | <a href="<?php echo Yii::app()->createUrl('ToStandard'); ?>"data-ajax="false">Full Site</a>
        </p>
    </div>
</div>