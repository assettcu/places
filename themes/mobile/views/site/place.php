<?php

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
$yt = "20144";
if(!isset($_SESSION)) {
    session_start();
}
if(isset($_SESSION["yt"]) and strlen($_SESSION["yt"]) == 5) {
    $yt = $_SESSION["yt"];
}
if(isset($_REQUEST["yt"]) and strlen($_REQUEST["yt"]) == 5) {
    $yt = $_REQUEST["yt"];
    $_SESSION["yt"] = $yt;
    die("HERE");
}

# Load the metadata to display in the "Relevant Information" tab
$place->load_metadata();

# Load classes for a building
if($place->placetype->machinecode == "building") {
    $classes = StdLib::external_call(
        "http://assettdev.colorado.edu/ascore/api/buildingclasses",
        array(
            "building"  => $place->metadata->data["building_code"]["value"],
            "term"      => $yt, # Semester/Year to lookup
        )
    );
}
# Load classes for a classroom
else if($place->placetype->machinecode == "classroom") {
    $classes = StdLib::external_call(
        "http://assettdev.colorado.edu/ascore/api/classroomclasses",
        array(
            "classroom" => $place->placename,
            "term"      => $yt, # Semester/Year to lookup
        )
    );
}
# Don't load classes if other
else {
    $classes = array();
}

function footer() {
    ob_start();
?>
<div data-role="footer">
    <p class="footer">
        Developed by <a href="http//assett.colorado.edu">ASSETT</a> | Copyright &copy; <?php echo date("Y"); ?><br/>
        All Rights Reserved | <a href="http//colorado.edu">University of Colorado Boulder</a>
    </p>
</div>
<?php
    $contents = ob_get_contents();
    return $contents;
}
?>
<style type="text/css">
    div.gallery-row:after { clear: both; content: "."; display: block; height: 0; visibility: hidden; }
    div.gallery-item { float: left; width: 33%; }
    div.gallery-item a { display: block; margin: 5px; border: 1px solid #3c3c3c; }
    div.gallery-item img { display: block; width: 100%; height: auto;}
    #Gallery1 .ui-content, #Gallery2 .ui-content { overflow: hidden; }
    #Header { background: #EEBF02; height: 61px; padding: 0; border-bottom: 1px solid #3c3c3c;  }
    #Header img { display: block; margin: 0 auto 0; }
    
    #MainContent { background: #ffffff; padding-bottom: 30px; }
    
    #Footer { padding: 10px; border-top: none; } 
    
    div.label {
        background-color:#d0d0d0;
        padding:5px;
        border-right:2px solid #fff;
        text-align:center;
    }
    div.value {
        text-align:center;
        word-wrap:break-word;
    }
    div.placetype {
        font-weight:normal;
        font-style:italic;
    }
    p.footer {
        font-size:.9em;
        text-align:center;
    }
</style>

<div data-role="page" id="images">
    
    <div data-role="header" data-position="fixed">
        <a href="<?php echo Yii::app()->baseUrl; ?>" class="ui-btn ui-btn-left ui-btn-icon-left"><span class="icon icon-home"> </span> Home</a>
        <h1><?php echo $place->placename; ?></h1>
          <div data-role="navbar">
            <ul>
              <li><a href="#images" class="ui-btn-active ui-state-persist" data-transition="fade"><span class="icon icon-image2"> </span> <span class="nav-text">Images</span></a></li>
              <li><a href="#information" data-transition="fade"><span class="icon icon-office"> </span> <span class="nav-text"><?php echo $place->placetype->singular; ?> Info</span></a></li>
              <?php if($place->placetype->singular == "Building"): ?>
              <li><a href="#rooms" data-transition="fade"><span class="icon icon-enter"> </span> <span class="nav-text">Rooms</a></span></li>
              <li><a href="#map" data-transition="fade"><span class="icon icon-map"> </span> <span class="nav-text">Map</span></a></li>
              <?php endif; ?>
              <li><a href="#classes" data-transition="fade"><span class="icon icon-list"> </span> <span class="nav-text">Classes</span></a></li>
            </ul>
          </div>
        <a href="#" class="ui-btn ui-btn-right ui-btn-icon-right"><span class="icon icon-search"> </span> Search</a>
    </div>
    
    <ul class="rslides" id="slider2">
        <?php foreach($place->images as $image): $image->make_thumb(true); ?>
            <li><a href="#"><img src="<?php echo Yii::app()->baseUrl.$image->path; ?>" alt=""></a></li>
        <?php endforeach; ?>
    </ul>
    
    <script>
    // Slideshow 2
    $("#slider2").responsiveSlides({
        auto: false,
        pager: true,
        speed: 300,
        maxwidth: 540
    });
    </script>
    
    <?php footer(); ?>

</div>

<div data-role="page" id="information">
    
    <div data-role="header" data-position="fixed">
        <a href="<?php echo Yii::app()->baseUrl; ?>" class="ui-btn ui-btn-left ui-btn-icon-left"><span class="icon icon-home"> </span> Home</a>
        <h1><?php echo $place->placename; ?></h1>
          <div data-role="navbar">
            <ul>
              <li><a href="#images" data-transition="fade"><span class="icon icon-image2"> </span> <span class="nav-text">Images</span></a></li>
              <li><a href="#information" class="ui-btn-active ui-state-persist" data-transition="fade"><span class="icon icon-office"> </span> <span class="nav-text"><?php echo $place->placetype->singular; ?> Info</span></a></li>
              <?php if($place->placetype->singular == "Building"): ?>
              <li><a href="#rooms" data-transition="fade"><span class="icon icon-enter"> </span> <span class="nav-text">Rooms</a></span></li>
              <li><a href="#anylink" data-transition="fade"><span class="icon icon-map"> </span> <span class="nav-text">Map</span></a></li>
              <?php endif; ?>
              <li><a href="#anylink" data-transition="fade"><span class="icon icon-list"> </span> <span class="nav-text">Classes</span></a></li>
            </ul>
          </div>
        <a href="#" class="ui-btn ui-btn-right ui-btn-icon-right"><span class="icon icon-search"> </span> Search</a>
    </div>
    
    <div data-role="main" class="ui-content">
        <div class="ui-grid-b">
            <?php $count=0; foreach($place->metadata->data as $index=>$data): ?>
                <?php if(($data["metatype"]!="students" and $data["metatype"]!="teachers" and $data["metatype"]!="both") or $data["value"] == "") continue; ?>
            <div class="ui-block-<?php $letter = array_slice(array("a","b","c"),$count%3,1); echo array_pop($letter); ?>">
                <div class="label">
                    <?php if(isset($data["icon"])) { ?><span class="icon <?php echo $data["icon"]; ?>"> </span><?php } echo $data["display_name"]; ?>
                </div>
                <div class="value">
                    <?php echo $data["value"]; ?>
                </div>
            </div>
            <?php  $count++; endforeach; ?>
        </div>
    </div>
    
    <?php footer(); ?>
</div>


<div data-role="page" id="rooms">
    
    <div data-role="header" data-position="fixed">
        <a href="<?php echo Yii::app()->baseUrl; ?>" class="ui-btn ui-btn-left ui-btn-icon-left"><span class="icon icon-home"> </span> Home</a>
        <h1><?php echo $place->placename; ?></h1>
          <div data-role="navbar">
            <ul>
              <li><a href="#images" data-transition="fade"><span class="icon icon-image2"> </span> <span class="nav-text">Images</span></a></li>
              <li><a href="#information" data-transition="fade"><span class="icon icon-office"> </span> <span class="nav-text"><?php echo $place->placetype->singular; ?> Info</span></a></li>
              <?php if($place->placetype->singular == "Building"): ?>
              <li><a href="#rooms" class="ui-btn-active ui-state-persist" data-transition="fade"><span class="icon icon-enter"> </span> <span class="nav-text">Rooms</a></span></li>
              <li><a href="#map" data-transition="fade"><span class="icon icon-map"> </span> <span class="nav-text">Map</span></a></li>
              <?php endif; ?>
              <li><a href="#classes" data-transition="fade"><span class="icon icon-list"> </span> <span class="nav-text">Classes</span></a></li>
            </ul>
          </div>
        <a href="#" class="ui-btn ui-btn-right ui-btn-icon-right"><span class="icon icon-search"> </span> Search</a>
    </div>
    
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
                      $thumb = $image->get_thumb();
            ?>
            <li value="<?php echo $childplace->placeid;?>">
                <a href="<?=Yii::app()->createUrl('place');?>?id=<?=$childplace->placeid;?>" data-transition="fade" >
                    <?php $childplace->render_first_image("auto","auto","thumb"); ?>
                    <?php echo $childplace->placename;?>
                    <div class="placetype"><?php echo $childplace->placetype->singular; ?></div>
                    <?php if(isset($childplace->description) and !empty($childplace->description)): ?>
                    <p><?php echo $childplace->description; ?></p>
                    <?php endif; ?>
                </a>
            </li>
            <?php endforeach; ?>
        <?php endif; ?>
        </ul>
    </div>
    
    <?php footer(); ?>
</div>


<div data-role="page" id="map">
    
    <div data-role="header" data-position="fixed">
        <a href="<?php echo Yii::app()->baseUrl; ?>" class="ui-btn ui-btn-left ui-btn-icon-left"><span class="icon icon-home"> </span> Home</a>
        <h1><?php echo $place->placename; ?></h1>
          <div data-role="navbar">
            <ul>
              <li><a href="#images" data-transition="fade"><span class="icon icon-image2"> </span> <span class="nav-text">Images</span></a></li>
              <li><a href="#information" data-transition="fade"><span class="icon icon-office"> </span> <span class="nav-text"><?php echo $place->placetype->singular; ?> Info</span></a></li>
              <?php if($place->placetype->singular == "Building"): ?>
              <li><a href="#rooms" data-transition="fade"><span class="icon icon-enter"> </span> <span class="nav-text">Rooms</a></span></li>
              <li><a href="#map" class="ui-btn-active ui-state-persist" data-transition="fade"><span class="icon icon-map"> </span> <span class="nav-text">Map</span></a></li>
              <?php endif; ?>
              <li><a href="#classes" data-transition="fade"><span class="icon icon-list"> </span> <span class="nav-text">Classes</span></a></li>
            </ul>
          </div>
        <a href="#" class="ui-btn ui-btn-right ui-btn-icon-right"><span class="icon icon-search"> </span> Search</a>
    </div>
    
    <div data-role="main" class="ui-content">
        <div id="map_canvas" style="height:250px;width:auto;"></div>
    </div>
    
    <?php footer(); ?>
</div>


<div data-role="page" id="classes">
    
    <div data-role="header" data-position="fixed">
        <a href="<?php echo Yii::app()->baseUrl; ?>" class="ui-btn ui-btn-left ui-btn-icon-left"><span class="icon icon-home"> </span> Home</a>
        <h1><?php echo $place->placename; ?></h1>
          <div data-role="navbar">
            <ul>
              <li><a href="#images" data-transition="fade"><span class="icon icon-image2"> </span> <span class="nav-text">Images</span></a></li>
              <li><a href="#information" data-transition="fade"><span class="icon icon-office"> </span> <span class="nav-text"><?php echo $place->placetype->singular; ?> Info</span></a></li>
              <?php if($place->placetype->singular == "Building"): ?>
              <li><a href="#rooms" data-transition="fade"><span class="icon icon-enter"> </span> <span class="nav-text">Rooms</a></span></li>
              <li><a href="#map" data-transition="fade"><span class="icon icon-map"> </span> <span class="nav-text">Map</span></a></li>
              <?php endif; ?>
              <li><a href="#classes" class="ui-btn-active ui-state-persist" data-transition="fade"><span class="icon icon-list"> </span> <span class="nav-text">Classes</span></a></li>
            </ul>
          </div>
        <a href="#" class="ui-btn ui-btn-right ui-btn-icon-right"><span class="icon icon-search"> </span> Search</a>
    </div>
    
    <div data-role="main" class="ui-content">
        <form method="post" id="yt-form">
            <label for="yt" class="ui-hidden-accessible">Change Term/Year: </label>
            <select name="yt" id="yt">
                <option value="20144" <?php if((isset($_REQUEST["yt"]) and $_REQUEST["yt"] == "20144") or (!isset($_REQUEST["yt"]) and isset($_SESSION["yt"]) and $_SESSION["yt"] == "20144")) : ?>selected='selected'<?php endif; ?>>Summer 2014</option>
                <option value="20137" <?php if((isset($_REQUEST["yt"]) and $_REQUEST["yt"] == "20137") or (!isset($_REQUEST["yt"]) and isset($_SESSION["yt"]) and $_SESSION["yt"] == "20137")) : ?>selected='selected'<?php endif; ?>>Fall 2013</option>
            </select>
        </form>
        <table data-role="table" data-mode="reflow" class="ui-responsive ui-shadow" id="myTable">
            <thead>
                <tr>
                    <th>Course</th>
                    <th class="calign">Section</th>
                    <th>Class</th>
                    <th class="calign">Room</th>
                    <th class="calign">Meets</th>
                </tr>
            </thead>
            <tbody>
                
            <?php if(count($classes) > 0): ?>
            <?php $count=0; foreach($classes as $class): $count++; ?>
                <?php
                # Do some processing before displaying
                $starttime  = $class["timestart"];
                $endtime    = $class["timeend"];
                $datetime = new DateTime($starttime);
                $starttime = $datetime->format("g:i a");
                $datetime = new DateTime($endtime);
                $endtime = $datetime->format("g:i a");
                
                $catalog_term = "2013-14";
                ?>
            <tr class="<?php echo ($count%2==0) ? 'odd' : 'even'; ?>">
                <td>
                    <a href="http://www.colorado.edu/catalog/<?php echo $catalog_term; ?>/courses?subject=<?php echo $class["subject"]; ?>&number=<?php echo $class["course"]; ?>" target="_blank">
                        <?php echo $class["subject"]; ?> <?php echo $class["course"]; ?>
                    </a>
                </td>
                <td class="calign"><?php echo substr("00".$class["section"],-3,3); ?></td>
                <td><?php echo $class["title"]; ?></td>
                <?php if($place->placetype->machinecode == "building" and in_array($class["building"]." ".$class["roomnum"],$childplace_names,FALSE)): ?>
                <td class="calign">
                    <a href="<?php echo Yii::app()->createUrl("place"); ?>?id=<?php echo $class["building"]." ".$class["roomnum"]; ?>" ref="external"><?php echo $class["building"]." ".$class["roomnum"]; ?></a>
                </td>
                <?php else: ?>
                <td class="calign"><?php echo $class["building"]." ".$class["roomnum"]; ?></td>
                <?php endif; ?>
                <td class="calign"><?php echo $class["meetingdays"]; ?> <?php echo @$starttime." - ".@$endtime; ?></td>
            </tr>
            <?php endforeach; ?>
            <?php else: ?>
            <tr>
                <td class="empty" colspan="7">
                    There are no classes in this <?php echo strtolower($place->placetype->singular); ?> currently for <?php echo $yt; ?>.
                </td>
            </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <?php footer(); ?>
</div>