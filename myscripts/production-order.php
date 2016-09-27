<?php 
    ini_set('display_errors', 'On');
    error_reporting(E_ALL);

    $flag = false;
    $msg = "";
    $quantity = 0;
    //$imgs ="";

    require '../app/Mage.php';
    Mage::app();

    include('classes.php');


    if (!empty($_GET))
    {


        $flag = true;
        $invoiceID = $_GET['invoiceNo'];

        $order = Mage::getModel('sales/order')->loadByIncrementId($invoiceID);
        //echo "SHIPP " . $order->getDeliveryDate();

        if($order->hasData()){

            $orderDate = Mage::app()
            ->getLocale()
            ->date(strtotime($order->getCreatedAtStoreDate()), null, null, false)
            ->toString('F H:m:s');

            $shippingDate = Mage::app()
            ->getLocale()
            ->date(strtotime($order->getDeliveryDate()), null, null, false)
            ->toString('F H:m:s');

            /********** SHIPPING INFORMATION **********/
            $orderShippingMethod = $order->getShippingDescription(); //$order->getShippingMethod();
            $shipping_address = $order->getShippingAddress(); 
            /********** SHIPPING INFORMATION::END **********/

            /********** CUSTOMER INFORMATION **********/
            $customerName = $shipping_address->getFirstname() . " " . $shipping_address->getLastname();
            $customerAddress = $shipping_address->getStreetFull() . ", " . $shipping_address->getCity() . " " . $shipping_address->getRegion() . ", " .$shipping_address->getPostcode() . ", " . $shipping_address->getCountry();
            $customerEmail =  $shipping_address->getEmail();
            $customerPhone =  $shipping_address->getTelephone();
            /********** CUSTOMER INFORMATION::END **********/


        }
        else{
            $flag = false;
            $msg = "Invoice order does not exist!";
        }
            


    }
    else
    {
        $msg = "Invoice order is missing!";
    }



function ShowOrder($incrementId)
{
   
    $order = Mage::getModel('sales/order')->loadByIncrementId($incrementId);
    global $quantity;
    //global $imgs;

    $prductCont = 0;
    $html = "";
    $accessories = "";

    foreach ($order->getAllItems() as $item) {
        $prductCont++;

        // GET CATEGORY
        $product = Mage::getModel('catalog/product')->load($item->getProductId());
        $cats = $product->getCategoryIds();
        //$_cat = Mage::getModel('catalog/category')->load($cats[0]);
        // pictuer
        //$imgs = $product->getImageUrl(); 
        
        //----------- GET Top category
        $categoryIds  = $product->getCategoryIds();
        $categories   = Mage::getResourceModel('catalog/category_collection');
        
        $mainCategory = null;
        
        foreach ($categoryIds as $id) {
                $category     = $categories->getItemById($id);
                $parents      = explode('/', $category->getPath());
                $mainCategory = (count($parents) > 2) ? $parents[2] : $parents[1];
                //$mainCategory - category after store root category.
        }

        $_cat = Mage::getModel('catalog/category')->load($mainCategory);
    
        $categoryName = $_cat->getName();
        //--------- GET Top category::END

        //GET OPTIONS
        $options = $item->getProductOptions();
        $customOptions = $options['options'];  
        $quantity = (int) $item->getQtyOrdered();
        
        if(!empty($customOptions))
        {
            //PUT LABEL AS  KEY IN ARRAY TO FRIENDLY SEARCH
            $optionArray = array();
            
            foreach ($customOptions as $option){
                $optionArray[trim($option['label'])] = $option['value'];
            }

            switch ($categoryName) {
                case 'Basketball':
                    $basketballOrder = new Basketball($optionArray, $quantity);
                    $html = $basketballOrder->getUniformDetails($categoryName, $product->getId(), $product->getName(), $prductCont);
                    $accessories = "";
                    break;

                case 'Football':
                    $footballOrder = new Football($optionArray, $quantity);
                    $html = $footballOrder->getUniformDetails($categoryName, $product->getId(), $product->getName(), $prductCont);
                    $accessories = "";
                    break;

                case 'Soccer':
                    $soccerOrder = new Soccer($optionArray, $quantity);
                    $html = $soccerOrder->getUniformDetails($categoryName, $product->getId(), $product->getName(), $prductCont);
                    $accessories = "";
                    break;

                case 'Softball':
                    $softballOrder = new Softball($optionArray, $quantity);
                    $html = $softballOrder->getUniformDetails($categoryName, $product->getId(), $product->getName(), $prductCont);
                    $accessories = $softballOrder->getAccessories();
                    break;

                case 'Baseball':
                    $baseballOrder = new Baseball($optionArray, $quantity);
                    $html = $baseballOrder->getUniformDetails($categoryName, $product->getId(), $product->getName(), $prductCont);
                    $accessories = "";
                    break;

                case 'Volleyball':
                    $volleyballOrder = new Volleyball($optionArray, $quantity);
                    $html = $volleyballOrder->getUniformDetails($categoryName, $product->getId(), $product->getName(), $prductCont);
                    $accessories = "";
                    break;
                
                default:
                    echo "CATEGORY $categoryName WAS NOT FOUND!...";
                    break;
            }

            $printingOptions = new OrderOptions($optionArray, $quantity);
            $customImages = new CustomImages("/myscripts/upload/custom-images/$incrementId/");
			$itemid= $item->getId();
            $html .=  $printingOptions->getPlayerList();
            $html .=  $printingOptions->getNotes();
            $html .=  $customImages->createCustomImageModule($incrementId,$itemid);
            $html .=  $printingOptions->getPrintingLogo();
            $html .=  $printingOptions->getPrintingNumber($incrementId);
            $html .=  $printingOptions->getNotes();
            $html .=  $accessories; 
            
           echo $html;
		   ?>
           <script language="javascript">
		   $('#photoimg<?=$itemid;?>').on('change', function(){ 
            $("#preview<?=$itemid;?>").html('');
            $("#preview<?=$itemid;?>").html('<img src="upload/loader.gif" alt="Uploading...."/>');
            
            $("#imageform<?=$itemid;?>").ajaxForm({
                target: '#preview<?=$itemid;?>'
            }).submit();
        
        });
		</script>
           <?php
        }
    }
}
   


?>
<!DOCTYPE html>
<html>
<head>
    <title>Production Order #<?php echo $_GET['invoiceNo']; ?></title>
    <meta charset="UTF-8">
    <!-- jQuery -->
    <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
    <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
    <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
    <script type="text/javascript" src="upload/scripts/jquery.form.js"></script>


    <script>

    $(document).ready(function () {

        //LIST NAME COLUMNS
        var size = $("#Playerlist > p").size();
        
        $(".column1 > p").each(function (index) {
            if (index >= size / 2) {
              //  $(this).appendTo("#column2");
            }
        });


        //UPLOAD IMAGE
        

        //DELETE IMAGE
        $(document).on("click", "img.deleteimage", function(){  
        //$('img.deleteimage').on('click', function(){
            var invoice = <?php echo $invoiceID; ?>;
            var path = $(this).attr('src');

            var file = path.split(invoice+'/');
            var fileName = file[1];

            
            
            if(confirm('Delete '+ fileName +' image ?')){
                $.ajax({
                    type: 'POST',
                    data: {
                        action: 'deleteimage',
                        fileName: fileName,
                        invoice: invoice,
                    },
                    url: 'upload/ajaxDeleteImage.php',
                    success: function(msg) {
                        alert(msg);
                        location.reload();
                    }
                });

            }
            
        });

        
    });

    //CALENDAR
    $(function() {
        $( "#shippingDate" ).datepicker();
    });


    

    

    </script>
    <!-- jQuery -->


    <style type="text/css">
       .main-container{
            padding: 0px 10;
        
       } 

       .header h3, .header img, .header p{
            position: relative;
            float: left;
            margin-right: 20px;
       }

       .customer{
            border: 1px solid;
            padding: 10;
       }

       fieldset{
            margin-bottom:10px; 
            border-color: black;
       }

       p{
        padding: 0;
        margin:0;
       }

       .myImage{
            position: relative;
            float: right;
            /*width:100px; height:150px;*/
       }

       .uniform{
            position: relative;
            float: left;
            margin: 0;
       }

       .mainTitle{
            background-color: white;
            width:auto;
            position: relative;
            top:-20px;
            left:10px;
            display: table;
       }

       .groupContainer{
            border:2px solid gray;
            margin-bottom:10px;
            padding: 10px 10px 10px 10px;
       }

       h4{
        padding: 0;
        margin: 0;
       }

       h4 span{
        color: gray;
       }

       .clear{
        clear: both;
       }

       #shippingDate{
        border:none;
        font-size: 14px;

       }

       #customLogo{
        position: relative;
        float: right;
       }

       .msgInfo{
            border-style: dashed;
            
        }

        .column1, #column2{
            float:left;width:300px;
        }

        .listPlayerSizeTitle{
            font-size: 10px;
            word-wrap: break-word;
            position: relative;
            float: left;
            font-size:10px;
            width: 50px;
        }

        .myMargin{
            margin-left: 65px;
        }

        .playerListHeaderContainer{
            display:table;
            position: relative;
            float: left; 
            border-bottom:1px solid;
        }

        #preview{
            border: 2px dashed #DEDEDE;
            float: left;
            position: relative;
        }
    </style>
</head>
<body>
    <div class="main-container">

        <div class="header">
            <img src="http://<?php echo $_SERVER['SERVER_NAME'];?>/skin/frontend/default/ma_sportshop/images/logo.png" alt="zeeni" />
            <h3>PRODUCTION ORDER</h3>
            <p><b>Invoice #:</b> <?php echo $invoiceID; ?></p>
            <p><i><?php echo $orderDate; ?></i></p>
            <div class="clear"></div>
        </div>
        
        <hr />
        
        <fieldset>
            <legend>CUSTOMER INFORMATION</legend>
            <p><b>Name: </b><?php echo $customerName; ?></p>
            <p><b>Shipping Address: </b><?php echo $customerAddress; ?></p>
            <p><b>Phone: </b><?php echo $customerPhone; ?></p>
            <p><b>Email: </b><?php echo $customerEmail; ?></p>
            <p><b>Shipping Method: </b><?php echo $orderShippingMethod; ?></p>
            <p><b>Shipping Date: </b><?php echo $shippingDate; ?><!-- <input type="text"  id="shippingDate" value="" /> --></p>
            
        </fieldset>
        
        <?php 
        if($flag)
            ShowOrder($invoiceID);
        else
            echo "<fieldset class=\"msgInfo\">
                    <legend>Message</legend>
                    <b> $msg </b>
                 </fieldset>";
            
        ?>
    </div>
    

</body>
</html>
