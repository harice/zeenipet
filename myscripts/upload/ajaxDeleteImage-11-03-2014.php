<?php 

	if($_POST["action"]=="deleteimage")
    {
        $fileName = $_POST['fileName'];
        $invoice = $_POST['invoice'];
        //$imagefileend = '../images'.end(explode('images',$imagefile)); //This will get me the path to the image ../images/1/whatever.jpg without the domain which is the correct path to the file. I tried that path directly and it deleted the file. 
        // $imagethumbend = '../images'.end(explode('images',$imagethumb));

        // unlink($imagefileend);
        
        $path = $_SERVER['DOCUMENT_ROOT'] . "/newsite/myscripts/upload/custom-images/$invoice/$fileName";
        if(unlink($path))
        	echo "Image was deleted!";
        else
        	echo "The Image was not deleted!";
    }

?>