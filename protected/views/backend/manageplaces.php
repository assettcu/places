<?php
$places = load_places("building");
$flashes = new Flashes;
$flashes->render();
?>

<h1>Manage Photos for a Place</h1>

<div class="ui-widget-content ui-corner-all" style="padding:6px;font-size:13px;margin-bottom:10px;">
    <span class="icon icon-home"> </span> Choose a place to manage photos with.
</div>

<?php 
$organized = array();
foreach($places as $place) {
    $pictures = "";
    if(!$place->has_pictures()) {
        $pictures = "<span class='icon icon-eye-blocked'> </span>";
    }
    echo "<div style='padding:5px;'><a href='".Yii::app()->createUrl('backend/editplace')."?id=".$place->placename."'>".$place->placename."</a> | <span class='small-text placetype-".$place->placetype->machinecode."'>(".$place->placetype->singular.")</span> ".$pictures." </div>";
    $children = $place->get_children();
    foreach($children as $child) {
        if(!$child->has_pictures()) {
            $pictures = "<span class='icon icon-eye-blocked'> </span>";
        }
        echo "<div style='padding-left:25px;'>&raquo; <a href='".Yii::app()->createUrl('backend/editplace')."?id=".$child->placename."'>".$child->placename."</a> | <span class='small-text placetype-".$child->placetype->machinecode."'>".$child->placetype->singular."</span> ".$pictures."</div>";
    }
}
?>