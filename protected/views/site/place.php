<?php
$id = $_REQUEST["id"];
$place = new PlacesObj($id);

if(!$place->loaded) {
    Yii::app()->user->setFlash("warning","Could not find place in the Place's database!");
    $this->redirect("index");
    exit;
}

$place->load_metadata();

$classes = StdLib::external_call(
    "http://assettdev.colorado.edu/ascore/api/buildingclasses",
    array(
        "building"  => $place->metadata->data["building_code"]["value"],
        "term"      => "20137", # Fall semester
    )
);

?>
<div class="entry">
    <a name="images"></a>
    <h2><?php echo $place->placename; ?></h2>
    <h3 class="nav sticky" sticky="150">
        <ul>
            <li><a href="#images">Building Info</a></li>
            <li><a href="#">Rooms</a></li>
            <li><a href="#">Google Map</a></li>
            <li><a href="#buildingclasses">Classes</a></li>
        </ul>
    </h3>
    <div class="content">
        <div class="images">
            <div id="galleria">
                <?php 
                if($place->has_pictures()) {
                    $place->render_pictures(true);
                } 
                else {
                    $place->render_no_image();
                }
                ?>
            </div>
            <br class="clear" />
        </div>
        <div class="meta">
            <div class="metatitle">Relevant Information</div>
            <div class="metachoice">
                <a href="#" class="ri selected">All</a> |
                <a href="#" class="ri">Students</a> |
                <a href="#" class="ri">Teachers</a>
            </div>
            <ul id="ri-list">
                <?php foreach($place->metadata->data as $index=>$data): ?>
                <?php if(($data["metatype"]!="students" and $data["metatype"]!="teachers" and $data["metatype"]!="both") or $data["value"] == "") continue; ?>
                <li class="<?=$data["metatype"];?>">
                    <div class="label"><?php echo $data["display_name"]; ?></div>
                    <div class="value"><?php echo $data["value"]; ?></div>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
        
        <br class="clear" />
        
        <a name="rooms"></a><br/>
        <h3>Rooms in this Building</h2>
        
        <?php
        
        
        ?>
        
        <br class="clear" />
        
        <a name="buildingclasses"></a><br/>
        <h3>Classes in this Building</h2>
            
        <table class="fancy-table">
            <thead>
                <tr>
                    <th>Course</th>
                    <th class="calign">Section</th>
                    <th>Class</th>
                    <th class="calign">Room</th>
                    <th class="calign">Term</th>
                    <th class="calign">Days</th>
                    <th class="calign">Times</th>
                </tr>
            </thead>
            <?php if(count($classes) > 0): ?>
            <?php foreach($classes as $class): ?>
                <?php
                # Do some processing before displaying
                $starttime  = $class["timestart"];
                $endtime    = $class["timeend"];
                $datetime = new DateTime($starttime);
                $starttime = $datetime->format("g:s a");
                $datetime = new DateTime($endtime);
                $endtime = $datetime->format("g:s a");
                
                $catalog_term = "2013-14";
                ?>
            <tr>
                <td>
                    <a href="http://www.colorado.edu/catalog/<?php echo $catalog_term; ?>/courses?subject=<?php echo $class["subject"]; ?>&number=<?php echo $class["course"]; ?>" target="_blank">
                        <?php echo $class["subject"]; ?> <?php echo $class["course"]; ?>
                    </a>
                </td>
                <td class="calign"><?php echo substr("00".$class["section"],-3,3); ?></td>
                <td><?php echo $class["title"]; ?></td>
                <td class="calign"><?php echo $class["building"]." ".$class["roomnum"]; ?></td>
                <td class="calign"><?php 
                    $term = substr($class["yearterm"],-1,1);
                    switch($term) {
                        case 7: $term = "Fall"; break;
                        case 1: $term = "Spring"; break;
                        case 5: $term = "Summer"; break;
                        default: $term = $term; break;
                    }
                    echo $term." ".substr($class["yearterm"],0,4);
                ?></td>
                <td class="calign"><?php echo $class["meetingdays"]; ?></td>
                <td class="calign"><?php echo @$starttime." - ".@$endtime; ?></td>
            </tr>
            <?php endforeach; ?>
            <?php else: ?>
            <tr>
                <td class="empty">
                    
                </td>
            </tr>
            <?php endif; ?>
        </table>
    </div>
</div>

<script src="<?php echo WEB_LIBRARY_PATH; ?>jquery/modules/galleria/galleria-1.3.5.js"></script>
<link type="text/css" rel="stylesheet" href="<?php echo WEB_LIBRARY_PATH; ?>jquery/modules/galleria/themes/classic/galleria.classic.css" />

<script>
Galleria.loadTheme('<?php echo WEB_LIBRARY_PATH; ?>/jquery/modules/galleria/themes/classic/galleria.classic.min.js');

$(function() {
  $('a[href*=#]:not([href=#])').click(function() {
    if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
      var target = $(this.hash);
      target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
      if (target.length) {
        $('html,body').animate({
          scrollTop: target.offset().top
        }, 800);
        return false;
      }
    }
  });
});

jQuery(document).ready(function($){
    init();
    
    $("a.ri").click(function(){
       var $val = $(this).text();
       $("a.ri").removeClass("selected");
       $(this).addClass("selected");
       if($val == "All") {
           $("ul#ri-list li:hidden").each(function(){
              $(this).stop().show('fade'); 
           });
       } 
       else if($val=="Students") {
           $("ul#ri-list li.students:hidden").stop().show('fade');
           $("ul#ri-list li.none:visible").stop().hide('fade');
           $("ul#ri-list li.teachers:visible").stop().hide('fade');
       }
       else if($val=="Teachers") {
           $("ul#ri-list li.teachers:hidden").stop().show('fade');
           $("ul#ri-list li.none:visible").stop().hide('fade');
           $("ul#ri-list li.students:visible").stop().hide('fade');
       }
    });
});
function init()
{
    Galleria.run("#galleria", {
        lightbox: true,
        dummy: "<?php echo WEB_IMAGE_LIBRARY.'images/no_image_available.png'; ?>",
        extend: function() {
            var gallery = this; // "this" is the gallery instance
            console.log(gallery); // call the play method
            $('#download-image').click(function() {
            });
        }
    });
}
</script>