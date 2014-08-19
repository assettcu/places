<?php
/**
 * Login page
 */
$this->pageTitle=Yii::app()->name . ' - Add New Place';

$flashes = new Flashes();
$flashes->render();

function get_placetypes()
{
    $results = Yii::app()->db->createCommand()
        ->select("placetypeid")
        ->from("placetypes")
        ->queryAll();
    
    foreach($results as $row) {
        $placetypes[] = new PlaceTypesObj($row["placetypeid"]);
    }
    
    return $placetypes;
}

$placetypes = get_placetypes();
?>

<!-- Load Queue widget CSS and jQuery -->
<style type="text/css">@import url(<?php echo WEB_LIBRARY_PATH; ?>jquery/modules/plupload/js/jquery.plupload.queue/css/jquery.plupload.queue.css);</style>

<!-- Load plupload and all it's runtimes and finally the jQuery queue widget -->
<script type="text/javascript" src="<?php echo WEB_LIBRARY_PATH; ?>jquery/modules/plupload/js/plupload.full.js"></script>
<script type="text/javascript" src="<?php echo WEB_LIBRARY_PATH; ?>jquery/modules/plupload/js/jquery.plupload.queue/jquery.plupload.queue.js"></script>

<h1>New Place</h1>

<div class="ui-widget-content ui-corner-all" style="padding:6px;font-size:13px;margin-bottom:10px;">
    <span class="icon icon-upload"> </span> Add a place to the repository!
</div>


<form method="post" id="placeform">
    <input type="hidden" name="placeform-submitted" />
    <table class="fancy-table" style="border-spacing:3px;">
        <tr>
            <th width="200px"><div <?php echo ($error == "placename") ? 'class="error"' : ''; ?>>Place Name</div></th>
            <td><input type="text" name="placename" id="placename" value="<?php @$_REQUEST["placename"]; ?>" style="width:300px;" /></td>
        </tr>
        <tr>
            <th width="200px"><div <?php echo ($error == "placetype") ? 'class="error"' : ''; ?>>Place Type</div></th>
            <td>
                <select id="placetypeid" name="placetypeid">
                    <?php foreach($placetypes as $placetype) : ?>
                        <option value="<?php echo $placetype->placetypeid; ?>"><?php echo $placetype->singular; ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr>
            <th width="200px"><div <?php echo ($error == "parentid") ? 'class="error"' : ''; ?>>Parent Place</div></th>
            <td>
                <select id="parentid" name="parentid">
                    <option value="0">No Parent</option>
                </select>
            </td>
        </tr>
        <tr>
            <th width="200px"><div <?php echo ($error == "description") ? 'class="error"' : ''; ?>>Description</div></th>
            <td>
                <textarea name="description" id="description" rows="6" cols="55" style="resize:none;"></textarea>
            </td>
        </tr>
        <tr>
            <th width="200px"><div <?php echo ($error == "tags") ? 'class="error"' : ''; ?>>Tags</div></th>
            <td>
                <textarea name="tags" id="tags" rows="6" cols="55" style="resize:none;"></textarea>
            </td>
        </tr>
        <tr>
            <th width="200px"><div <?php echo ($error == "description") ? 'class="error"' : ''; ?>>Images</div></th>
            <td><div id="html5_uploader">You browser doesn't support native upload. Try Firefox 3 or Safari 4.</div></td>
        </tr>
        <tr>
            <th></th>
            <td><button id="addplace-button" class="addplace-button" style="font-size:12px;">Add New Place</button></td>
        </tr>
    </table>
</form>

<script>
jQuery(document).ready(function(){
    
    $("button").button();
    
    // All buttons do not submit forms
    $("button").click(function(){
        return false;
    });

    // Submit the new Property post
    $("button.addplace-button").click(function(e){
        // Let's submit the form
        $("button").button({"disabled":"disabled"});
        var uploader = $('#html5_uploader').pluploadQueue();

        // Files in queue upload them first
        if (uploader.files.length > 0) {
            // When all files are uploaded submit form
            uploader.bind('StateChanged', function() {
                if (uploader.files.length === (uploader.total.uploaded + uploader.total.failed)) {
                    $("form#placeform").submit();
                }
            });
                
            uploader.start();
        }
    });
    
    // Button to return home
    $("button.cancel").click(function(){
        $("button").button({"disabled":"disabled"});
        window.location = "<?php echo Yii::app()->createUrl('index'); ?>";
        return false; 
    });
    
    // File uploader
    uploader = $('#html5_uploader').pluploadQueue({
       runtimes:        'html5',
       container:       'html5_uploader',
       url:             '<?php echo Yii::app()->createUrl('ajax/uploadImages'); ?>',
       max_file_size:   '20mb',
       chunk_size:      '1mb',
       unique_names:    true,
       browse_button:   'Select Images',
       filters:         [{
            title:          "Image Files",
            extensions:     "jpg,gif,png,jpeg,bmp"
       }]
    });
    
    // Hide the upload button, the files will upload when the form is submitted
    $("a.plupload_start").hide();
    
   $("#placetypeid").on("change",function(){
        $("#parentid").load(
           "<?php echo Yii::app()->createUrl('ajax/loadparents'); ?>",
           "placetypeid="+$("#placetypeid").val(),
           function() { } 
        );
   });
   
});
</script>