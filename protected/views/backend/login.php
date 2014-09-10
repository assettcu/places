<?php
/**
 * Login page
 */
$this->pageTitle=Yii::app()->name . ' - Login';

$imager = new Imager(LOCAL_IMAGE_LIBRARY."icons/lock.png");
$imager->width = "16px";
$imager->height = "16px";
$imager->attributes["title"] = "This password is passed through 128-bit AES encryption for authentication.";

$flashes = new Flashes();
$flashes->render();
?>
<h1>Authentication Needed</h1>

<div class="ui-widget-content ui-corner-all" style="padding:6px;font-size:13px;margin-bottom:10px;">Please fill out the following form with your identikey username and password:</div>


<form method="post">
    <input type="hidden" name="propertyform" />
    <table id="post-form-table" style="border-spacing:3px;">
        <tr>
            <th width="200px"><div <?php echo ($error == "username") ? 'class="error"' : ''; ?>> <span class="icon icon-user3"> </span> Identikey Username</div></th>
            <td><input type="text" name="username" id="username" value="<?php @$_REQUEST["username"]; ?>" maxlength="8" /></td>
        </tr>
        <tr>
            <th><div <?php echo ($error == "password") ? 'class="error"' : ''; ?>>  <span class="icon icon-key2"> </span> Identikey Password</div></th>
            <td>
                <input type="password" name="password" id="password" value="" /> <?php $imager->render(); ?>
            </td>
        </tr>
        <tr>
            <th></th>
            <td><button id="submit" class="submit" style="font-size:12px;">Login</button></td>
        </tr>
    </table>
</form>

<script>
jQuery(document).ready(function(){
    $("#submit").button();
    $("#submit").click(function(){
        $(this).removeClass("ui-state-hover");
        $(this).addClass("disabled");
        $(this).prop("value","Logging in...");
        $("#login-form").submit();
        return true;
    });
});
</script>