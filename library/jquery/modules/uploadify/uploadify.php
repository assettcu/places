<?php
/*
Uploadify
Copyright (c) 2012 Reactive Apps, Ronnie Garcia
Released under the MIT License <http://www.opensource.org/licenses/mit-license.php> 
*/

// Define a destination
$targetFolder = getcwd().'/../../images'; // Relative to the root

if (!empty($_FILES)) {
    $location = $_REQUEST["location"];
    $targetFolder .= "/".$location;
    
    if(!is_dir($targetFolder)) mkdir($targetFolder);
    
    $localfolder = "/images/".$location."/".$_FILES["Filedata"]["name"];
    
	$tempFile = $_FILES['Filedata']['tmp_name'];
	$targetFile = rtrim($targetFolder,'/') . '/' . $_FILES['Filedata']['name'];
    
	// Validate the file type
	$fileTypes = array('jpg','jpeg','gif','png'); // File extensions
	$fileParts = pathinfo($_FILES['Filedata']['name']);
	
	if (in_array(strtolower($fileParts['extension']),$fileTypes)) {
		move_uploaded_file($tempFile,$targetFile);
		echo $localfolder;
	} else {
		echo 'Invalid file type.';
	}
}
?>