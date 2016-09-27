<?php  

// filename: upload.processor.php

// first let's set some variables

// make a note of the current working directory, relative to root.
$directory_self = str_replace(basename($_SERVER['PHP_SELF']), '', $_SERVER['PHP_SELF']);

// make a note of the directory that will recieve the uploaded files
$uploadsDirectory = $_SERVER['DOCUMENT_ROOT'] . '/newsite/media/swatches/';//$_SERVER['DOCUMENT_ROOT'] . $directory_self . '../media/swatches/';

// make a note of the location of the upload form in case we need it
$uploadForm = 'http://' . $_SERVER['HTTP_HOST'] . $directory_self . 'multiple.upload.form.php';

// make a note of the location of the success page
$uploadSuccess = 'http://' . $_SERVER['HTTP_HOST'] . $directory_self . 'multiple.upload.success.php';

// name of the fieldname used for the file in the HTML form
$fieldname = 'file';

//echo'<pre>';print_r($_FILES);exit;

echo $uploadsDirectory;

// Now let's deal with the uploaded files

// possible PHP upload errors
$errors = array(1 => 'php.ini max file size exceeded', 
                2 => 'html form max file size exceeded', 
                3 => 'file upload was only partial', 
                4 => 'no file was attached');

// check the upload form was actually submitted else print form
isset($_POST['submit'])
	or error('the upload form is neaded', $uploadForm);
	
// check if any files were uploaded and if 
// so store the active $_FILES array keys
$active_keys = array();
foreach($_FILES[$fieldname]['name'] as $key => $filename)
{
	if(!empty($filename))
	{
		$active_keys[] = $key;
	}
}

// check at least one file was uploaded
count($active_keys)
	or error('No files were uploaded', $uploadForm);
		
// check for standard uploading errors
foreach($active_keys as $key)
{
	($_FILES[$fieldname]['error'][$key] == 0)
		or error($_FILES[$fieldname]['tmp_name'][$key].': '.$errors[$_FILES[$fieldname]['error'][$key]], $uploadForm);
}
	
// check that the file we are working on really was an HTTP upload
foreach($active_keys as $key)
{
	@is_uploaded_file($_FILES[$fieldname]['tmp_name'][$key])
		or error($_FILES[$fieldname]['tmp_name'][$key].' not an HTTP upload', $uploadForm);
}
	
// validation... since this is an image upload script we 
// should run a check to make sure the upload is an image
foreach($active_keys as $key)
{
	@getimagesize($_FILES[$fieldname]['tmp_name'][$key])
		or error($_FILES[$fieldname]['tmp_name'][$key].' not an image', $uploadForm);
}
	
// make a unique filename for the uploaded file and check it is 
// not taken... if it is keep trying until we find a vacant one
foreach($active_keys as $key)
{
	$now = time();


	while(file_exists($uploadFilename[$key] = $uploadsDirectory.$_FILES[$fieldname]['name'][$key]))
	{
		$now++;
		unlink($uploadsDirectory.$_FILES[$fieldname]['name'][$key]);
	}
}

// now let's move the file to its final and allocate it with the new filename
foreach($active_keys as $key)
{
	@move_uploaded_file($_FILES[$fieldname]['tmp_name'][$key], $uploadFilename[$key])
		or error('receiving directory insuffiecient permission', $uploadForm);

	chmod($uploadFilename[$key], 0777);
}
	
// If you got this far, everything has worked and the file has been successfully saved.
// We are now going to redirect the client to the success page.
header('Location: ' . $uploadSuccess . "?uploadForm=$uploadForm");

// make an error handler which will be used if the upload fails
function error($error, $location, $seconds = 5)
{
	header("Refresh: $seconds; URL=\"$location\"");
	echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"'."\n".
	'"http://www.w3.org/TR/html4/strict.dtd">'."\n\n".
	'<html lang="en">'."\n".
	'	<head>'."\n".
	'		<meta http-equiv="content-type" content="text/html; charset=iso-8859-1">'."\n\n".
	'		<link rel="stylesheet" type="text/css" href="stylesheet.css">'."\n\n".
	'	<title>Upload error</title>'."\n\n".
	'	</head>'."\n\n".
	'	<body>'."\n\n".
	'	<div id="Upload">'."\n\n".
	'		<h1>Upload failure</h1>'."\n\n".
	'		<p>An error has occured: '."\n\n".
	'		<span class="red">' . $error . '...</span>'."\n\n".
	'	 	The upload form is reloading</p>'."\n\n".
	'	 </div>'."\n\n".
	'</html>';
	exit;
} // end error handler

?>