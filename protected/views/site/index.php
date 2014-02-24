<?php $this->pageTitle=Yii::app()->name; ?>
<?php
$type = @$_REQUEST["type"];
if($type == "") $type="building";

$organizer = new Places();
$places = $organizer->load_places($type);

?>

<?php if(isset(Yii::app()->user) and !Yii::app()->user->isGuest): ?>
<div class="admin-bar">
	<div class="admin-button ui-widget-header active add-building" title="Add New Building">
		<?=StdLib::load_image("plus","20px");?>
	</div>
	<div class="admin-button ui-widget-header active" title="Download Entire Album">
		<?=StdLib::load_image("arrow_down","20px");?>
	</div>
	<div class="admin-button ui-widget-header active reorder" title="Reorder Buildings">
		<?=StdLib::load_image("wizard","20px");?>
	</div>
	<div class="spacer">
		
	</div>
	<div class="admin-button ui-widget-header active configure" title="Configure System">
		<?=StdLib::load_image("options_2","20px");?>
	</div>
</div>
<?php endif; ?>

<ul class="rig columns-4">
    <?php foreach($places as $place):
              $image = $place->load_first_image();
              if(!$image->loaded)
                $image = new PictureObj(1);
                $thumb = $image->get_thumb();
    ?>
    <li>
        <a href="<?php echo Yii::app()->createUrl('place'); ?>?id=<?php echo $place->placeid; ?>">
            <div class="image-container">
                <img src="<?php echo $thumb; ?>" width="100%" height="100%" />
            </div>
            <h3><?php echo $place->placename; ?></h3>
            <?php if(isset($place->description) and !empty($place->description)): ?>
            <p><?php echo $place->description; ?></p>
            <?php endif; ?>
        </a>
    </li>
    <?php endforeach; ?>
</ul>

<script>
jQuery(document).ready(function() {
   $("ul.rig li").hover(
       function(){
           $(this).fadeTo("fast",1);
       },
       function(){
           $(this).fadeTo("fast",0.8);
       }
   );
});
</script>