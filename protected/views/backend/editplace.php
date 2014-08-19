<?php
$flashes= new Flashes();
$flashes->render();
?>

<!-- Load Queue widget CSS and jQuery -->
<style type="text/css">@import url(<?php echo WEB_LIBRARY_PATH; ?>jquery/modules/plupload/js/jquery.plupload.queue/css/jquery.plupload.queue.css);</style>

<!-- Load plupload and all it's runtimes and finally the jQuery queue widget -->
<script type="text/javascript" src="<?php echo WEB_LIBRARY_PATH; ?>jquery/modules/plupload/js/plupload.full.js"></script>
<script type="text/javascript" src="<?php echo WEB_LIBRARY_PATH; ?>jquery/modules/plupload/js/jquery.plupload.queue/jquery.plupload.queue.js"></script>

<form method="post" id="editplace">
    <input type="hidden" name="editplace-form" value="true" />
    <div id="editplace-buttons" class="sticky" sticky="85">
        <div class="button-container">
            <button id="viewplace">View Place</button>
            <button id="saveplace">Save Place</button><br/>
            <button id="cancel">&lt; Go Back</button>
        </div>
    </div>
    <table class="fancy-table" id="editplace" style="border-spacing:3px;">
        <tbody>
            <tr>
                <td class="header" colspan="2">
                    Place Information
                </td>
            </tr>
            <tr>
                <th width="170px">
                    <div>Place ID</div>
                </th>
                <td>
                    <div><?php echo $place->placeid; ?></div>
                </td>
            </tr>
            <tr>
                <th>
                    <div>Place Name</div>
                </th>
                <td>
                    <div>
                        <input type="text" name="placename" id="placename" value="<?php echo $place->placename; ?>" />
                    </div>
                </td>
            </tr>
            <tr>
                <th>
                    <div>Parent Place</div>
                </th>
                <td>
                    <div>
                        <?php echo $place->parent_->placename; ?> 
                        (<span class="placetype-<?php echo $place->parent_->placetype->machinecode; ?>"><?php echo $place->parent_->placetype->singular; ?></span>)
                    </div>
                </td>
            </tr>
            <tr>
                <th>
                    <div>Place Type</div>
                </th>
                <td>
                    <div>
                        <span class="placetype-<?php echo $place->placetype->machinecode; ?>"><?php echo $place->placetype->singular; ?></span>
                    </div>
                </td>
            </tr>
            <tr>
                <th>
                    <div>Description</div>
                </th>
                <td>
                    <div>
                        <textarea name="description" id="description" rows="5" cols="50"><?php echo $place->description; ?></textarea>
                    </div>
                </td>
            </tr>
            <tr>
                <th>
                    <div>Tags</div>
                </th>
                <td>
                    <div>
                        <textarea name="tags" id="tags" rows="2" cols="50"><?php echo $place->tags; ?></textarea>
                    </div>
                </td>
            </tr>
            <tr>
                <th>
                    <div>Date Last Modified</div>
                </th>
                <td>
                    <div>
                        <span class="date"><?php echo StdLib::format_date($place->date_modified, "normal"); ?></span>
                    </div>
                </td>
            </tr>
            <tr>
                <th>
                    <div>Date Created</div>
                </th>
                <td>
                    <div>
                        <span class="date"><?php echo StdLib::format_date($place->date_created, "normal"); ?></span>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="spacer">
                    <hr />
                </td>
            </tr>
            <tr>
                <td class="header" colspan="2">
                    Place Metadata
                </td>
            </tr>
            <?php foreach($place->metadata->data as $index=>$metadata): ?>
                <?php if($metadata["display_name"] === false) continue; ?>
            <tr>
                <th>
                    <div>
                        <?php echo $metadata["display_name"]; ?>
                    </div>
                </th>
                <td>
                    <div class="metadata-fields">
                        <?php if($metadata["inputtype"] == "textarea"): ?>
                        <textarea name="<?php echo $index; ?>" id="<?php echo $index; ?>" rows="6" cols="50"><?php echo $metadata["value"]; ?></textarea>
                        <?php elseif($metadata["inputtype"] == "text"): ?>
                        <input type="text" name="<?php echo $index; ?>" id="<?php echo $index; ?>" value="<?php echo $metadata["value"]; ?>" />
                        <?php endif; ?>
                        <?php if($metadata["metatype"] == "none"): ?>(<span class="date">hidden</span>)<?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="2" class="spacer">
                    <hr />
                </td>
            </tr>
            <tr>
                <td class="header" colspan="2">
                    Place Pictures
                </td>
            </tr>
            <tr>
                <th>
                    <div>Current Pictures</div>
                </th>
                <td>
                    <ul class="rig columns-4">
                    <?php foreach($place->images as $picture): ?>
                        <li style="cursor:default;"><?php $picture->render_boxfit("150","150"); ?><a href="remove" class="remove-picture" id="pictureid-<?php echo $picture->pictureid; ?>"><span class="icon icon-remove"> </span> remove</a></li>
                    <?php endforeach; ?>
                    </ul>
                </td>
            </tr>
            <tr>
                <th>
                    <div>Add New Pictures</div>
                </th>
                <td>
                    <div>
                        <div id="html5_uploader">Your browser does not support HTML 5 Image Uploader.</div>
                    </div>
                </td>
        </tbody>
    </table>
</form>

<script>
jQuery(document).ready(function($){
   $("button").button();
   $("button").click(function(){ return false; }); 

    // Submit the new Property post
    $("button#saveplace").click(function(e){
        // Let's submit the form
        $("button").button({"disabled":"disabled"});
        var uploader = $('#html5_uploader').pluploadQueue();

        // Files in queue upload them first
        if (uploader.files.length > 0) {
            // When all files are uploaded submit form
            uploader.bind('StateChanged', function() {
                if (uploader.files.length === (uploader.total.uploaded + uploader.total.failed)) {
                    $("form#editplace").submit();
                }
            });
                
            uploader.start();
        }
    });
    
   $("button#cancel").click(function(){
       window.location = "<?php echo Yii::app()->createUrl('backend/manageplaces'); ?>";
       return false;
   });
   
    $("a.remove-picture").click(function(){
       var $id = $(this).attr("id").replace("pictureid-","");
       var $image = $(this).parent();
       $.ajax({
           url: "<?php echo Yii::app()->createUrl('ajax/removepicture'); ?>",
           data: "pictureid="+$id,
           success: function() {
               $image.fadeOut(400, function(){
                   $(this).remove();
               });
           }
       });
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
});
</script>