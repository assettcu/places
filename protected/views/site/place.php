<<<<<<< HEAD
<style>
.entry {
    float:left;
    width:100%;
    margin:0;
    padding:0;
}
.entry h2 {
    float: right;
    width: 85%;
}
.entry h3.nav {
    float: left;
    margin-top: 0.72727em;
    width:5.5em;
    margin-right:0.5em;
}
.entry h3.nav ul {
    margin:0;
    font-size:18px;
    list-style:none;
    display:block;
    padding:0;
}
.entry h3.nav ul li {
    display:block;
    margin-left: 0.2em;
    float:none;
    margin-bottom:0;
    line-height: 1.5em;
    text-align:right;
}
.entry h3.nav ul li a {
    display:block;
    text-decoration:none;
    color:#888;
    width:auto!important;
    padding:.25em .5em .25em 0;
    border-right:2px solid #888;
}
.entry h3.nav ul li a:hover {
    text-decoration:none;
    color:#555;
    background-color:#f0f0f0;
    border-right:2px solid #09f;
}
.entry .content {
    float:right;
    width:85%;
    margin:0;
    padding:0;
}
.entry .images {
    float:left;
    padding:0em;
    width:auto;
    margin-bottom:60px;
}

.entry .meta {
    float: right;
    width: 24%;
    margin-top: 0.5em;
}

div.information ul {
    list-style:none;
    padding:0;
    margin:0;
    font-size:.8em;
}
div.information ul li {
    width: 16em;
    float:left;
    margin-right:0.5em;
    margin-bottom:0.5em;
}
div.information ul li div.label {
    font-weight:bold;
    font-style:italic;
    color:#555;
}
div.information ul li div.value {
    padding-left:0.9em;
    padding-top:0.25em;
}
</style>

<?php
$place = new PlacesObj($_REQUEST["id"]);
?>
<div class="entry">
    <h2><?php echo $place->placename; ?></h2>
    <h3 class="nav">
        <ul>
            <li><a href="#">Images</a></li>
            <li><a href="#">Classrooms</a></li>
            <li><a href="#">Labs</a></li>
            <li><a href="#">Google Map</a></li>
        </ul>
    </h3>
    <div class="content">
        <div class="images">
            Almost every new client these days wants a mobile version of their website. It’s practically essential after all: one design for the BlackBerry, another for the iPhone, the iPad, netbook, Kindle — and all screen resolutions must be compatible, too. In the next five years, we’ll likely need to design for a number of additional inventions. When will the madness stop? It won’t, of course.
In the field of Web design and development, we’re quickly getting to the point of being unable to keep up with the endless new resolutions and devices. For many websites, creating a website version for each resolution and new device would be impossible, or at least impractical. Should we just suffer the consequences of losing visitors from one device, for the benefit of gaining visitors from another? Or is there another option?
        </div>
        <br class="clear" />
        <?php var_dump($place->metadata); ?>
        <div class="information">
            <ul>
                <li>
                    <div class="label">Smartroom Capable</div>
                    <div class="value">&raquo; Yes</div>
                </li>
                <li>
                    <div class="label">Smartroom Capable</div>
                    <div class="value">&raquo; Yes</div>
                </li>
                <li>
                    <div class="label">Smartroom Capable</div>
                    <div class="value">&raquo; Yes</div>
                </li>
                <li>
                    <div class="label">Smartroom Capable</div>
                    <div class="value">&raquo; Yes</div>
                </li>
                <li>
                    <div class="label">Smartroom Capable</div>
                    <div class="value">&raquo; Yes</div>
                </li>
                <li>
                    <div class="label">Smartroom Capable</div>
                    <div class="value">&raquo; Yes</div>
                </li>
            </ul>
        </div>
    </div>
</div>
=======
<?php
$placeid = $_REQUEST["id"];

$place = new PlacesObj($placeid);
if(!$place->loaded){
	$this->redirect("error");
	exit;
}
$place->load_metadata();

$place->load_pictures();
$classrooms = $place->get_children("classroom");
$labs       = $place->get_children("lab");

$table_width = 1130;
$cols = 5;
$colsize = $table_width / $cols - 10;
$count=0;

switch($place->placetype->machinecode)
{
	case "building": $image = "school"; break;
	case "classroom": $image = "chalkboard"; break;
    case "lab": $image = "lab"; break;
	default: $image = "school"; break;
}
$marker = StdLib::load_image_source($image);
$marker = StdLib::make_path_local($marker);
$icon = new Imager($marker);
$icon->width = "36px";
$icon->height = "36px";
$icon->styles["float"] = "left";
$icon->styles["margin-top"] = "-6px";
$icon->styles["margin-right"] = "6px";

$list_height = $icon->height - 28;

$place->load_parent();
?>

<?php 
if(0):
if(Yii::app()->user->name == "carneymo") :

$place->render_pictures(true);

echo "<pre>";
var_dump($place);



endif;
endif;
?>
<div class="breadcrumb-bar ui-widget-header" style="font-size:13px;font-weight:normal;">
   &gt; <a href="<?=Yii::app()->baseUrl;?>/">Buildings</a> &gt; 
   <?php if($place->parent_->placetype->machinecode=="building") echo "<a href='".Yii::app()->createUrl('place')."?id=".$place->parent_->placeid."'>".$place->parent_->placename."</a> &gt; "; ?> <?=$place->placename;?>
</div>

<style>
img.slideshow-img {
	display:none;
}
div.admin-bar {
    float:right;
    padding:0;
    margin:0;
}
div.admin-button {
    border:1px solid #69f;
    padding:3px;
    width:20px;
    border-radius:5px;
    display:inline-block;
    opacity:0.5;
}
.active {
    cursor:pointer;
}
.disabled {
    cursor:default;
}
.selected {
    cursor:pointer;
}
</style>
<?php if(isset(Yii::app()->user) and !Yii::app()->user->isGuest): ?>
<div class="admin-bar">
    <div class="admin-button ui-widget-header active add-pictures" title="Manage Pictures">
        <?=StdLib::load_image("bag","20px","20px");?>
    </div>
    <div class="admin-button ui-widget-header active edit-place" title="Edit <?=$place->placetype->singular;?> Information">
        <?=StdLib::load_image("pencil_edit","20px","20px");?>
    </div>
    <div class="admin-button ui-widget-header active reorder" title="Configure <?=$place->placetype->singular;?>">
        <?=StdLib::load_image("options","20px","20px");?>
    </div>
</div>
<?php endif; ?>

<h1 style="margin-top:10px;margin-left:5px;">
	<?=$icon->render(); ?> <?=$place->placename;?> <span style="font-weight:normal;font-size:12px;color:#ccc;">(<?=$place->placetype->singular;?>)</span>
	<span class="date-taken" style="font-size:15px;color:#89f;">Last Updated: <?=date("F jS, Y",strtotime($place->date_modified));?></span>
</h1>

<?php 
$modules = array();
switch($place->placetype->machinecode)
{
    case "building": $modules = array("details","classrooms","labs","googlemap"); break;
    case "classroom": $modules = array("details"); break;
    case "lab": $modules = array("details"); break;
    default: $modules = array("details");
}
?>

<div class="tablinks">
    <?php if(in_array("details",$modules)): ?>
	<a href="#details" id="details-link" class="tablink anchor-link">Details</a> |
	<?php endif; ?>
    <?php if(in_array("classrooms",$modules)): ?>
    <a href="#classrooms" id="places-link" class="tablink anchor-link">Classrooms</a> |
    <?php endif; ?>
    <?php if(in_array("labs",$modules)): ?>
    <a href="#labs" id="labs-link" class="tablink anchor-link">Labs</a> |
    <?php endif; ?>
    <?php if(in_array("googlemap",$modules)): ?>
    <a href="#googlemap" id="googlemap-link" class="tablink anchor-link">Google Map</a>
    <?php endif; ?>
</div>

<style>
div#details div.header,
div#classrooms div.header,
div#googlemap div.header,
div#labs div.header {
    padding:6px;
    width:97%;
    font-size:15px;
    font-weight:bold;
    margin-bottom:10px;
}
div#details,
div#googlemap,
div#classrooms,
div#labs {
    margin-bottom:25px;
    min-height:50px;
}
</style>

<div id="details">
    <div class="ui-widget-header header">Details</div>
	<div class="obj-container" style="width:95%;margin:auto;margin-bottom:30px;">
		
		<div style="width:100%;text-align:right;margin-bottom:5px;">
			View information as for:
			<select>
				<option value="both">Both Students and Teachers</option>
				<option value="students">Students</option>
				<option value="teachers">Teachers</option>
			</select>
		</div>
		
		<div class="" style="width:600px;float:left;">
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
		
		<div class="" style="float:left;border-left:1px solid #ccc;width:450px;padding-left:10px;margin-right:10px;">
			<div class="ui-widget-header" style="padding:4px;font-size:15px;">
				<?=$place->placetype->singular;?> Information
			</div>
			<?php if(isset(Yii::app()->user) and !Yii::app()->user->isGuest): ?>
			<div class="admin-bar">
			    <div class="admin-button ui-widget-header active edit-place" style="margin-bottom:4px;margin-top:2px;margin-left:3px;" title="Edit <?=$place->placetype->singular;?> Information">
			        <?=StdLib::load_image("pencil_edit","20px","20px");?>
			    </div><br/>
				<div class="admin-button ui-widget-header active add-infotype" style="margin-bottom:4px;margin-top:2px;margin-left:3px;"  title="Manage Information Fields">
					<?=StdLib::load_image("options_2","20px","20px");?>
				</div><br/>
			</div>
			<?php endif; ?>
			<div class="rooms-list" style="overflow-y:scroll;height:370px;">
				<?php foreach($place->metadata->data as $index=>$data): ?>
				<?php if($data["metatype"]!="students" and $data["metatype"]!="teachers" and $data["metatype"]!="both") continue; ?>
					<div class="<?=$data["metatype"];?>">
						<div class="info-box" style="padding:7px;padding-right:0px;border-bottom:1px solid #cdf;background-color:#09f;color:#fff;font-weight:bold;">
							<?=$data["display_name"];?>
						</div>
						<div class="room-link" style="padding:7px;padding-right:0px;border-bottom:1px solid #cdf;">
							<?=$data["value"];?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<br class="clear" />
	</div>
</div>

<?php if(in_array("classrooms",$modules)): ?>
<div id="classrooms">
    <div class="ui-widget-header header">Classrooms</div>
	<?php if(isset(Yii::app()->user) and !Yii::app()->user->isGuest): ?>
	<div class="admin-bar" style="margin-right:25px;">
	    <div class="admin-button ui-widget-header active add-classroom" title="Add a Classroom">
	        <?=StdLib::load_image("plus_2","20px");?>
	    </div>
	    <div class="admin-button ui-widget-header active reorder disabled" title="Reorder Classrooms">
	        <?=StdLib::load_image("wizard","20px");?>
	    </div>
	</div>
	<?php endif; ?>
    <?php if(empty($classrooms)): ?>
        <div style="font-size:18px;margin-left:25px;">Classrooms for this building have not been added yet. Please stay tuned.</div>
    <?php else: ?>
    <table class="imgtable">
        <?php $count = 0; foreach($classrooms as $classroom):  ?>
            <?php if($count%$cols==0): ?>
            <tr>
            <?php endif; ?>
                <td width="<?=100/$cols;?>%" height="150px" placeid="<?=$classroom->placeid;?>" class="img-holder">
                	<div class="img-container" style="position:relative;margin:0;padding:0;width:100%;height:100%;">
	                    <?php $classroom->render_first_image("200px","133px","thumb"); ?>
	                    <div class="image"><?=$classroom->placename;?></div>
                    </div>
                </td>
                <?php if(count($classrooms)==$count+1 and count($classrooms)<$cols): ?>
                <?php for($count;$count<$cols;$count++): ?>
                    <td class="empty">&nbsp;</td>
                <?php endfor; ?>
                <?php endif; ?>
            <?php if($count%$cols==$cols-1): ?>
            </tr>
            <?php endif; ?>
        <?php $count++; endforeach; ?>
    </table>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php if(in_array("labs",$modules)): ?>
<div id="labs">
    <div class="ui-widget-header header">Labs</div>
    <?php if(isset(Yii::app()->user) and !Yii::app()->user->isGuest): ?>
    <div class="admin-bar" style="margin-right:25px;">
        <div class="admin-button ui-widget-header active add-lab" title="Add a Lab">
            <?=StdLib::load_image("plus_2","20px");?>
        </div>
        <div class="admin-button ui-widget-header active reorder disabled" title="Reorder Labs">
            <?=StdLib::load_image("wizard","20px");?>
        </div>
    </div>
    <?php endif; ?>
    <?php if(empty($labs)): ?>
        <div style="font-size:18px;margin-left:25px;">Labs for this building have not been added yet. Please stay tuned.</div>
    <?php else: ?>
    <table class="imgtable">
        <?php $count = 0; foreach($labs as $lab): ?>
            <?php if($count%$cols==0): ?>
            <tr>
            <?php endif; ?>
                <td width="<?=100/$cols;?>%" height="150px" placeid="<?=$lab->placeid;?>" class="img-holder">
                    <div class="img-container" style="position:relative;margin:0;padding:0;width:100%;height:100%;">
                        <?php $lab->render_first_image("200px","133px","thumb"); ?>
                        <div class="image"><?=$lab->placename;?></div>
                    </div>
                </td>
                <?php if(count($labs)==$count+1 and count($labs)<$cols): ?>
                <?php for($count;$count<$cols;$count++): ?>
                    <td class="empty">&nbsp;</td>
                <?php endfor; ?>
                <?php endif; ?>
            <?php if($count%$cols==$cols-1): ?>
            </tr>
            <?php endif; ?>
        <?php $count++; endforeach; ?>
    </table>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php if(in_array("googlemap",$modules)): ?>
<div id="googlemap">
    <div class="ui-widget-header header">Google Map</div>
	<?php if(isset(Yii::app()->user) and !Yii::app()->user->isGuest): ?>
	<div class="admin-bar" style="margin-right:25px;">
		<?php if(!isset($place->metadata->googlemap) or $place->metadata->googlemap==""): ?>
	    <div class="admin-button ui-widget-header active add-googlemap" title="Add a Google Map">
	        <?=StdLib::load_image("plus_2","20px");?>
	    </div>
	    <?php else: ?>
	    <div class="admin-button ui-widget-header active edit-googlemap" title="Edit Google Map">
	        <?=StdLib::load_image("pencil_edit","20px");?>
	    </div>
	    <?php endif; ?>
	</div>
	<?php endif; ?>
	<?php if(!isset($place->metadata->googlemap) or $place->metadata->googlemap==""): ?>
        <div style="font-size:18px;margin-left:25px;">There is no google map for this building yet.</div>
	<?php else: ?>
	<div style="text-align:center"><?=$place->metadata->googlemap;?></div>
	<?php endif; ?>
</div>
<?php endif; ?>


<div id="googlemap-dialog" title="Google Map" style="display:none;">
	Load your google map in Google Maps then click on "embed".<br/>
	Copy and paste the code in this box:<br/>
	<textarea id="googlemap-textarea" name="googlemap" rows="9" cols="49"><?=@$place->metadata->googlemap;?></textarea>
</div>

<script src="http<?=(isset($_SERVER["HTTPS"]) and $_SERVER["HTTPS"]=="on")?"s":"";?>://compass.colorado.edu/libraries/javascript/jquery/modules/galleria/galleria-1.2.8.js"></script>
<script src="http<?=(isset($_SERVER["HTTPS"]) and $_SERVER["HTTPS"]=="on")?"s":"";?>://compass.colorado.edu/libraries/javascript/jquery/modules/jnotes/js/jquery-notes_1.0.8_min.js"></script>

<link type="text/css" rel="stylesheet" href="http<?=(isset($_SERVER["HTTPS"]) and $_SERVER["HTTPS"]=="on")?"s":"";?>://compass.colorado.edu/libraries/javascript/jquery/modules/galleria/themes/classic/galleria.classic.css">
<link type="text/css" rel="stylesheet" href="http<?=(isset($_SERVER["HTTPS"]) and $_SERVER["HTTPS"]=="on")?"s":"";?>://compass.colorado.edu/libraries/javascript/jquery/modules/jnotes/css/style.css">

<script>
Galleria.loadTheme('http<?=(isset($_SERVER["HTTPS"]) and $_SERVER["HTTPS"]=="on")?"s":"";?>://compass.colorado.edu/libraries/javascript/jquery/modules/galleria/themes/classic/galleria.classic.min.js');
jQuery(document).ready(function($){
	
	init();
	
	$("#googlemap-dialog").dialog({
		"autoOpen": 		false,
		"width": 			450,
		"height": 			300,
		"modal":   			true,
		"resizable": 		false,
		"draggable": 		false,
		"buttons":  		{
			"Cancel": 		function()
			{
				$("#googlemap-textarea").html("");
				$("#googlemap-dialog").dialog("close");
			},
			"Save Google Map": 		function()
			{
				$.ajax({
					"url": 		"<?=Yii::app()->createUrl('_save_google_map');?>",
					"data": 	"id=<?=$place->placeid;?>&googlemap="+escape($("#googlemap-textarea").val()),
					"success":  function(data)
					{
						$("#googlemap-dialog").dialog("close");
						window.location.reload();
					}
				});
			}
		}
	});
	
	$("td.img-holder").hover(
		function(){
			$(this).find("img").stop().css("opacity",1);
		},
		function(){
			$(this).find("img").stop().css("opacity",0.5);
		}
	);
	
	$("td.img-holder").click(function(){
		if($(this).is(".empty")) return false;
		var loc = $(this).attr("placeid");
		window.location = "<?=Yii::app()->createUrl('place');?>?id="+loc;
		return false;
	});
	
	$("select").change(function(){
		var val = $(this).val();
		if(val=="students")
		{
			$("div.teachers").hide();
			$("div.students").show();
		}
		if(val=="teachers")
		{
			$("div.students").hide();
			$("div.teachers").show();
		}
		if(val=="both")
		{
			$("div.students:hidden").show();
			$("div.teachers:hidden").show();
		}
	});

    $("div.admin-button.active").hover(
        function(){
            if($(this).is(".disabled")) return false;
            $(this).stop().fadeTo('fast',1);
        },
        function(){
            if($(this).is(".disabled")) return false;
            $(this).stop().fadeTo('fast',0.5);
        }
    );
    
    $("div.admin-button").tipTip({
        defaultPosition:    "top",
        delay:              150,
    });
    
    $(".admin-button.edit-googlemap").click(function(){
    	$("#googlemap-dialog").dialog("option","title","Edit Google Map for <?=$place->placename;?>");
		$("#googlemap-dialog").dialog("open");
		return false;
    });
    
    $(".admin-button.add-googlemap").click(function(){
    	$("#googlemap-dialog").dialog("option","title","Add Google Map to <?=$place->placename;?>");
		$("#googlemap-dialog").dialog("open");
		return false;
    });
    
    $(".admin-button.edit-place").click(function(){
        window.location = "<?=Yii::app()->createUrl('editplace');?>?id=<?=$place->placeid;?>";
        return false;
    });
    
    $(".admin-button.add-pictures").click(function(){
        window.location = "<?=Yii::app()->createUrl('pictures');?>?id=<?=$place->placeid;?>";
        return false;
    });
    
    $(".admin-button.add-classroom").click(function(){
        window.location = "<?=Yii::app()->createUrl('addplace');?>?parentid=<?=$place->placeid;?>&placetype=classroom";
        return false;
    });
    
    $(".admin-button.add-lab").click(function(){
        window.location = "<?=Yii::app()->createUrl('addplace');?>?parentid=<?=$place->placeid;?>&placetype=lab";
        return false;
    });
    
    $(".admin-button.add-infotype").click(function(){
        window.open("<?=Yii::app()->createUrl('newinfotype');?>?id=<?=$place->placeid;?>");
        return false;
    });
    
	$(".anchor-link").click(function(event){       
        event.preventDefault();
        $('html,body').animate({scrollTop:$(this.hash).offset().top}, 500);
		return false;
	});
});
function init()
{
	Galleria.run("#galleria", {
		lightbox: true,
		dummy: "<?php echo WEB_IMAGE_LIBRARY.'no_image_available.png'; ?>",
        extend: function() {
            var gallery = this; // "this" is the gallery instance
            console.log(gallery); // call the play method
            $('#download-image').click(function() {
            });
        }
	});
}
</script>


>>>>>>> bf5750938e5a9043950a9246ab69b3afbc9ad332
