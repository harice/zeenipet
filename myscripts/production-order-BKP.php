<?php 
    ini_set('display_errors', 'On');
    error_reporting(E_ALL);

    $flag = false;
    $msg = "";

    require '../app/Mage.php';
    Mage::app();

    if (!empty($_GET))
    {
        $flag = true;
        $invoiceID = $_GET['invoiceNo'];

        $order = Mage::getModel('sales/order')->loadByIncrementId($invoiceID);

        if($order->hasData()){

            $orderDate = Mage::app()
            ->getLocale()
            ->date(strtotime($order->getCreatedAtStoreDate()), null, null, false)
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


/********** CLASSES **********/
class OrderOptions
{
    public $optionArray;

    function __construct($optionArray)
    {
        $this->optionArray = $optionArray;
    }

    public function setPlayerListHeader($jersey, $shorts, $name, $number){
        $html .= "<div class=\"playerListHeaderContainer\" >";
        $html .= "<p class=\"listPlayerSizeTitle\">$jersey</p>";
        $html .= "<p class=\"listPlayerSizeTitle\">$shorts</p>";
        $html .= "<p class=\"listPlayerSizeTitle\">$name</p>";
        $html .= "<p class=\"listPlayerSizeTitle myMargin\">$number</p>";
        $html .= "<div class=\"clear\"></div>";
        $html .= "</div>";

        $html .= "<div class=\"playerListHeaderContainer\" style=\"margin-left:35px;\" >";
        $html .= "<p class=\"listPlayerSizeTitle\">$jersey</p>";
        $html .= "<p class=\"listPlayerSizeTitle\">$shorts</p>";
        $html .= "<p class=\"listPlayerSizeTitle\">$name</p>";
        $html .= "<p class=\"listPlayerSizeTitle myMargin\">$number</p>";
        $html .= "<div class=\"clear\"></div>";
        $html .= "</div>";

        $html .= "<div class=\"clear\"></div>";


        return $html;
    }

    public function getPlayerList(){
        $html = "<div class=\"groupContainer\">";
        $html .= "<p class=\"mainTitle\">NAME LIST</p>";
        $html .= "<b>How Many Names to Print?</b> " . $this->optionArray['How Many Names to Print?'];
        $html .= "<br><br>";

        $html .= $this->setPlayerListHeader("JERSEY SIZE", "SHORTS SIZE", "NAME", "NUMBER");

        $players = explode("\n",$this->optionArray['Players']);

        $html .=  "<div id=\"Playerlist\" class=\"column1\">";

        foreach ($players as $player) {
            $html .=  "<p>".str_replace("-", "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", $player)."</p>";
            ;
        }

        $html .= "</div>";
        $html .= "<div id=\"column2\"></div>";
        $html .= "<div class=\"clear\"></div>";

        $html .=  "</div>";

        return $html;
    }


    public function getNotes(){
        $html = "<fieldset>";
        $html .=  "<legend>NOTES</legend>";
        $html .=  $this->optionArray['Notes'];
        $html .=  "</fieldset>";

        return $html;
    }


    public function getPrintingLogo(){
        $html = "<fieldset>";
        $html .= "<legend>LOGO</legend>";

        if($this->optionArray['Logo Type'] == "Custom"){
            $html .= $this->getLink($this->optionArray['Upload Your Photo']);
            $html .= "<br><b>Font Type:</b> " . $this->optionArray['Font Type'];
            $html .= "<br><b>Font Style:</b> " . $this->optionArray['Choose ' . $this->optionArray['Font Type'] . ' Stock Font'];
            $html .= "<br><b>Lettering Style:</b> " . $this->optionArray['Choose Lettering Style'];
        }


        if($this->optionArray['Logo Type'] == "No Logo"){
             $html .= "<b>Style:</b> " . $this->optionArray['Logo Type'];
        }

        if($this->optionArray['Logo Type'] == "Stock Logo"){
             $html .= "<b>Style:</b> " . $this->optionArray['Choose on of the logos below and we will substitute your team name'];
        }
        else
            $html .= "<b>Style:</b> " . $this->optionArray['Choose on of the logos below and we will substitute your team name'];

       
        $html .= "<br><b>Color Inside:</b> " . $this->optionArray['Choose Color Logo'];
        $html .= "<br><b>Color Outside:</b> " . $this->optionArray['Choose Outline Color Logo'];
        $html .= "<br>";
        $html .= "<br><b>Color Inside (Reverse):</b> " . $this->optionArray['Choose Color Logo (Reverse Side)'];
        $html .= "<br><b>Color Outside (Reverse):</b> " . $this->optionArray['Choose Outline Color Logo (Reverse Side)'];

        $html .= "</fieldset>";

        return $html;
    }


    public function getPrintingNumber($invoiceID){
        $html = "<fieldset>";
        $html .= "<legend>NUMBER</legend>";          
        $html .= "<b>Number Location:</b> " . $this->optionArray['Choose Number Location'];
        $html .= "<br><b>Color Inside:</b> " . $this->optionArray['Choose Color Number'];
        $html .= "<br><b>Color Outside:</b> " . $this->optionArray['Choose Outline Color Number'];
        $html .= "<br>";
        $html .= "<br><b>Color Insede (Reverse):</b> " . $this->optionArray['Choose Color Number (Reverse Side)'];
        $html .= "<br><b>Color Outside (Reverse):</b> " . $this->optionArray['Choose Outline Color Number (Reverse Side)'];
        $html .= "<br><b>Lettering Package</b> " . $this->optionArray['Choose Package'];
        
        $html .= "<form id=\"imageform\" method=\"post\" enctype=\"multipart/form-data\" action='upload/ajaximage.php?invoiceNo=$invoiceID'>
                    <br><b>Upload custom uniform image</b> <input type=\"file\" name=\"photoimg\" id=\"photoimg\" /></form>";
        $html .= "<div id='preview'></div>";
        $html .= "<div class=\"clear\"></div>";

        $html .= "</fieldset>";

        return $html;
    }

    public function getImage($color){  
        $filename = "/media/swatches/" . str_replace(".", "", str_replace(" ", "", str_replace("/", "", strtolower($color)))) . ".gif";
        
        if(file_exists($_SERVER['DOCUMENT_ROOT'] . $filename)){
            $path = "http://".$_SERVER['SERVER_NAME'].$filename;
            $html = "<img class=\"myImage\"  src=\"$path\" alt=\"". $color ."\" />";
        }
        else
            $html = "<img class=\"myImage\"  src=\"".  "http://".$_SERVER['SERVER_NAME']."/media/swatches/no-image.gif\" />";

        return $html;     
    }

    public function getColor($color){
        
        if(strpos($color, "_")){
            $color = explode("_", $color);
            $color = $color[1];
        }

        return $color;    
    }

    public function getLink($html)
    {
        $hrefs = array();

        $dom = new DOMDocument();
        $dom->loadHTML($html);

        $tags = $dom->getElementsByTagName('a');
        
        foreach ($tags as $tag) {
            $link =  $tag->getAttribute('href');
        }
        
        return "<fieldset id=\"customLogo\">
                    <legend>Custom logo</legend>
                    <img src=\"$link\" alt=\"CUSTOM LOGO\" />
                </fieldset>";
    }

}


class Basketball extends OrderOptions
{
    
    public function getUniformDetails($categoryName, $productId, $productName, $prductCont, $qty){
        $jerseyColor = $this->optionArray['Choose Jersey Color'];
        $shortsColor = $this->optionArray['Choose Shorts Color'];

        
        $html = "<br><hr /><h4>$productName <span>[Product # $prductCont]</span></h4><hr />"; 
        $html .= "<div class=\"uniform\">";
        $html .= "<br>";
        $html .= "<b>Category</b> " . $categoryName;
        $html .= "<br><b>Qty</b> " . $qty;
        $html .= "<br><b>Jersey Colors</b> " . $this->getColor($jerseyColor);
        $html .= (!empty($shortsColor))?"<br><b>Shorts Color</b> " . $this->getColor($shortsColor) : "";
        $html .= "<br><b>Team name</b> " . $this->optionArray['Enter Team Name'];
        $html .= "</div>";

        //Print Images
        $html .= $this->getImage($jerseyColor);
        $html .= (!empty($shortsColor))? $this->getImage($shortsColor) : "";
        
        $html .= "<div class=\"clear\"></div><br><br>";

        return $html;
    }
}


class  Baseball extends Basketball
{
    public function getUniformDetails($categoryName, $productId, $productName, $prductCont, $qty){
        $jerseyPanelColor = $this->optionArray['Jersey Sleeves/Panels Color'];
        //$shortsColor = $this->optionArray['Choose Shorts Color'];

        
        $html = "<br><hr /><h4>$productName <span>[Product # $prductCont]</span></h4><hr />"; 
        $html .= "<div class=\"uniform\">";
        $html .= "<br>";
        $html .= "<b>Category</b> " . $categoryName;
        $html .= "<br><b>Qty</b> " . $qty;
        $html .= "<br>";
        $html .= "<br><b>Fabrics</b> " . $this->optionArray['Choose Fabrics'];
        $html .= "<br><b>Fabric Color</b> " . $this->optionArray['Choose Color'];
        $html .= "<br>";
        $html .= "<br><b>Jersey Sleeves/Panels Color</b> " . $this->getColor($jerseyPanelColor);
        //$html .= (!empty($shortsColor))?"<br><b>Shorts Color</b> " . $this->getColor($shortsColor) : "";
        $html .= "<br><b>Team name</b> " . $this->optionArray['Enter Team Name'];
        $html .= "</div>";

        //Print Images
        $html .= $this->getImage($jerseyPanelColor);
        //$html .= (!empty($shortsColor))? $this->getImage($shortsColor) : "";
        
        $html .= "<div class=\"clear\"></div><br>";

        return $html;
    }

}


class  Softball extends Basketball
{
    
    public function getAccessories(){
        $html = "<fieldset>";
        $html .= "<legend>ACCESORIES</legend>";  
        $html .= "<br><b>How many?:</b> " . $this->optionArray['How many?'];
        
        if(strpos($this->optionArray['Would you like to add Visors?'], 'Yes')){
            echo "<br><b>Choose Visor Color:</b> " . $this->optionArray['Choose Visor Color'];
        }

        $html .= "<br>";

        if(strpos($this->optionArray['Would you like to add Socks?'], 'Yes')){          
            $html .= "<br><b>Choose Socks Type:</b> " . $this->optionArray['Choose Socks Type'];
            $html .= (!empty($this->optionArray['Choose Socks Color']))? "<br><b>Choose Socks Color</b> " . $this->optionArray['Choose Socks Color'] : "";
        }
        
        $html .= "</fieldset>";

        return $html;
    }
    
}


class  Soccer extends Basketball
{
}


class  Football extends Basketball
{

}


class  Volleyball extends Basketball
{
}

/********** CLASSES::END **********/



function ShowOrder($incrementId)
{
   
    $order = Mage::getModel('sales/order')->loadByIncrementId($incrementId);
    

    $prductCont = 0;
    $html = "";
    $accessories = "";

    foreach ($order->getAllItems() as $item) {
        $prductCont++;

        // GET CATEGORY
        $product = Mage::getModel('catalog/product')->load($item->getProductId());
        $cats = $product->getCategoryIds();
        $_cat = Mage::getModel('catalog/category')->load($cats[0]);
        $categoryName = $_cat->getName();
        

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
                    $basketballOrder = new Basketball($optionArray);
                    $html = $basketballOrder->getUniformDetails($categoryName, $product->getId(), $product->getName(), $prductCont, $quantity);
                    $accessories = "";
                    break;

                case 'Football':
                    $footballOrder = new Football($optionArray);
                    $html = $footballOrder->getUniformDetails($categoryName, $product->getId(), $product->getName(), $prductCont, $quantity);
                    $accessories = "";
                    break;

                case 'Soccer':
                    $soccerOrder = new Soccer($optionArray);
                    $html = $soccerOrder->getUniformDetails($categoryName, $product->getId(), $product->getName(), $prductCont, $quantity);
                    $accessories = "";
                    break;

                case 'Softball':
                    $softballOrder = new Softball($optionArray);
                    $html = $softballOrder->getUniformDetails($categoryName, $product->getId(), $product->getName(), $prductCont, $quantity);
                    $accessories = $softballOrder->getAccessories();
                    break;

                case 'Baseball':
                    $baseballOrder = new Baseball($optionArray);
                    $html = $baseballOrder->getUniformDetails($categoryName, $product->getId(), $product->getName(), $prductCont, $quantity);
                    $accessories = "";
                    break;

                case 'Volleyball':
                    $volleyballOrder = new Volleyball($optionArray);
                    $html = $volleyballOrder->getUniformDetails($categoryName, $product->getId(), $product->getName(), $prductCont, $quantity);
                    $accessories = "";
                    break;
                
                default:
                    echo "CATEGORY $categoryName WAS NOT FOUND!...";
                    break;
            }

            $printingOptions = new OrderOptions($optionArray);

            $html .=  $printingOptions->getPlayerList();
            $html .=  $printingOptions->getNotes();
            $html .=  $printingOptions->getPrintingLogo();
            $html .=  $printingOptions->getPrintingNumber($incrementId); 
            $html .=  $accessories; 
            
           echo $html;
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
                $(this).appendTo("#column2");
            }
        });


        //UPLOAD IMAGE
        $('#photoimg').on('change', function(){ 
            $("#preview").html('');
            $("#preview").html('<img src="upload/loader.gif" alt="Uploading...."/>');
            
            $("#imageform").ajaxForm({
                target: '#preview'
            }).submit();
        
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
            width:100px; height:150px;
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
            float: right;
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
            <p><b>Shipping Date: </b><input type="text"  id="shippingDate" value="" /></p>
            
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
