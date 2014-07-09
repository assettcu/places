<?php $this->pageTitle=Yii::app()->name; ?>
<?php
$type = @$_REQUEST["type"];
if($type == "") $type="building";

$places = load_places($type);
?>
<ul class="rig columns-4">
    <?php foreach($places as $place):
              $image = $place->load_first_image();
              if(!$image->loaded)
                $image = new PictureObj(1);
                $thumb = $image->get_thumb();
    ?>
    <li>
        <a href="<?php echo Yii::app()->createUrl('place'); ?>?id=<?php echo $place->placename; ?>">
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