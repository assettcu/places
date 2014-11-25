<?php
$childplace_names[] = array();

if(isset($_REQUEST["id"])) {
	$place = new PlacesObj($_REQUEST["id"]);
	if(!$place->loaded) {
		$this->redirect(Yii::app()->createUrl('index'));
		exit;
	}
} 
else {
	$this->redirect(Yii::app()->createUrl('index'));
	exit;
}
$place->load_images();
$place->load_metadata();
$childplaces = $place->get_children();

# Session will keep which year/term (yt) user is looking at across the application
$yt = "20147";
if(!isset($_SESSION)) {
    session_start();
}
if(isset($_SESSION["yt"]) and strlen($_SESSION["yt"]) == 5) {
    $yt = $_SESSION["yt"];
}
if(isset($_REQUEST["yt"]) and strlen($_REQUEST["yt"]) == 5) {
    $yt = $_REQUEST["yt"];
    $_SESSION["yt"] = $yt;
}

# Load the metadata to display in the "Relevant Information" tab
$place->load_metadata();

# Load classes for a building
if($place->placetype->machinecode == "building") {
    $classes = StdLib::external_call(
        "http://compass.colorado.edu/ascore/api/buildingclasses",
        array(
            "building"  => $place->metadata->data["building_code"]["value"],
            "term"      => $yt, # Semester/Year to lookup
        )
    );
}
# Load classes for a classroom
else if($place->placetype->machinecode == "classroom") {
    $parent = $place->get_parent();
    $parent->load_metadata();
    $building_code = $parent->metadata->data["building_code"]["value"];
    $classes = StdLib::external_call(
        "http://compass.colorado.edu/ascore/api/classroomclasses",
        array(
            "building"  => $building_code,
            "classroom" => $place->placename,
            "term"      => $yt, # Semester/Year to lookup
        )
    );
}
# Don't load classes if other
else {
    $classes = array();
}

$yearterms = StdLib::external_call(
    "http://compass.colorado.edu/ascore/api/uniqueyearterms"
);

function footer() {
    ob_start();
?>
<div data-role="footer">
    <p class="footer">
        Developed by <a href="http://assett.colorado.edu">ASSETT</a> | Copyright &copy; <?php echo date("Y"); ?><br/>
        All Rights Reserved | <a href="http://colorado.edu">University of Colorado Boulder</a> | <a href="<?php echo Yii::app()->createUrl('ToStandard'); ?>"data-ajax="false">Full Site</a>
    </p>
</div>
<?php
    $contents = ob_get_contents();
    return $contents;
}

function insert_header($place) {
  ob_start();
?>
<div data-role="header" data-position="inline">
        <a href="<?php echo Yii::app()->getBaseUrl();?>/" class="ui-btn ui-btn-left ui-btn-icon-left"><span class="icon icon-home"></span></a>
        <h1><?php echo $place->placename; ?></h1>
          <div data-role="navbar" class="place-navbar">
            <ul>
              <?php if($place->placetype->singular != "Building" and $place->parentid != 0): ?>
              <li><a href="<?php Yii::app()->createUrl('place'); ?>?id=<?php echo $place->parentid; ?>" data-ajax='false'><span class="icon icon-point-left"> </span><span class="nav-text"> Back</a></li>
              <?php endif; ?>
              <li id="place-navbar-images"><a href="#images" data-transition="fade"><span class="icon icon-image2"> </span> <span class="nav-text">Images</span></a></li>
            <?php if($place->placetype->singular == "Building"): ?>
              <li id="place-navbar-spaces"><a href="#spaces" data-transition="fade"><span class="icon icon-enter"> </span> <span class="nav-text">Spaces</a></span></li>
              <li id="place-navbar-map"><a href="#map" data-transition="fade"><span class="icon icon-map"> </span> <span class="nav-text">Map</span></a></li>
              <?php endif; ?>
            </ul>
            <ul>
              <li id="place-navbar-information"><a href="#information" data-transition="fade"><span class="icon icon-office"> </span> <span class="nav-text"><?php echo $place->placetype->singular; ?> Info</span></a></li>
              <li id="place-navbar-classes"><a href="#classes" data-transition="fade"><span class="icon icon-list"> </span> <span class="nav-text">Classes</span></a></li>
            </ul>
          </div>
        <a href="<?php echo Yii::app()->getBaseUrl();?>/search" class="ui-btn ui-btn-right ui-btn-icon-right"><span class="icon icon-search"></span></a>
    </div>
<?php
  $contents = ob_get_contents();
  return $contents;
}
?>

<div data-role="page" id="images" data-dom-cache="false">

    <?php insert_header($place); ?>
    
    <ul class="rslides centered-btns" id="slider2">
        <?php 
        if(empty($place->images)) :
            $image = new PictureObj(1);
        ?>
            <li><a href="#"><?php echo $image->render(); ?></a></li>
        <?php
        else :
        foreach($place->images as $image): $image->make_thumb();
        ?>
            <li><a href="#">
                <img src="<?php echo $image->load_image_href(); ?>">
            </a></li>
        <?php endforeach; ?>
        <?php endif; ?>
    </ul>
    
    <?php footer(); ?>

</div>

<div data-role="page" id="information" data-dom-cache="false">
    
    <?php insert_header($place); ?>
    
    <div data-role="main" class="ui-content">
        <div class="ui-grid-solo">
            <?php $count=0; foreach($place->metadata->data as $index=>$data): ?>
                <?php if(($data["metatype"]!="students" and $data["metatype"]!="teachers" and $data["metatype"]!="both") or $data["value"] == "") continue; ?>
            <div class="ui-block-a">
                <div class="label" style="text-align:left;">
                    <?php if(isset($data["icon"])) { ?><span class="icon <?php echo $data["icon"]; ?>"> </span><?php } echo $data["display_name"]; ?>
                </div>
                <div class="value" style="padding:8px;">
                    <?php echo $data["value"]; ?>
                </div>
            </div>
            <?php  $count++; endforeach; ?>
        </div>
    </div>
    
    <?php footer(); ?>
</div>


<div data-role="page" id="spaces" data-dom-cache="false">
    
    <?php insert_header($place); ?>
    
    <div data-role="main" class="ui-content">
        <ul data-role="listview" data-filter="true" data-input="#myFilter">
        <?php if(!empty($childplaces)):  ?>
            <?php foreach($childplaces as $childplace):
            $childplace_names[] = $childplace->placename;
            $image = $childplace->load_first_image();
            if(!$image->loaded) {
              $image = new PictureObj(1);
            }
            if(!$image->loaded) {
              continue;
            }
            $image->make_thumb(false);
          ?>
            <li value="<?php echo $childplace->placeid;?>">
                <a href="<?php echo Yii::app()->createUrl('place');?>?id=<?=$childplace->placeid;?>" data-transition="fade" data-ajax="false">
                    <img src="<?php echo $image->get_thumb(); ?>">
                    <?php echo $childplace->placename;?>
                    <div class="placetype"><?php echo $childplace->placetype->singular; ?></div>
                    <?php if(isset($childplace->description) and !empty($childplace->description)): ?>
                    <p><?php echo $childplace->description; ?></p>
                    <?php endif; ?>
                </a>
            </li>
            <?php endforeach; ?>
        <?php else: ?>
            <li>
                There are no spaces for this <?php echo $place->placetype->singular; ?> yet.
            </li>
        <?php endif; ?>
        </ul>
    </div>
    
    <?php footer(); ?>
</div>


<?php if($place->placetype->machinecode == "building") : ?>
<div data-role="page" id="map" data-dom-cache="false">
    
    <?php insert_header($place); ?>
    
    <div data-role="main" class="ui-content">
        <div id="map_canvas" style="height:250px;width:auto;"></div>
    </div>
    
    <?php footer(); ?>
</div>
<?php endif; ?>


<div data-role="page" id="classes" data-dom-cache="false">
    
    <?php insert_header($place); ?>
    
    <div data-role="main" class="ui-content">
        <form method="post" id="yt-form">
            <label for="yt" class="ui-hidden-accessible">Change Term/Year: </label>
            <select name="yt" id="yt-select">
                <?php foreach($yearterms as $yearterm): ?>
                <option value="<?php echo $yearterm["value"]; ?>" <?php if((isset($_REQUEST["yt"]) and $_REQUEST["yt"] == $yearterm["value"]) or (!isset($_REQUEST["yt"]) and isset($_SESSION["yt"]) and $_SESSION["yt"] == $yearterm["value"])) : ?>selected='selected'<?php endif; ?>><?php echo $yearterm["display"]; ?></option>
                <?php endforeach; ?>
            </select>
        </form>

        <div id="courses-listview">
        <ul data-role="listview" data-inset="true" data-filter="true" data-filter-placeholder="Filter classes" class="course-listing ui-icon-alt">
          <?php if(count($classes) > 0): ?>
            <?php 
              $count=0; 
              foreach($classes as $class): 
                $count++; 
                $starttime  = $class["timestart"];
                $endtime    = $class["timeend"];
                $datetime = new DateTime($starttime);
                $starttime = $datetime->format("g:i a");
                $datetime = new DateTime($endtime);
                $endtime = $datetime->format("g:i a");
                $catalog_term = "2013-14";
                $linkable = ($place->placetype->machinecode == "building" and in_array($class["building"]." ".$class["roomnum"],$childplace_names,FALSE));
            ?>

            <li>
              <?php if($linkable): ?>
                <a href="<?php echo Yii::app()->createUrl('place'); ?>?id=<?php echo $class['building'].' '.$class['roomnum']; ?>" data-ajax="false">
              <?php endif; ?>
                <h2>
                  <?php echo $class["subject"]; ?> <?php echo $class["course"]; ?>-<span style="font-weight:normal;"><?php echo substr("00".$class["section"],-3,3); ?></span>
                </h2>
                <span class="ui-li-aside">
                  <?php echo $class["building"]." ".$class["roomnum"]; ?>
                </span>
                <p style="font-style:italic; font-size: 0.8rem;"><?php echo $class["title"]; ?></p>
                <p><?php echo $class["meetingdays"]; ?> <?php echo @$starttime." - ".@$endtime; ?></td></p>
              <?php if($linkable): ?>
                </a>
              <?php endif; ?>
            </li>
          <?php endforeach; ?>
        <?php else : ?>
          <li>
            <p style="white-space:normal; text-align:center;">There are no classes in this <?php echo strtolower($place->placetype->singular); ?> currently for <?php echo $yt; ?>.</p>
          </li>
        <?php endif; ?>
      </ul>
      </div>
    </div>
    
    <?php footer(); ?>
</div>
