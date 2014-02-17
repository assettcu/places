<?php

$places = new Places;
$placetypes = $places->load_places_types();

if(isset($place->parentid))
{
    $place->load_parent();
    $placetype = new PlaceTypesObj($place->parent_->placetypeid);
    $placesmanager = new Places;
    $parents = $placesmanager->load_places(@$placetype->machinecode);
}
?>

<div style="float:right;width:400px;text-align:right;"><button id="return">Return to <?=$place->placename;?></button></div>
<h1>Edit Place</h1>

<div class="ui-widget-content" style="padding:6px;font-size:13px;margin-bottom:10px;">Fill out the information for the place as best as you can:</div>
<?php if(isset($error) and $error!=""): ?>
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

<form method="post" name="edit-place">
    <table class="newplace-table">
        <tr>
        	<td colspan="2">
        		<div style="padding-bottom:10px;font-size:13px;font-weight:bold;color:#0cf;">Place Details</div>
        	</td>
        </tr>
        <tr>
            <td style="padding-right:15px;"><label>Place Name <span class="required">*</span></label></td>
            <td><input type="text" name="placename" class="placename" value="<?=$place->placename;?>" /></td>
        </tr>
        <tr>
            <td style="padding-right:15px;"><label>Type of Place <span class="required">*</span></label></td>
            <td>
                <select name="placetypeid">
                    <option value="0"></option>
                    <?php if(!empty($placetypes)): ?>
                        <?php foreach($placetypes as $placetype): ?>
                            <option value="<?=$placetype->placetypeid;?>" <?=($place->placetypeid==$placetype->placetypeid)?"selected='selected'":"";?>><?=$placetype->singular;?></option>    
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
                    <?php if(isset($place->parentid)): ?>
                        <?php foreach($parents as $parent): ?>
                            <option value="<?=$parent->placeid;?>" <?=($place->parentid==$parent->placeid)?"selected='selected'":"";?>><?=$parent->placename;?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </td>
        </tr>
        <tr>
        	<td colspan="2">
        		<hr style="margin-top:10px;" />
        		<div style="padding-bottom:10px;font-size:13px;font-weight:bold;color:#0cf;">Additional Information</div>
        	</td>
        </tr>
        <?php 
        $metadata = new MetadataObj(0,"metadata_".$place->placetype->machinecode);
		$metaext = $metadata->load_all_metadata_ext();
        if(!empty($metaext)): ?>
        <?php foreach($metaext as $data): ?>
        <tr>
			<td><label><?=$data["display_name"];?></label></td>
			<?php if($data["inputtype"]=="text"): ?>
			<td><input type="text" value="<?=$place->metadata->data[$data["column_name"]]["value"];?>" name="<?=$data["column_name"];?>" /></td>
			<?php elseif($data["inputtype"]=="textarea"): ?>
			<td><textarea name="<?=$data["column_name"];?>" rows="7" cols="80" style="margin:0px;"><?=$place->metadata->data[$data["column_name"]]["value"];?></textarea></td>
			<?php else: ?>
				<?php var_dump($data); ?>
			<?php endif; ?>
		</tr>        	
       	<?php endforeach; ?>
       	<?php endif; ?>
        <tr>
            <td colspan="2" style="padding-top:15px;"><button class="cancel">Cancel</button> <button class="delete">Delete Place</button> <button>Save Place</button></td>
        </tr>
    </table>
</form>

<script>
jQuery(document).ready(function($){
	$("button").button();
	$("#return").click(function(){
		window.location = "<?=Yii::app()->createUrl('place');?>?id=<?=$place->placeid;?>";
		return false;
	});
   $("select[name=placetypeid]").change(function(){
      var $placetypeid = $(this).val();
      load_places_by_placetype($placetypeid);
   });
   $("button.cancel").click(function(){
      window.location = "<?=Yii::app()->createUrl('place');?>?id=<?=$place->placeid;?>";
      return false;
   });
   $("button.delete").click(function(){
      
        var answer = confirm("Are you sure you want to delete this place?");
        if (answer){
			$.ajax({
				"url": 		"<?=Yii::app()->createUrl('_delete_place');?>?id=<?=$place->placeid;?>",
				"success":  function(data)
				{
					if(data=="1"){
						window.location = "<?=Yii::app()->createUrl('index');?>";
						return false;
					}
					else
					{
						$(".error").html(data).parent().show('blind');
					}
				}
			});
        }
        else{
			
        }
      return false;
   });
   $("button").button();
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