<?php 

/***** PRINTING OPTIONS *****/

class OrderOptions
{ 
    public $optionArray;
    public $playerQty = 0;

    function __construct($optionArray, $playerQty)
    {
        $this->optionArray = $optionArray;
        $this->setPlayerQuantity($playerQty);
    }

    public function setPlayerQuantity($value){
        $this->playerQty = $value;
    }

    public function getPlayerQuantity(){
        return $this->playerQty;
    }

    public function isEmpty($value){
        return ($value != '')?$value:"--";

    }

    public function setPlayerListHeader($jersey, $shorts, $name, $number)
    {
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

    public function getPlayerList()
    {

        $html = "<div class=\"groupContainer\">";
        $html .= "<p class=\"mainTitle\">NAME LIST</p>";
        $html .= "<b>How Many Names to Print?</b> " . $this->optionArray['How Many Names to Print?'];
        $html .= "<br><br>";

        $html .= $this->setPlayerListHeader("JERSEY SIZE", "SHORTS SIZE", "NAME", "NUMBER");
        

        $list = array();
        $spacing = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

        parse_str(html_entity_decode($this->optionArray['Players']), $list);

        $html .=  "<div id=\"Playerlist\" style=\"display: table;\" class=\"column1\">";
        

        for ($i=1; $i <= $this->getPlayerQuantity(); $i++) {
            $html .= "<p style=\"display: table-row; width:300px;\">";
            $html .= "<span style=\"display: table-cell; width:50px;\">" . $this->isEmpty($list['top'.$i]) . "</span>";
            $html .= "<span style=\"display: table-cell; width:50px;\">" . $this->isEmpty($list['bottom'.$i]) . "</span>";
            $html .= "<span style=\"display: table-cell; width:120px; \">" . $this->isEmpty($list['name'.$i]) . "</span>";
            $html .= "<span style=\"display: table-cell;\">" . $this->isEmpty($list['number'.$i]) . "</span>";
            $html .= "</p>";
        }

       

        // for($players as $player) {
        //     $html .=  "<p>".str_replace("-", "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", $player)."</p>";
        //     ;
        // }

        $html .= "</div>";
        $html .= "<div id=\"column2\" style=\"display: table;\"></div>";
        $html .= "<div class=\"clear\"></div>";

        $html .=  "</div>";

        return $html;
    }


    public function getNotes()
    {
        $html = "<fieldset>";
        $html .=  "<legend>NOTES</legend>";
        $html .=  $this->optionArray['Notes'];
        $html .=  "</fieldset>";

        return $html;
    }


    public function getPrintingLogo()
    {
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


    public function getPrintingNumber($invoiceID)
    {
        $html = "<fieldset>";
        $html .= "<legend>NUMBER</legend>";          
        $html .= "<b>Number Location:</b> " . $this->optionArray['Choose Number Location'];
        $html .= "<br><b>Color Inside:</b> " . $this->optionArray['Choose Color Number'];
        $html .= "<br><b>Color Outside:</b> " . $this->optionArray['Choose Outline Color Number'];
        $html .= "<br>";
        $html .= "<br><b>Color Insede (Reverse):</b> " . $this->optionArray['Choose Color Number (Reverse Side)'];
        $html .= "<br><b>Color Outside (Reverse):</b> " . $this->optionArray['Choose Outline Color Number (Reverse Side)'];
        $html .= "<br><b>Lettering Package</b> " . $this->optionArray['Choose Package'];
        
        $html .= "</fieldset>";

        return $html;
    }

    public function getImage($color)
    {  
        $filename = "/media/swatches/" . str_replace(".", "", str_replace(" ", "", str_replace("/", "", strtolower($color)))) . ".gif";
        
        if(file_exists($_SERVER['DOCUMENT_ROOT'] ."/newsite/" . $filename)){
            $path = "http://".$_SERVER['SERVER_NAME'].$filename;
            $html = "<img class=\"myImage\"  src=\"$path\" alt=\"". $color ."\" />";
        }
        else
            $html = "<img class=\"myImage\" alt=\"$filename\"  src=\"".  "http://".$_SERVER['SERVER_NAME']."/media/swatches/no-image.gif\" />";

        return $html;     
    }

    public function getColor($color)
    {
        
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
    public function getUniformDetails($categoryName, $productId, $productName, $prductCont){
        
        $jerseyColor = $this->optionArray['Choose Jersey Color'];
        $shortsColor = $this->optionArray['Choose Shorts Color'];
        $packageColor = $this->optionArray['Choose Package Color'];
        $chooseColor = $this->optionArray['Choose Color'];

        $choosePanelColor = $this->optionArray['Choose Panel Color'];
        $chooseBodyColor = $this->optionArray['Choose Body Color'];
        $chooseJerseyPanelColor = $this->optionArray['Choose Jersey Panel Color'];
        $chooseJerseyStripeColor = $this->optionArray['Choose Jersey Stripe Color'];
        $choosePipingColor = $this->optionArray['Choose Piping Color'];
        $chooseJerseySleeveColor = $this->optionArray['Choose Jersey Sleeve Color'];
        $chooseJerseyBraidColor = $this->optionArray['Choose Jersey Braid Color'];
        $chooseJerseyTrimColor = $this->optionArray['Choose Jersey Trim Color'];
        $choosePanel1 = $this->optionArray['Choose Panel 1'];
        $choosePanel2Stripe = $this->optionArray['Choose Panel 2 / Stripe'];
        $choosePanel1RevPanel1 = $this->optionArray['Choose Panel 1 / Rev Panel 1'];
        $choosePanel2RevBody = $this->optionArray['Choose Panel 2 / Rev. Body'];
        $choosePipingRevPiping = $this->optionArray['Choose Piping / Rev Piping'];
        
        $chooseYourMascot = $this->optionArray['Choose your Mascot'];
        $mascotChooseColor = $this->optionArray['Mascot Choose Color'];
        $mascotChooseColorReverseSide = $this->optionArray['Mascot Choose Color- Reverse Side'];

        $choosePantsColor = $this->optionArray['Choose Pants Color'];
        $chooseShortsColor = $this->optionArray['Choose Shorts Color'];
        $chooseBeltColor = $this->optionArray['Choose Belt Color'];
        $chooseCapsColor = $this->optionArray['Choose Caps Color'];
        $chooseSocksColor = $this->optionArray['Choose Socks color'];

        $chooseVisorColor = $this->optionArray['Choose Visor Color'];
        $chooseBagColor = $this->optionArray['Choose Bag Color'];
        $chooseTopColor = $this->optionArray['Choose Top Color'];
        $chooseBottomColor = $this->optionArray['Choose Bottom Color'];


        
        $html = "<br><hr /><h4>$productName <span>[Product # $prductCont]</span></h4><hr />"; 
        $html .= "<div class=\"uniform\">";
        $html .= "<br>";
        
        //Print text 
        $html .= "<b>Category</b> " . $categoryName;
        $html .= "<br><b>Qty</b> " . $this->getPlayerQuantity();
        $html .= (!empty($jerseyColor))?"<br><b>Jersey Colors</b> " . $this->getColor($jerseyColor) : "";
        $html .= (!empty($shortsColor))?"<br><b>Shorts Color</b> " . $this->getColor($shortsColor) : "";
        $html .= (!empty($packageColor))?"<br><b>Package Color</b> " . $this->getColor($packageColor) : "";
        $html .= (!empty($chooseColor))?"<br><b>Color</b> " . $this->getColor($chooseColor) : "";
        
        //--
        $html .= (!empty($choosePanelColor))?"<br><b>Choose Panel Color</b> " . $this->getColor($choosePanelColor) : "";
        $html .= (!empty($chooseBodyColor))?"<br><b>Choose Body Color</b> " . $this->getColor($chooseBodyColor) : "";
        $html .= (!empty($chooseJerseyPanelColor))?"<br><b>Choose Jersey Panel Color</b> " . $this->getColor($chooseJerseyPanelColor) : "";
        $html .= (!empty($chooseJerseyStripeColor))?"<br><b>Choose Jersey Stripe Color</b> " . $this->getColor($chooseJerseyStripeColor) : "";
        $html .= (!empty($choosePipingColor))?"<br><b>Choose Piping Color</b> " . $this->getColor($choosePipingColor) : "";
        $html .= (!empty($chooseJerseySleeveColor))?"<br><b>Choose Jersey Sleeve Color</b> " . $this->getColor($chooseJerseySleeveColor) : "";
        $html .= (!empty($chooseJerseyBraidColor))?"<br><b>Choose Jersey Braid Color</b> " . $this->getColor($chooseJerseyBraidColor) : "";
        $html .= (!empty($chooseJerseyTrimColor))?"<br><b>Choose Jersey Trim Color</b> " . $this->getColor($chooseJerseyTrimColor) : "";
        $html .= (!empty($choosePanel1))?"<br><b>Choose Panel 1</b> " . $this->getColor($choosePanel1) : "";
        $html .= (!empty($choosePanel2Stripe))?"<br><b>Choose Panel 2 / Stripe</b> " . $this->getColor($choosePanel2Stripe) : "";
        $html .= (!empty($choosePanel1RevPanel1))?"<br><b>choose Panel 1 / Rev Panel 1</b> " . $this->getColor($choosePanel1RevPanel1) : "";
        $html .= (!empty($choosePanel2RevBody))?"<br><b>Choose Panel 2 Rev. Body</b> " . $this->getColor($choosePanel2RevBody) : "";
        $html .= (!empty($choosePipingRevPiping))?"<br><b>Choose Piping / Rev Piping</b> " . $this->getColor($choosePipingRevPiping) : "";
        
        $html .= (!empty($chooseYourMascot))?"<br><b>Choose your Mascot</b> " . $this->getColor($chooseYourMascot) : "";
        $html .= (!empty($mascotChooseColor))?"<br><b>Mascot Choose Color</b> " . $this->getColor($mascotChooseColor) : "";
        $html .= (!empty($mascotChooseColorReverseSide))?"<br><b>Mascot Choose Color- Reverse Side</b> " . $this->getColor($mascotChooseColorReverseSide) : "";
        
        $html .= (!empty($choosePantsColor))?"<br><b>Choose Pants Color</b> " . $this->getColor($choosePantsColor) : "";
        $html .= (!empty($chooseShortsColor))?"<br><b>Choose Shorts Color</b> " . $this->getColor($chooseShortsColor) : "";
        $html .= (!empty($chooseBeltColor))?"<br><b>Choose Belt Color</b> " . $this->getColor($chooseBeltColor) : "";
        $html .= (!empty($chooseCapsColor))?"<br><b>Choose Caps Color</b> " . $this->getColor($chooseCapsColor) : "";
        $html .= (!empty($chooseSocksColor))?"<br><b>Choose Socks Color</b> " . $this->getColor($chooseSocksColor) : "";
        $html .= (!empty($chooseVisorColor))?"<br><b>Choose Visor Color</b> " . $this->getColor($chooseVisorColor) : "";
        $html .= (!empty($chooseBagColor))?"<br><b>Choose Bag Color</b> " . $this->getColor($chooseBagColor) : "";
        $html .= (!empty($chooseTopColor))?"<br><b>Choose Top Color</b> " . $this->getColor($chooseTopColor) : "";
        $html .= (!empty($chooseBottomColor))?"<br><b>Choose Bottom Color</b> " . $this->getColor($chooseBottomColor) : "";
      
        //--


        $html .= "<br><b>Team name</b> " . $this->optionArray['Enter Team Name'];
        $html .= "</div>";

        //Print Images
        $html .= (!empty($jerseyColor))? $this->getImage($jerseyColor) : "";
        $html .= (!empty($shortsColor))? $this->getImage($shortsColor) : "";
        $html .= (!empty($packageColor))? $this->getImage($packageColor) : "";
        $html .= (!empty($chooseColor))? $this->getImage($chooseColor) : "";
        
        $html .= "<div class=\"clear\"></div><br><br>";

        return $html;
    }
}


class  Baseball extends Basketball
{
    public function getUniformDetails($categoryName, $productId, $productName, $prductCont){
        
        $jerseyPanelColor = $this->optionArray['Jersey Sleeves/Panels Color'];
        $packageColor = $this->optionArray['Choose Package Color'];
        $chooseColor = $this->optionArray['Choose Color'];
        //$shortsColor = $this->optionArray['Choose Shorts Color'];

        
        $html = "<br><hr /><h4>$productName <span>[Product # $prductCont]</span></h4><hr />"; 
        $html .= "<div class=\"uniform\">";
        $html .= "<br>";
        $html .= "<b>Category</b> " . $categoryName;
        $html .= "<br><b>Qty</b> " . $this->getPlayerQuantity();
        $html .= "<br>";
        $html .= "<br><b>Fabrics</b> " . $this->optionArray['Choose Fabrics'];
        $html .= "<br><b>Fabric Color</b> " . $this->optionArray['Choose Color'];
        $html .= "<br>";
        $html .= (!empty($jerseyColor))? "<br><b>Jersey Sleeves/Panels Color</b> " . $this->getColor($jerseyPanelColor) : "";
        $html .= (!empty($packageColor))? "<br><b>Package Color</b> " . $this->getColor($packageColor) : "";
        $html .= (!empty($chooseColor))? "<br><b>Color</b> " . $this->getColor($chooseColor) : "";
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




/***** Custom Images *****/

class CustomImages
{
    public $valid_formats = array("jpg", "png", "gif","jpeg");
    public $path;
    public $http_path;

    
    function __construct($local_path)
    {
        $this->path = $_SERVER['DOCUMENT_ROOT'] . "/newsite".$local_path;
        $this->http_path = "http://".$_SERVER['SERVER_NAME'] . $local_path;
    }


    public function createDirectory(){
        $flag = true;

        if(!file_exists($this->path)){
            
            if(!mkdir($this->path, 0777))
                $flag = false;

        }
        //else
            //chmod($path, 0777);
        
        return $flag;
    }


    public function displayImages(){
        $html = "";
   
        if ($handle = opendir($this->path)) {
            
            while (false !== ($file = readdir($handle))) {
                if($file!="." && $file!=".."){
                     $html .= "<img class=\"customImages deleteimage\" src=\"" . $this->http_path . $file . "\" />";
                }
            }    
            closedir($handle);
        }

        return $html;
    }


    public function uploadCustomImage(){

        if(isset($_POST) and $_SERVER['REQUEST_METHOD'] == "POST")
        {
            $name = $_FILES['photoimg']['name'];
            $size = $_FILES['photoimg']['size'];
            
            if(strlen($name))
            {
                list($txt, $ext) = explode(".", $name);
                
                if(in_array($ext,$this->valid_formats))
                {
                    if($size<(1024*1024)) // Image size max 1 MB
                    {
                        $actual_image_name = $name;
                        $tmp = $_FILES['photoimg']['tmp_name'];

                        if($this->createDirectory()){

                            if(move_uploaded_file($tmp, $this->path.$actual_image_name))
                            {
                            
                                $html = "<img src='".$this->http_path . $actual_image_name . "'  class='preview deleteimage'>";
                            }
                            else
                                $html = "failed";
                        }
                        else
                            $html = "Directory " . $this->path . " could not be created!";
                    }
                    else
                        $html = "Image file size max 1 MB";
                }
                else
                    $html = "Invalid file format..";
            }
            else{
                $html = "Please select image..!";

                //exit;
            }
        }

        return $html;
    }

    public function createCustomImageModule($invoiceID){
        $html = "<fieldset>";
        $html .= "<legend>CUSTOM IMAGES</legend>";  

        $html .= "<form id=\"imageform\" method=\"post\" enctype=\"multipart/form-data\" action='upload/ajaximage.php?invoiceNo=$invoiceID'>
                    <br><b>Upload custom uniform image</b> <input type=\"file\" name=\"photoimg\" id=\"photoimg\" /></form>";
        $html .= "<div id='preview'></div>";
        $html .= "<br>";
        $html .= $this->displayImages();
        $html .= "<div class=\"clear\"></div>";
        $html .= "</fieldset>";

        return $html;
    } 
}
?>