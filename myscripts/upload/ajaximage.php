<?php
include('../classes.php');


if(!empty($_GET['invoiceNo'])){

	$invoice = $_GET['invoiceNo'];
	$itemid = $_GET['itemid'];
	$path2 = "/myscripts/upload/custom-images/$invoice/";
	$path = "/myscripts/upload/custom-images/$invoice/$itemid/";
	
	$customImage = new customImages($path);

	echo $customImage->uploadCustomImage($path2);
	
	// if(isset($_POST) and $_SERVER['REQUEST_METHOD'] == "POST")
	// {
	// 	$name = $_FILES['photoimg']['name'];
	// 	$size = $_FILES['photoimg']['size'];
		
	// 	if(strlen($name))
	// 	{
	// 		list($txt, $ext) = explode(".", $name);
			
	// 		if(in_array($ext,$valid_formats))
	// 		{
	// 			if($size<(1024*1024)) // Image size max 1 MB
	// 			{
	// 				$actual_image_name = $name;
	// 				$tmp = $_FILES['photoimg']['tmp_name'];

	// 				if(createDirectory($path)){

	// 					if(move_uploaded_file($tmp, $path.$actual_image_name))
	// 					{
	// 						scandir($path);
	// 						//echo "<img src='upload/custom-images/".$actual_image_name."' class='preview'>";
	// 					}
	// 					else
	// 						echo "failed";
	// 				}
	// 				else
	// 					echo "Directory $invoice could not be created!";
	// 			}
	// 			else
	// 				echo "Image file size max 1 MB";
	// 		}
	// 		else
	// 			echo "Invalid file format..";
	// 	}
	// 	else
	// 		echo "Please select image..!";
	// 		exit;
	// }
}
else
	echo "Invoice Number is missing!";

?>