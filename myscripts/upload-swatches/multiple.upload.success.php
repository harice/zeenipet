<?php
//$uploadForm = 'http://' . $_SERVER['HTTP_HOST'] . $directory_self . 'multiple.upload.form.php';
//header("Refresh: 3; URL=\"$uploadForm\"");
header('Refresh: 3;' . $_GET['uploadForm']);

// filename: upload.success.php

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
"http://www.w3.org/TR/html4/strict.dtd">

<html lang="en">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
		
		<link rel="stylesheet" type="text/css" href="stylesheet.css">
		
		<title>Successful upload</title>
	
	</head>
	
	<body>
	
		<div id="Upload">
			<h1>File upload</h1>
			<p>Congratulations! Your file uploads were successful</p>
		</div>
	
	</body>

</html>