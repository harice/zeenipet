<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Owner
 * Date: 10.01.12
 * Time: 13:30
 * To change this template use File | Settings | File Templates.
 */
class Infomodus_Upslabel_RefundController extends Mage_Core_Controller_Front_Action
{
    public function preDispatch()
    {
        parent::preDispatch();

        if (!Mage::getSingleton('customer/session')->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
        }
    }

    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function printAction()
    {
        $this->loadLayout();
        $this->getLayout()->getBlock('root')->setTemplate('upslabel/sales/order/refund/refund.phtml');
        $this->renderLayout();
    }

    public function customerrefundAction()
    {
        if (Mage::getStoreConfig('upslabel/profile/frontend_customer_return') == 1) {
            if ($_POST) {
                $order_id = $this->getRequest()->getParam('id');
                $type = 'customer';
                $collections = Mage::getModel('upslabel/upslabel');
                $colls = $collections->getCollection()->addFieldToFilter('order_id', $order_id)->addFieldToFilter('type', $type);
                $coll = 0;
                foreach ($colls AS $v) {
                    $coll = $v['upslabel_id'];
                    break;
                }
                $collection = Mage::getModel('upslabel/upslabel')->load($coll);
                if ($collection->getOrderId() != $order_id) {
                    $packages = array();
                    $configOptions = new Infomodus_Upslabel_Model_Config_Options;
                    $configMethod = new Infomodus_Upslabel_Model_Config_Upsmethod;
                    $AccessLicenseNumber = Mage::getStoreConfig('upslabel/profile/accesslicensenumber');
                    $UserId = Mage::getStoreConfig('upslabel/profile/userid');
                    $Password = Mage::getStoreConfig('upslabel/profile/password');
                    $shipperNumber = Mage::getStoreConfig('upslabel/profile/shippernumber');
                    $order = Mage::getModel('sales/order')->load($order_id);
                    $shipTo = $order->getShippingAddress();

                    $path = Mage::getBaseDir('media') . DS . 'upslabel' . DS . 'label' . DS;

                    $lbl = new Infomodus_Upslabel_Model_Ups();

                    $lbl->setCredentials($AccessLicenseNumber, $UserId, $Password, $shipperNumber);
                    
                    $lbl->testing = Mage::getStoreConfig('upslabel/profile/testing');

                    $lbl->shipmentDescription = Infomodus_Upslabel_Helper_Help::escapeXML($shipTo->getCompany() ? $shipTo->getCompany() : $shipTo->getFirstname() . ' ' . $shipTo->getLastname());
                    $lbl->shipperName = Infomodus_Upslabel_Helper_Help::escapeXML(Mage::getStoreConfig('upslabel/shipper/name'));
                    $lbl->shipperAttentionName = Infomodus_Upslabel_Helper_Help::escapeXML(Mage::getStoreConfig('upslabel/shipper/attentionname'));
                    $lbl->shipperPhoneNumber = Infomodus_Upslabel_Helper_Help::escapeXML(Mage::getStoreConfig('upslabel/shipper/phonenumber'));
                    $lbl->shipperAddressLine1 = Infomodus_Upslabel_Helper_Help::escapeXML(Mage::getStoreConfig('upslabel/shipper/addressline1'));
                    $lbl->shipperCity = Infomodus_Upslabel_Helper_Help::escapeXML(Mage::getStoreConfig('upslabel/shipper/city'));
                    $lbl->shipperStateProvinceCode = Infomodus_Upslabel_Helper_Help::escapeXML(Mage::getStoreConfig('upslabel/shipper/stateprovincecode'));
                    $lbl->shipperPostalCode = Infomodus_Upslabel_Helper_Help::escapeXML(Mage::getStoreConfig('upslabel/shipper/postalcode'));
                    $lbl->shipperCountryCode = Infomodus_Upslabel_Helper_Help::escapeXML(Mage::getStoreConfig('upslabel/shipper/countrycode'));

                    $lbl->shiptoCompanyName = Infomodus_Upslabel_Helper_Help::escapeXML($shipTo->getCompany() ? $shipTo->getCompany() : $shipTo->getFirstname() . ' ' . $shipTo->getLastname());
                    $lbl->shiptoAttentionName = Infomodus_Upslabel_Helper_Help::escapeXML($shipTo->getFirstname() . ' ' . $shipTo->getLastname());
                    $lbl->shiptoPhoneNumber = Infomodus_Upslabel_Helper_Help::escapeXML($shipTo->getTelephone());
                    $lbl->shiptoAddressLine1 = Infomodus_Upslabel_Helper_Help::escapeXML(is_array($shipTo->getStreet())?trim(implode(' ', $shipTo->getStreet())):$shipTo->getStreet());
                    $lbl->shiptoCity = Infomodus_Upslabel_Helper_Help::escapeXML($shipTo->getCity());
                    $lbl->shiptoStateProvinceCode = Infomodus_Upslabel_Helper_Help::escapeXML($configOptions->getProvinceCode($shipTo->getRegion()));
                    $lbl->shiptoPostalCode = Infomodus_Upslabel_Helper_Help::escapeXML($shipTo->getPostcode());
                    $lbl->shiptoCountryCode = Infomodus_Upslabel_Helper_Help::escapeXML($shipTo->getCountryId());
                    $lbl->residentialAddress = Infomodus_Upslabel_Helper_Help::escapeXML($shipTo->getCompany() ? '' : '<ResidentialAddress />');


                    $lbl->shipfromCompanyName = Infomodus_Upslabel_Helper_Help::escapeXML(Mage::getStoreConfig('upslabel/shipfrom/companyname'));
                    $lbl->shipfromAttentionName = Infomodus_Upslabel_Helper_Help::escapeXML(Mage::getStoreConfig('upslabel/shipfrom/attentionname'));
                    $lbl->shipfromPhoneNumber = Infomodus_Upslabel_Helper_Help::escapeXML(Mage::getStoreConfig('upslabel/shipfrom/phonenumber'));
                    $lbl->shipfromAddressLine1 = Infomodus_Upslabel_Helper_Help::escapeXML(Mage::getStoreConfig('upslabel/shipfrom/addressline1'));
                    $lbl->shipfromCity = Infomodus_Upslabel_Helper_Help::escapeXML(Mage::getStoreConfig('upslabel/shipfrom/city'));
                    $lbl->shipfromStateProvinceCode = Infomodus_Upslabel_Helper_Help::escapeXML(Mage::getStoreConfig('upslabel/shipfrom/stateprovincecode'));
                    $lbl->shipfromPostalCode = Infomodus_Upslabel_Helper_Help::escapeXML(Mage::getStoreConfig('upslabel/shipfrom/postalcode'));
                    $lbl->shipfromCountryCode = Infomodus_Upslabel_Helper_Help::escapeXML(Mage::getStoreConfig('upslabel/shipfrom/countrycode'));

                    $lbl->serviceCode = '03';
                    $lbl->serviceDescription = $configMethod->getUpsMethodName($lbl->serviceCode);

                    $prod = Mage::getModel('catalog/product');
                    $weight = 0;
                    $paramWeight = $this->getRequest()->getParam('weight');
                    foreach ($this->getRequest()->getParam('cart') AS $k => $item) {
                        if (count($item) > 0 && $item > 0) {
                            $weight += $paramWeight[$k]*$item['qty'];
                        }
                    }
                    $packages[0]['weight'] = $weight;
                    $lbl->weightUnits = Mage::getStoreConfig('upslabel/profile/weightunits');
                    $packages[0]['large'] = $weight > 89 ? '<LargePackageIndicator />' : '';

                    $lbl->includeDimensions = 0;


                    $packages[0]['packagingtypecode'] = Infomodus_Upslabel_Helper_Help::escapeXML(Mage::getStoreConfig('upslabel/profile/packagingtypecode'));
                    $packages[0]['packagingdescription'] = Infomodus_Upslabel_Helper_Help::escapeXML(Mage::getStoreConfig('upslabel/profile/packagingdescription'));
                    $packages[0]['packagingreferencenumbercode'] = Infomodus_Upslabel_Helper_Help::escapeXML(Mage::getStoreConfig('upslabel/profile/packagingreferencenumbercode'));
                    $packages[0]['packagingreferencenumbervalue'] = Infomodus_Upslabel_Helper_Help::escapeXML(Mage::getStoreConfig('upslabel/profile/packagingreferencenumbervalue'));
                    $lbl->packages = $packages;

                    $lbl->codYesNo = 0;
                    $lbl->currencyCode = '';
                    $lbl->codMonetaryValue = '';
                    $upsl = $lbl->getShipFrom();
                    if (!array_key_exists('error', $upsl) || !$upsl['error']) {
                        foreach($upsl['arrResponsXML'] AS $upsl_one){
                            $upslabel = Mage::getModel('upslabel/upslabel');
                            $upslabel->setTitle('Order ' . $order_id . ' TN' . $upsl_one['trackingnumber']);
                            $upslabel->setOrderId($order_id);
                            $upslabel->setShipmentId(0);
                            $upslabel->setType($type);
                            /*$upslabel->setBase64Image();*/
                            $upslabel->setTrackingnumber($upsl_one['trackingnumber']);
                            $upslabel->setShipmentidentificationnumber($upsl['shipidnumber']);
                            $upslabel->setShipmentdigest($upsl['digest']);
                            $upslabel->setLabelname('label' . $upsl_one['trackingnumber'] . '.gif');
                            $upslabel->setCreatedTime(Date("Y-m-d H:i:s"));
                            $upslabel->setUpdateTime(Date("Y-m-d H:i:s"));
                            $upslabel->save();
                        }
                        include($path . $upsl_one['trackingnumber'] . '.html');
                    }
                    else {
                        Mage::register('error', preg_replace('/\<textarea\>.*?\<\/textarea\>/is', '', $upsl['error']));
                        $this->loadLayout();
                        $this->renderLayout();
                    }
                }
                else {
                    Mage::getSingleton('core/session')->addError($this->__('For one order, you can create only one return'));
                    $this->_redirectUrl($_SERVER['HTTP_REFERER']);
                }
            }
            else {
                $this->loadLayout();
                $this->renderLayout();
            }
        }
    }

    public function customershowlabelAction()
    {
        if (Mage::getStoreConfig('upslabel/profile/frontend_customer_return') == 1) {
            $track_id = $this->getRequest()->getParam('id');
            $label = Mage::getModel('upslabel/upslabel')->getCollection()->addFieldToFilter('trackingnumber', $track_id)->addFieldToFilter('type', 'customer');
            $label = $label->getData();
            $label = $label[0];
            if (count($label) > 0) {
                $path = Mage::getBaseDir('media') . DS . 'upslabel' . DS . 'label' . DS;
                include($path . $label['trackingnumber'] . '.html');
            }
        }
    }

}
