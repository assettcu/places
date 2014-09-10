<?php $this->pageTitle=Yii::app()->name; ?>
<?php
$type = @$_REQUEST["type"];
if($type == "") $type="building";

$places = load_places($type);
?>
<h1 class="hide">Places Around the Campus</h1>
<div style="padding-bottom:15px;text-align:right;"><span id="include-residence-checkbox" class="icon icon-checkbox-unchecked" style="cursor:pointer;"> </span> <label for="include-residence-checkbox">Include Residence Halls</label></div>

<ul class="rig columns-4">
    <?php 
    foreach($places as $place):
        $image = $place->load_first_image();
        if(!$image->loaded) {
            $image = new PictureObj(1);
        }
        $thumb = $image->get_thumb();
    ?>
    <li>
        <a href="<?php echo Yii::app()->createUrl('place'); ?>?id=<?php echo $place->placename; ?>">
            <div class="image-container">
                <img src="<?php echo $thumb; ?>" width="100%" height="100%" alt="" />
            </div>
            <div class="title"><?php echo $place->placename; ?></div>
            <?php if(isset($place->shortdesc) and !empty($place->shortdesc)): ?>
            <p><?php echo $place->shortdesc; ?></p>
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

   if(sessionStorage.getItem("showResHalls") == "true") {
      $("#include-residence-checkbox").removeClass("icon-checkbox-unchecked").addClass("icon-checkbox-checked");
    }
    else {
      // Hide Residence halls by default
      $("li:contains('Residence Hall')").hide();
    }
    

    // Toggle Residence Halls via Checkbox
    $("#include-residence-checkbox").on("click",function(){
      if($(this).hasClass("icon-checkbox-unchecked")) {
        $(this).removeClass("icon-checkbox-unchecked").addClass("icon-checkbox-checked");
        sessionStorage.setItem("showResHalls", "true");
        $("li:not(:contains('Residence Hall'))").fadeOut(300, function() {
          $("li").fadeIn(600);
        });
      }
      else {
        $(this).removeClass("icon-checkbox-checked").addClass("icon-checkbox-unchecked");
        sessionStorage.setItem("showResHalls", "false");
        $('.rig > li').fadeOut(300, function() {
          $("li:not(:contains('Residence Hall'))").fadeIn(600);
        });
      }
    }); 
});
</script>