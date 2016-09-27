<?php 

$flag = false;

ini_set('display_errors', 'On');
error_reporting(E_ALL);

require '../app/Mage.php';
Mage::app();

if (!empty($_POST))
{
    $flag = true;
}



function displayOrder(){
    $incrementId = $_POST['orderid'];
    $order = Mage::getModel('sales/order')->loadByIncrementId($incrementId);
    
    foreach ($order->getAllItems() as $item) {
        $options = $item->getProductOptions();
        $customOptions = $options['options'];  
        if(!empty($customOptions))
        {
            foreach ($customOptions as $option)
            {      
                $optionTitle = $option['label'] . "[" . strlen($option['label'])."]";
                $optionId = $option['option_id'];
                $optionType = $option['type'];
                $optionValue = $option['value'];

                echo "<b>$optionTitle</b><br>";
                echo "$optionValue<br><br>";
            }
        }
    }

    $pdf = new Zend_Pdf();
    //$logo = "/skin/frontend/default/ma_sportshop/images/logo.png";

    
    //CREATE PAGE
    $page = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);
    $pageHeight = $page->getHeight();
    $pageWidth = $page->getWidth();
    
    /*************************** LOGO *****************************/
    $image = Zend_Pdf_Image::imageWithPath('logo.png');
    $page->drawImage($image, 25, $pageHeight - 117 , 236, $pageHeight -25);




    $pdf->pages[] = $page; // this will get reference to the first page.

    //STYLES
    $style = new Zend_Pdf_Style();
    $style->setLineColor(new Zend_Pdf_Color_Rgb(0,0,0));
    //$font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD);
    $style->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD),12);
    $page->setStyle($style);



    /*************************** HADER *****************************/
    // text
    $page->drawText('PRODUCTION ORDER',150,($pageHeight - 35)); 
    

    //rectangle
    $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.45));
    $page->setLineWidth(1);
    $page->drawLine(150, $pageHeight -45, 565, $pageHeight - 45); //top
    $page->drawLine(150, $pageHeight -45, 150, $pageHeight - 85); //left
    $page->drawLine(565, $pageHeight -45, 565, $pageHeight - 85); //right
    $page->drawLine(150, $pageHeight -85, 565, $pageHeight - 85); //bottom
                    /* x, y, width, height*/
   


    $style->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES),12);
    $page->setStyle($style);
    $page->drawText("#". $incrementId, 280, $pageHeight - 35);
    $page->drawText(date('m/d/Y'), 380, $pageHeight - 35);
    $page->drawText('Basketball', 480, $pageHeight - 35);
    //rectangle

    // $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.45));
    // $page->setLineWidth(1);
    // $page->drawLine(150, $pageHeight -25, 565, $pageHeight - 25); //top
    // $page->drawLine(150, $pageHeight -25, 150, $pageHeight - 85); //left
    // $page->drawLine(565, $pageHeight -25, 565, $pageHeight - 85); //right
    // $page->drawLine(150, $pageHeight -85, 565, $pageHeight - 85); //bottom
                    /* x, y, width, height*/
    
    // $page->setFillColor(new Zend_Pdf_Color_Html('#014495'))
    //      ->drawRectangle(30, 800, 100, 780, Zend_Pdf_Page::SHAPE_DRAW_FILL);
                        

    $pdf->render();

    $pdf->save('test.pdf');

    /*  
    $pdf = Zend_Pdf::load('INVOICE.pdf');
    $pdf->setTextField('invoice', 'caca');
    //$pdf->setTextField('invoiceDate', 'caca');

    $pdf->save('outputfile.pdf');
    */
}


?>
<html>
<head>
<title>cac</title>
</head>
<body>
<form action="myorder.php" method="post">
order id: <input type="text" name="orderid">
<input type="submit">
</form>
<hr>
<?php if($flag)
displayOrder();
?>
</body>
</html>
