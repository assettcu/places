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
