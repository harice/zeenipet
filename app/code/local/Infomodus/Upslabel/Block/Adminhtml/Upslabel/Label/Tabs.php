<?php

/*
 * Author Rudyuk Vitalij Anatolievich
 * Email rvansp@gmail.com
 * Blog www.cervic.info
 */
?>
<?php

class Infomodus_Upslabel_Block_Adminhtml_Upslabel_Label_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    protected function _beforeToHtml()
    {
        $AccessLicenseNumber = Mage::getStoreConfig('upslabel/profile/accesslicensenumber');
        $UserId = Mage::getStoreConfig('upslabel/profile/userid');
        $Password = Mage::getStoreConfig('upslabel/profile/password');
        $shipperNumber = Mage::getStoreConfig('upslabel/profile/shippernumber');

        $order_id = $this->getRequest()->getParam('order_id');
        $newsss = $this->getRequest()->getParam('new');
        $ship_id = $this->getRequest()->getParam('ship_id');

        $path = Mage::getBaseDir('media') . DS . 'upslabel' . DS . 'label' . DS;

        $lbl = new Infomodus_Upslabel_Model_Ups();

        $lbl->setCredentials($AccessLicenseNumber, $UserId, $Password, $shipperNumber);

        $order_id = $this->getRequest()->getParam('order_id');

        $collection = Mage::getModel('upslabel/upslabel')->load($order_id, 'order_id');
        //return print_r($collection);
        if ($collection->getOrderId() != $order_id) {
            $order = Mage::getModel('sales/order')->load($order_id);
            //$customer = Mage::getModel('customer/customer')->load($order['customer_id']);
            //$shiping_adress = Mage::getModel('sales/order_address')->load($order['shipping_address_id']);
            $shiping_adress = $order->getShippingAddress();
            //$quote = Mage::getModel('sales/quote');
            $ship_method = $order->getShippingMethod();
            $shipByUps = preg_replace("/^ups_.{2,4}$/", 'ups', $ship_method);
            /* echo '<ul>';
              foreach (get_class_methods(get_class($quote)) as $cMethod) {
              echo '<li>' . $cMethod . '</li>';
              }
              echo '</ul>'; */
            //echo $order->getShippingCarrier();
            //print_r($order->getShippingAddress());
            $onlyups = Mage::getStoreConfig('upslabel/profile/onlyups');
            if ($shipByUps == 'ups' || $onlyups == 0) {
                $provinceCode = array(
                    'Alabama' => 'AL',
                    'Alaska' => 'AK',
                    'American Samoa' => 'AS',
                    'Arizona' => 'AZ',
                    'Arkansas' => 'AR',
                    'Armed Forces Africa' => 'AE',
                    'Armed Forces Americas' => 'AA',
                    'Armed Forces Canada' => 'AE',
                    'Armed Forces Europe' => 'AE',
                    'Armed Forces Middle East' => 'AE',
                    'Armed Forces Pacific' => 'AP',
                    'California' => 'CA',
                    'Colorado' => 'CO',
                    'Connecticut' => 'CT',
                    'Delaware' => 'DE',
                    'District of Columbia' => 'DC',
                    'Federated States Of Micronesia' => 'FM',
                    'Florida' => 'FL',
                    'Georgia' => 'GA',
                    'Guam' => 'GU',
                    'Hawaii' => 'HI',
                    'Idaho' => 'ID',
                    'Illinois' => 'IL',
                    'Indiana' => 'IN',
                    'Iowa' => 'IA',
                    'Kansas' => 'KS',
                    'Kentucky' => 'KY',
                    'Louisiana' => 'LA',
                    'Maine' => 'ME',
                    'Marshall Islands' => 'MH',
                    'Maryland' => 'MD',
                    'Massachusetts' => 'MA',
                    'Michigan' => 'MI',
                    'Minnesota' => 'MN',
                    'Mississippi' => 'MS',
                    'Missouri' => 'MO',
                    'Montana' => 'MT',
                    'Nebraska' => 'NE',
                    'Nevada' => 'NV',
                    'New Hampshire' => 'NH',
                    'New Jersey' => 'NJ',
                    'New Mexico' => 'NM',
                    'New York' => 'NY',
                    'North Carolina' => 'NC',
                    'North Dakota' => 'ND',
                    'Northern Mariana Islands' => 'MP',
                    'Ohio' => 'OH',
                    'Oklahoma' => 'OK',
                    'Oregon' => 'OR',
                    'Palau' => 'PW',
                    'Pennsylvania' => 'PA',
                    'Puerto Rico' => 'PR',
                    'Rhode Island' => 'RI',
                    'South Carolina' => 'SC',
                    'South Dakota' => 'SD',
                    'Tennessee' => 'TN',
                    'Texas' => 'TX',
                    'Utah' => 'UT',
                    'Vermont' => 'VT',
                    'Virgin Islands' => 'VI',
                    'Virginia' => 'VA',
                    'Washington' => 'WA',
                    'West Virginia' => 'WV',
                    'Wisconsin' => 'WI',
                    'Wyoming' => 'WY',
                    /* Canada */
                    'Alberta' => 'AB',
                    'British Columbia' => 'BC',
                    'Manitoba' => 'MB',
                    'New Brunswick' => 'NB',
                    'Newfoundland and Labrador' => 'NL',
                    'Northwest Territories' => 'NT',
                    'Nova Scotia' => 'NS',
                    'Nunavut' => 'NU',
                    'Ontario' => 'ON',
                    'Prince Edward Island' => 'PE',
                    'Quebec' => 'QC',
                    'Saskatchewan' => 'SK',
                    'Yukon' => 'YT',
                    '' => '',
                    '' => '',
                );
                $sercoD = array(
                    '1DM' => '14',
                    '1DA' => '01',
                    '1DP' => '13',
                    '2DM' => '59',
                    '2DA' => '02',
                    '3DS' => '12',
                    'GND' => '03',
                    'EP' => '54',
                    'ES' => '07',
                    'SV' => '65',
                    'EX' => '08',
                    'ST' => '11',
                    'ND' => '07',
                );

                $sercoD2 = array(
                    '14' => '14',
                    '1' => '01',
                    '13' => '13',
                    '59' => '59',
                    '2' => '02',
                    '12' => '12',
                    '3' => '03',
                    '54' => '54',
                    '7' => '07',
                    '65' => '65',
                    '8' => '08',
                    '11' => '11',
                    '7' => '07',
                );

                $shipByUpsCode = preg_replace("/^ups_(.{2,4})$/", '$1', $ship_method);
                $db = Mage::getSingleton('core/resource')->getConnection('core_write');
                $shipByUpsName = $db->query('SELECT method_title FROM ' . Mage::app()->getConfig()->getNode('global/resources/db')->table_prefix . 'sales_flat_quote_shipping_rate where code=\'' . $ship_method . '\'')->fetch();
                $lbl->shipmentDescription = $this->getRequest()->getParam('description') ? $this->getRequest()->getParam('description') : Mage::getStoreConfig('upslabel/profile/description');
                $lbl->shipperName = Mage::getStoreConfig('upslabel/shipper/name');
                $lbl->shipperAttentionName = Mage::getStoreConfig('upslabel/shipper/attentionname');
                $lbl->shipperPhoneNumber = Mage::getStoreConfig('upslabel/shipper/phonenumber');
                $lbl->shipperAddressLine1 = Mage::getStoreConfig('upslabel/shipper/addressline1');
                $lbl->shipperCity = Mage::getStoreConfig('upslabel/shipper/city');
                $lbl->shipperStateProvinceCode = Mage::getStoreConfig('upslabel/shipper/stateprovincecode');
                $lbl->shipperPostalCode = Mage::getStoreConfig('upslabel/shipper/postalcode');
                $lbl->shipperCountryCode = Mage::getStoreConfig('upslabel/shipper/countrycode');

                $lbl->shiptoCompanyName = strlen($shiping_adress['company']) == 0 ? Mage::getStoreConfig('upslabel/profile/companyname') : $shiping_adress['company'];
                $lbl->shiptoCompanyName = strlen($lbl->shiptoCompanyName) == 0 ? $shiping_adress['firstname'] . ' ' . $shiping_adress['lastname'] : $lbl->shiptoCompanyName;
                $lbl->shiptoAttentionName = $shiping_adress['firstname'] . ' ' . $shiping_adress['lastname'];
                $lbl->shiptoPhoneNumber = $shiping_adress['telephone'];
                $lbl->shiptoAddressLine1 = $shiping_adress['street'];
                $lbl->shiptoCity = $shiping_adress['city'];
                $lbl->shiptoStateProvinceCode = array_key_exists($shiping_adress['region'], $provinceCode) ? $provinceCode[$shiping_adress['region']] : '';
                $lbl->shiptoPostalCode = $shiping_adress['postcode'];
                $lbl->shiptoCountryCode = $shiping_adress['country_id'];

                $lbl->shipfromCompanyName = Mage::getStoreConfig('upslabel/shipfrom/companyname');
                $lbl->shipfromAttentionName = Mage::getStoreConfig('upslabel/shipfrom/attentionname');
                $lbl->shipfromPhoneNumber = Mage::getStoreConfig('upslabel/shipfrom/phonenumber');
                $lbl->shipfromAddressLine1 = Mage::getStoreConfig('upslabel/shipfrom/addressline1');
                $lbl->shipfromCity = Mage::getStoreConfig('upslabel/shipfrom/city');
                $lbl->shipfromStateProvinceCode = Mage::getStoreConfig('upslabel/shipfrom/stateprovincecode');
                $lbl->shipfromPostalCode = Mage::getStoreConfig('upslabel/shipfrom/postalcode');
                $lbl->shipfromCountryCode = Mage::getStoreConfig('upslabel/shipfrom/countrycode');
                $lbl->serviceCode = array_key_exists($this->getRequest()->getParam('serviceCode'), $sercoD) ? $sercoD[$this->getRequest()->getParam('serviceCode')] : $this->getRequest()->getParam('serviceCode');
                $lbl->serviceDescription = $shipByUpsName['method_title'];

                $lbl->packageWeight = $order['weight'];

                $lbl->packagingTypeCode = Mage::getStoreConfig('upslabel/profile/packagingtypecode');
                $lbl->packagingDescription = Mage::getStoreConfig('upslabel/profile/packagingdescription');
                $lbl->packagingReferenceNumberCode = Mage::getStoreConfig('upslabel/profile/packagingreferencenumbercode');
                $lbl->packagingReferenceNumberValue = Mage::getStoreConfig('upslabel/profile/packagingreferencenumbervalue');
                $lbl->codMonetaryValue = $order->getGrandTotal();

                //echo $lbl->getShip($order_id);
                //print_r($lbl->getShip($order_id));

                $upsl = $lbl->getShip($order_id);
                //return print_r($upsl['trackingnumber']);
                if (!array_key_exists('error', $upsl) || !$upsl['error']) {
                    $upslabel = Mage::getModel('upslabel/upslabel');
                    $upslabel->setTitle('Order ' . $order_id . ' TN' . $upsl['trackingnumber']);
                    $upslabel->setOrderId($order_id);
                    $upslabel->setTrackingnumber($upsl['trackingnumber']);
                    $upslabel->setShipmentidentificationnumber($upsl['shipidnumber']);
                    $upslabel->setShipmentdigest($upsl['digest']);
                    $upslabel->setLabelname($upsl['img_name']);
                    $upslabel->setCreatedTime(Date("Y-m-d H:i:s"));
                    $upslabel->setUpdateTime(Date("Y-m-d H:i:s"));
                    $upslabel->save();
                    $backLink = $this->getUrl('adminhtml/sales_order_shipment/new/order_id/' . $order_id);
                    if ($shipByUps == 'ups' && Mage::getStoreConfig('upslabel/profile/addtrack') == 1) {
                        $trTitle = 'United Parcel Service';
                        $convertor = Mage::getModel('sales/convert_order');
                        $shipment = $convertor->toShipment($order);
                        foreach ($order->getAllItems() as $orderItem) {
                            if (!$orderItem->getQtyToShip()) {
                                continue;
                            }
                            if ($orderItem->getIsVirtual()) {
                                continue;
                            }
                            $item = $convertor->itemToShipmentItem($orderItem);
                            $qty = $orderItem->getQtyToShip();
                            $item->setQty($qty);
                            $shipment->addItem($item);
                        }
                        $track = Mage::getModel('sales/order_shipment_track')
                            ->setNumber(trim($upsl['trackingnumber']))
                            ->setCarrierCode('ups')
                            ->setTitle($trTitle);
                        $shipment->addTrack($track)->save();
                        $backLink = $this->getUrl('adminhtml/sales_order/view/order_id/' . $order_id);
                    }
                    echo '<h1> Order ID ' . $order_id . ' TN ' . $upsl['trackingnumber'] . '</h1>
<br />
<a href="' . $backLink . '">Back</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<a href="' . $this->getUrl('upslabel/adminhtml_upslabel/deletelabel/order_id/' . $order_id) . '">Delete Label</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="' . $this->getUrl('upslabel/adminhtml_upslabel/print/imname/' . $upsl['img_name']) . '" target="_blank">Print Label</a>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="' . Mage::getBaseUrl('media') . 'upslabel/label/' . $upsl['trackingnumber'] . '.html" target="_blank">Print Html image</a>
';
                    if (file_exists(Mage::getBaseDir('media') . '/upslabel/label/' . "HVR" . $upsl['trackingnumber'] . ".html")) {
                        echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="' . Mage::getBaseUrl('media') . 'upslabel/label/HVR' . $upsl['trackingnumber'] . '.html" target="_blank">Print High Value report</a>';
                    }
                    echo '
<br /><br />
Tracking Number ' . $upsl['trackingnumber'] . '
<br /><br />
<br /><a href="' . Mage::getBaseUrl('media') . 'upslabel/label/' . $upsl['img_name'] . '" target="_blank"><img src="' . Mage::getBaseUrl('media') . 'upslabel/label/' . $upsl['img_name'] . '" /></a>';
                } else {
                    echo $upsl['error'];
                }
            } else {
                echo "This is not sent by UPS";
            }
        } else {
            $new = 'view';
            $sp = $ship_id;
            $ships = 'shipment_id';
            if (!$newsss || $newsss != 'no') {
                $new = 'new';
                $ships = 'order_id';
                $sp = $order_id;
            }
            echo '<h1>' . $collection['title'] . '</h1>
<br />
<a href="' . $this->getUrl('adminhtml/sales_order_shipment/' . $new . '/' . $ships . '/' . $sp) . '">Back</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<a href="' . $this->getUrl('upslabel/adminhtml_upslabel/deletelabel/order_id/' . $order_id) . '">Delete Label</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="' . $this->getUrl('upslabel/adminhtml_upslabel/print/imname/' . $collection['labelname']) . '" target="_blank">Print Label</a>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="' . Mage::getBaseUrl('media') . 'upslabel/label/' . $collection['trackingnumber'] . '.html" target="_blank">Print Html image</a>
    ';
            if (file_exists(Mage::getBaseDir('media') . '/upslabel/label/' . "HVR" . $collection['trackingnumber'] . ".html")) {
                echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="' . Mage::getBaseUrl('media') . 'upslabel/label/HVR' . $collection['trackingnumber'] . '.html" target="_blank">Print High Value report</a>';
            }
            echo '
<br /><br />
Tracking Number ' . $collection['trackingnumber'] . '
<br /><br />
<br /><a href="' . Mage::getBaseUrl('media') . 'upslabel/label/' . $collection['labelname'] . '" target="_blank"><img src="' . Mage::getBaseUrl('media') . 'upslabel/label/' . $collection['labelname'] . '" /></a>';
        }
        //$collection->setOrderId($order_id);
        //$collection->setTitle('Testkklsdfjgdfkljgldfk');
        //$collection->save();
        //echo $collection->getTitle();


        return parent::_beforeToHtml();
    }

}