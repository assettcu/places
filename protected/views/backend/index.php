<style>
a {
    text-decoration:none;
}
</style>

<h1>Administration Panel</h1>

<div class=" ui-widget-content ui-corner-all" style="padding:6px;font-size:13px;margin-bottom:10px;">
    Do administrative things!
</div>
<a href="<?php echo Yii::app()->createUrl('backend/new'); ?>"><span class="icon icon-plus"> </span>Add a Place</a><br/><br/>
<a href="<?php echo Yii::app()->createUrl('backend/managephotos'); ?>"><span class="icon icon-picassa"> </span>Manage Photos</a>
