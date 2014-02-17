<h1>Manage Information Types for <?=$place->placetype->name;?></h1>

<style>
table thead tr th {
	background-color:#09f;
	color:#fff;
	font-weight:bold;
	padding:5px;
	cursor: default;
}
table thead {
	margin-bottom:10px;
}
table tbody tr.input-row td {
	padding-top:10px;
	padding-bottom:10px;
}
table tbody tr td {
	font-size:13px;
	padding-top:5px;
	padding-bottom:5px;
}
div.admin-bar {
	text-align:center;
	width:100%;
	padding:0;
	margin:0;
}
div.admin-button {
	border:1px solid #69f;
	padding:3px;
	width:16px;
	border-radius:5px;
	display:inline-block;
	margin-right:2px;
	opacity: 0.5;
}
tr.actual-row {
	
}
div.spacer {
	display:inline-block;
	padding:3px;
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

<div class="ui-widget-content" style="padding:6px;font-size:13px;margin-bottom:10px;">
	Information Types are the types of information associated with the Place. For example: You may want to add how many doors a "Building" may have.<br/>
	Information Name: "Number of Doors", Machine Name: "number_of_doors", Metadata Type: "both", Input Type: "Short Text"
</div>

<form id="add-infotype-form">
	<table>
		<thead>
			<tr>
				<th></th>
				<th class="calign" title="Name of the additional information you wish to add to this place. <br/>Example: 'Number of Doors'">Information Name</th>
				<th class="calign" title="Machine name of the field in the database (on the backend)">Machine Name</th>
				<th class="calign" title="Whether this information relates more to teachers or students or both. None will hide this from the additional information table.">Metadata Type</th>
				<th class="calign" title="If the expected values of this field are short or long (required for how information is inputted)">Input Type</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<tr class="input-row">
				<td style="width:30px;"></td>
				<td class="calign" style="width:243px;">
					<input type="text" id="display_name" name="display_name" value="" />
				</td>
				<td class="calign" style="width:243px;">
					<input type="text" id="column_name" name="column_name" value="" />
				</td>
				<td class="calign" style="width:179px;">
					<select id="metatype" name="metatype">
						<option value="both">Both Teachers and Students</option>
						<option value="teachers">Teachers Only</option>
						<option value="students">Students Only</option>
						<option value="none">None</option>
					</select>
				</td>
				<td class="calign" style="width:141px;">
					<select id="inputtype" name="inputtype">
						<option value="text">Short Text</option>
						<option value="textarea">Long Text</option>
					</select>
				</td>
				<td class="calign" style="width:166px;">
					<button id="add-infotype-submit">Add Info Type</button>
				</td>
			</tr>
		</tbody>
	</table>
</form>

<table id="table-values">
	<tbody>
		<?php foreach($metadata as $metaitem): ?>
		<tr class="actual-row">
			<td class="calign mover" style="width:30px;cursor:move;min-height:36px;height:25px;">
				<?=StdLib::load_image("move.png","16px");?>
			</td>
			<td class="calign display_name" value="<?=$metaitem["display_name"];?>" style="width:243px;"><?=$metaitem["display_name"];?></td>
			<td class="calign column_name" value="<?=$metaitem["column_name"];?>"  style="width:243px;"><?=$metaitem["column_name"];?></td>
			<td class="calign metatype" value="<?=$metaitem["metatype"];?>"  style="width:179px;"><?php
				switch($metaitem["metatype"])
				{
					case "none": echo "None"; break;
					case "students": echo "Students"; break;
					case "teachers": echo "Teachers"; break;
					case "both": echo "Both Teachers and Students"; break;
					default: echo $metaitem["metatype"]; break;
				}
			?></td>
			<td class="calign inputtype" value="<?=$metaitem["inputtype"];?>"  style="width:141px;"><?php
				switch($metaitem["inputtype"])
				{
					case "textarea": echo "Long Text"; break;
					case "text": echo "Short Text"; break;
					default: echo $metaitem["inputtype"]; break;
				}
			?></td>
			<td class="calign" style="width:166px;">
				<div class="admin-bar">
					<span class="edit-buttons" style="display:none;">
					    <div class="admin-button ui-widget-header active save-changes" title="Save Infotype">
					        <?=StdLib::load_image("save.png","16px");?>
					    </div>
					    <div class="admin-button ui-widget-header active cancel-changes" title="Cancel Changes">
					        <?=StdLib::load_image("close_delete.png","16px");?>
					    </div>
				    </span>
				    
					<span class="main-buttons">
					    <div class="admin-button ui-widget-header active edit-infotype" title="Edit Infotype">
					        <?=StdLib::load_image("pencil_edit.png","16px");?>
					    </div>
					    <div class="admin-button ui-widget-header active delete-infotype" title="Delete this Infotype">
					        <?=StdLib::load_image("close_delete_2.png","16px");?>
					    </div>
					</span>
				</div>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<link rel="stylesheet" href="http<?=(isset($_SERVER["HTTPS"]) and $_SERVER["HTTPS"]=="on")?"s":"";?>://assettdev.colorado.edu/libraries/javascript/jquery/modules/tiptip/tipTip.css" type="text/css" />
<script src="http<?=(isset($_SERVER["HTTPS"]) and $_SERVER["HTTPS"]=="on")?"s":"";?>://assettdev.colorado.edu/libraries/javascript/jquery/modules/tiptip/jquery.tipTip.js"></script>
<script>
var oldcolname = "";
jQuery(document).ready(function($){
	$("button").button().click(function(){
	    return false;
	});
    
    $("div.admin-button").tipTip({
        defaultPosition:    "top",
        delay:              150,
    });
	
    $("table thead tr th").tipTip({
        defaultPosition:    "top",
        delay:              150,
    });
    
    $("table#table-values tbody").sortable({
		helper: fixHelper,
		handle: ".mover",
        update : function () { 
        	console.log("updated");
			$.each($("table#table-values tbody tr"), function(key,value){
				var columnname = $(value).find("td.column_name").html();
				$.ajax({
					"url": 		"<?=Yii::app()->createUrl('_update_infotype_sorder');?>",
					"data": 	"placetype=<?=$place->placetype->machinecode;?>&column_name="+columnname+"&sorder="+key
				});
			});
            // $("#info").load("process-sortable.php?"+order); 
        } 
	}).disableSelection();
    
    $(document).on("click",".admin-button.save-changes",function(){
		
		var row = $(this).parent().parent().parent().parent();
    	var data = row.find("input, select").serialize();
    	
    	$.ajax({
    		"url": 		"<?=Yii::app()->createUrl('_edit_infotype');?>",
    		"data": 	"placetype=<?=$place->placetype->machinecode;?>&"+data+"&old_column_name="+oldcolname,
    		"success": 	function(ret_data)
    		{
				if(ret_data == 1)
				{
					row.find("span.edit-buttons").hide();
					$("span.main-buttons").show();
					
					row.find("td.display_name").html(row.find("td.display_name input").val());
					row.find("td.display_name").attr("value",row.find("td.display_name input").val());
					row.find("td.column_name").html(row.find("td.column_name input").val());
					
					var metatypeval = row.find("td.metatype select").val();
					row.find("td.metatype").attr("value",metatypeval);
					if(metatypeval=="none") metatypeval = "None";
					if(metatypeval=="teachers") metatypeval = "Teachers";
					if(metatypeval=="students") metatypeval = "Students";
					if(metatypeval=="both") metatypeval = "Both Teachers and Students";
					row.find("td.metatype").html(metatypeval);
					
					var inputval = row.find("td.inputtype select").val();
					row.find("td.inputtype").attr("value",inputval);
					if(inputval=="text") inputval = "Short Text";
					if(inputval=="textarea") inputval = "Long Text";
					row.find("td.inputtype").html(inputval);
					
					$("table#table-values tbody").sortable("option","disabled",false);
				}
				else
				{
					alert(ret_data);
				}
    		}
    	});
    	return false;
    });
    
    $(document).on("click",".admin-button.cancel-changes",function(){
		
		var row = $(this).parent().parent().parent().parent();
		
		row.find("td.display_name").html(row.find("td.display_name").attr("value"));
		row.find("td.column_name").html(oldcolname);
		
		var metatypeval = row.find("td.metatype").attr("value");
		if(metatypeval=="none") metatypeval = "None";
		if(metatypeval=="teachers") metatypeval = "Teachers";
		if(metatypeval=="students") metatypeval = "Students";
		if(metatypeval=="both") metatypeval = "Both Teachers and Students";
		
		row.find("td.metatype").html(metatypeval);
		
		var inputval = row.find("td.inputtype").attr("value");
		if(inputval=="text") inputval = "Short Text";
		if(inputval=="textarea") inputval = "Long Text";
		row.find("td.inputtype").html(inputval);
		
		$("span.main-buttons").show();
		$(this).parent().hide();
		$("table#table-values tbody").sortable("option","disabled",false);
		return false;
    });
    
    $(document).on("click",".admin-button.edit-infotype",function(){
    	
		$("table#table-values tbody").sortable("option","disabled",true);
		
		var row = $(this).parent().parent().parent().parent();
		$(this).parent().parent().find("span.edit-buttons").show();
		$("span.main-buttons").hide();
		
		oldcolname = row.find("td.column_name").html();
		
		row.find("td.display_name").html("<input type='text' name='display_name' value='"+row.find("td.display_name").html()+"' />");
		row.find("td.column_name").html("<input type='text' name='column_name' value='"+row.find("td.column_name").html()+"' />");

		var teachers = "<option value='teachers'>Teachers</option>";
		var students = "<option value='students'>Students</option>";
		var both = "<option value='both'>Both Teachers and Students</option>";
		var none = "<option value='none'>None</option>";
		
		row.find("td.metatype").html("<select name='metatype'>"+none+teachers+students+both+"</select>");
		row.find("td.metatype select").val(row.find("td.metatype").attr("value"));
		row.find("td.inputtype").html("<select name='inputtype'><option value='text'>Short Text</option><option value='textarea'>Long Text</option></select>");
		row.find("td.inputtype select").val(row.find("td.inputtype").attr("value"));
		
		return false;
    });
    
    $(document).on("click",".admin-button.delete-infotype",function(){
		var row = $(this).parent().parent().parent().parent();
    	var answer = confirm("Are you sure you wish to delete this Infotype?");
    	var columnname = row.find("td.column_name").html();
    	if(answer)
    	{
	    	$.ajax({
	    		"url": 		"<?=Yii::app()->createUrl('_delete_infotype');?>",
	    		"data": 	"placetype=<?=$place->placetype->machinecode;?>&column_name="+columnname,
	    		"success": 	function(ret_data)
	    		{
					row.remove();
	    		}
	    	});
    	} else {
    		console.log(answer);
    	}
    	return false;
    });
    
    $("#add-infotype-submit").click(function(){
    	var data = $("#add-infotype-form").serialize();
    	var movebutton = '<?=StdLib::load_image("move.png","16px");?>';
    	$.ajax({
    		"url": 		"<?=Yii::app()->createUrl('_add_infotype');?>",
    		"data": 	"placetype=<?=$place->placetype->machinecode;?>&"+data,
    		"success": 	function(ret_data)
    		{
				$("table#table-values tbody").prepend(ret_data);
				reinit_buttons();
				row = $("tr.newappend");
				row.removeClass('newappend');
				
				row.find("td.display_name").html($("#display_name").val());
				row.find("td.column_name").html($("#column_name").val());
				
				var metatypeval = $("#metatype").val();
				row.find("td.metatype").attr("value",metatypeval);
				if(metatypeval=="none") metatypeval = "None";
				if(metatypeval=="teachers") metatypeval = "Teachers";
				if(metatypeval=="students") metatypeval = "Students";
				if(metatypeval=="both") metatypeval = "Both Teachers and Students";
				row.find("td.metatype").html(metatypeval);
				
				var inputval = $("#inputtype").val();
				row.find("td.inputtype").attr("value",inputval);
				if(inputval=="text") inputval = "Short Text";
				if(inputval=="textarea") inputval = "Long Text";
				row.find("td.inputtype").html(inputval);
				
				$("#inputtype").val("text");
				$("#metatype").val("both");
				$("#display_name").val("");
				$("#column_name").val("");
				return false;
    		}
    	});
    	return false;
    });
    
    $("#display_name").keyup(function(){
    	var $val = $(this).val();
    	$val = $val.replace(" ","_").toLowerCase().replace(/[^A-Za-z0-9\_]+/g, "");
    	$("#column_name").val($val);
        return false;
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
});
var reinit_buttons = function(){
	$("button").button().click(function(){
        return false;
	});
    $("div.admin-button").tipTip({
        defaultPosition:    "top",
        delay:              150,
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
}
var fixHelper = function(e, ui) {
	ui.children().each(function() {
		$(this).width($(this).width());
	});
	return ui;
};
</script>