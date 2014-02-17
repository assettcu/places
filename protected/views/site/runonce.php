<?php

set_time_limit(0);

$runonce = dirname(__FILE__)."/.runonce";
$run = file_get_contents($runonce);

# file_put_contents($runonce, 0);

# RUN ONCE PROCEDURES BELOW

$conn = Yii::app()->db;
$query = "
	SELECT		pictureid
	FROM		{{placepictures}}
	WHERE		1=1
	ORDER BY	pictureid ASC;
";
$result = $conn->createCommand($query)->queryAll();

print "<pre>";
foreach($result as $row)
{
	$picture = new PictureObj($row["pictureid"]);
	$path = explode("/",$picture->path);
	$path = array_filter($path);
	$file = array_pop($path);
	$imagedir = array_shift($path);
	$path_ = "";
	if(!empty($path)) $path_ = implode("/",$path);
	$thumbdir = getcwd()."/".$imagedir."/thumbs/".$path_;
	if(!is_dir($thumbdir)) mkdir($thumbdir,0700,true);
	$thumbpath = getcwd()."/".$imagedir."/thumbs/".$path_."/".$file;
	var_dump($thumbpath);
	if(!is_file($thumbpath)){
		$picture->crop("500",$thumbpath);
	}
}



# END OF RUN

print "Finished!";
?>