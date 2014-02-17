<?php
$places = new Places;
$placetypes = $places->load_places_types();

if(isset($_REQUEST["parentid"]))
{
	$parent = new PlacesObj($_REQUEST["parentid"]);
    $placetype = new PlaceTypesObj($parent->placetypeid);
    $placesmanager = new Places;
    $parents = $placesmanager->load_places($placetype->machinecode);
}

$flashes = new Flashes;
$flashes->render();
?>

<h1>Add a new Place</h1>

<div class="ui-widget-content" style="padding:6px;font-size:13px;margin-bottom:10px;">Fill out the information for the building as best as you can:</div>
<?php if(isset($error)): ?>
<div class="ui-state-error" style="padding:6px;font-size:13px;margin-bottom:10px;"><?=$error;?></div>
<?php endif; ?>

<style>
table.newplace-table {
	width:auto;
}
table.newplace-table tr td label {
	font-weight:bold;
}
table.newplace-table tr td input,
table.newplace-table tr td select {
	padding:3px;
	letter-spacing:1px;
	margin-left:0px;
	min-width:200px;
}
.required {
	color:#f00;
}
input.placename {
	width:300px;
}
</style>

<form method="post" name="new-building">
    <input type="hidden" value="building" name="type" />
    <table class="newplace-table">
    	<tr>
    		<td style="padding-right:15px;"><label>Place Name <span class="required">*</span></label></td>
    		<td><input type="text" name="placename" class="placename" value="<?=@$place->placename;?>" /></td>
    	</tr>
        <tr>
            <td style="padding-right:15px;"><label>Type of Place <span class="required">*</span></label></td>
            <td>
                <select name="placetypeid">
                    <option value="0"></option>
                    <?php if(!empty($placetypes)): ?>
                        <?php foreach($placetypes as $placetype): ?>
                            <option value="<?=$placetype->placetypeid;?>" <?=(@$place->placetypeid==$placetype->placetypeid)?"selected='selected'":"";?>><?=$placetype->singular;?></option>    
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </td>
        </tr>
        <tr>
            <td style="padding-right:15px;"><label>Belongs to:</label></td>
            <td>
                <select name="parentid">
                    <option value="0"></option>
                    <?php if(isset($_REQUEST["parentid"])): ?>
                        <?php foreach($parents as $parent): ?>
                            <option value="<?=$parent->placeid;?>" <?=(@$place->parentid==$parent->placeid)?"selected='selected'":"";?>><?=$parent->placename;?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="padding-top:15px;"><button id="cancel">Cancel</button> <button>Save and Add Pictures &gt;</button></td>
        </tr>
    </table>
</form>

<script>
jQuery(document).ready(function($){
  
  $("button").button();
  
  $("select[name=placetypeid]").change(function(){
     var $placetypeid = $(this).val();
     load_places_by_placetype($placetypeid);
  });
  $("#cancel").click(function(){
      <?php if(isset($_REQUEST["parentid"]) or $place->loaded): ?>
  	     window.location = "<?=Yii::app()->createUrl('place');?>?id=<?php echo (isset($_REQUEST["parentid"])) ? $place->parentid : $place->placeid; ?>";
  	  <?php else: ?>
  	     window.location = "<?php echo Yii::app()->createUrl('index'); ?>";
  	  <?php endif; ?>
  	return false;
  });
});

function load_places_by_placetype($placetypeid)
{
  $.ajax({
     "url":         "<?=Yii::app()->createUrl('_load_places_by_placetype');?>",
     "data":        "placetypeid="+$placetypeid,
     "success":     function(data)
     {
        $("select[name=parentid]").html(data);
     } 
  });
}
</script>