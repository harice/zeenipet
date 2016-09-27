<?php
/*
 * Author Rudyuk Vitalij Anatolievich
 * Email rvansp@gmail.com
 * Blog www.cervic.info
 */
?>
<?php

class Infomodus_Upslabel_Adminhtml_UpslabelController extends Mage_Adminhtml_Controller_Action
{

    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('upslabel/items')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));

        return $this;
    }

    public function indexAction()
    {
        $this->_initAction()
            ->renderLayout();
    }

    public function showlabelAction()
    {
        $configOptions = new Infomodus_Upslabel_Model_Config_Options;
        $configMethod = new Infomodus_Upslabel_Model_Config_Upsmethod;

        $order_id = $this->getRequest()->getParam('order_id');
        $type = $this->getRequest()->getParam('type');
        $shipment_id = $this->getRequest()->getParam('shipment_id');
        $params = $this->getRequest()->getParams();
        $this->loadLayout();
        //$block = $this->getLayout()->getBlock('showlabel');
        $AccessLicenseNumber = Mage::getStoreConfig('upslabel/profile/accesslicensenumber');
        $UserId = Mage::getStoreConfig('upslabel/profile/userid');
        $Password = Mage::getStoreConfig('upslabel/profile/password');
        $shipperNumber = Mage::getStoreConfig('upslabel/profile/shippernumber');

        $path = Mage::getBaseDir('media') . DS . 'upslabel' . DS . 'label' . DS;

        $lbl = Mage::getModel('upslabel/ups');

        $lbl->setCredentials($AccessLicenseNumber, $UserId, $Password, $shipperNumber);
        $collections = Mage::getModel('upslabel/upslabel');
        $colls = $collections->getCollection()->addFieldToFilter('shipment_id', $shipment_id)->addFieldToFilter('type', $type);
        $coll = 0;
        foreach ($colls AS $v) {
            $coll = array_key_exists('upslabel_id',$v->getData())?$v['upslabel_id']:0;
            break;
        }
        $v='';
        $collection = Mage::getModel('upslabel/upslabel')->load($coll);
        //print_r($colls);
        if ($collection->getShipmentId() != $shipment_id) {
            $arrPackagesOld = $this->getRequest()->getParam('package');

            foreach($arrPackagesOld AS $k=>$v){
                $i=0;
                foreach($v AS $d=>$f){
                    $arrPackages[$i][$k] = $f;
                    $i+=1;
                }
            }
            unset($v, $k, $i, $d, $f);
            $lbl->packages = $arrPackages;
            $lbl->shipmentDescription = Infomodus_Upslabel_Helper_Help::escapeXML($params['shipmentdescription']);
            $lbl->shipperName = Infomodus_Upslabel_Helper_Help::escapeXML(Mage::getStoreConfig('upslabel/shipper/name'));
            $lbl->shipperAttentionName = Infomodus_Upslabel_Helper_Help::escapeXML(Mage::getStoreConfig('upslabel/shipper/attentionname'));
            $lbl->shipperPhoneNumber = Infomodus_Upslabel_Helper_Help::escapeXML(Mage::getStoreConfig('upslabel/shipper/phonenumber'));
            $lbl->shipperAddressLine1 = Infomodus_Upslabel_Helper_Help::escapeXML(Mage::getStoreConfig('upslabel/shipper/addressline1'));
            $lbl->shipperCity = Infomodus_Upslabel_Helper_Help::escapeXML(Mage::getStoreConfig('upslabel/shipper/city'));
            $lbl->shipperStateProvinceCode = Infomodus_Upslabel_Helper_Help::escapeXML(Mage::getStoreConfig('upslabel/shipper/stateprovincecode'));
            $lbl->shipperPostalCode = Infomodus_Upslabel_Helper_Help::escapeXML(Mage::getStoreConfig('upslabel/shipper/postalcode'));
            $lbl->shipperCountryCode = Infomodus_Upslabel_Helper_Help::escapeXML(Mage::getStoreConfig('upslabel/shipper/countrycode'));

            $lbl->shiptoCompanyName = Infomodus_Upslabel_Helper_Help::escapeXML($params['shiptocompanyname']);
            $lbl->shiptoAttentionName = Infomodus_Upslabel_Helper_Help::escapeXML($params['shiptoattentionname']);
            $lbl->shiptoPhoneNumber = Infomodus_Upslabel_Helper_Help::escapeXML($params['shiptophonenumber']);
            $lbl->shiptoAddressLine1 = trim(Infomodus_Upslabel_Helper_Help::escapeXML($params['shiptoaddressline1']));
            $lbl->shiptoAddressLine2 = trim(Infomodus_Upslabel_Helper_Help::escapeXML($params['shiptoaddressline2']));
            $lbl->shiptoCity = Infomodus_Upslabel_Helper_Help::escapeXML($params['shiptocity']);
            $lbl->shiptoStateProvinceCode = Infomodus_Upslabel_Helper_Help::escapeXML($configOptions->getProvinceCode($params['shiptostateprovincecode']));
            $lbl->shiptoPostalCode = Infomodus_Upslabel_Helper_Help::escapeXML($params['shiptopostalcode']);
            $lbl->shiptoCountryCode = Infomodus_Upslabel_Helper_Help::escapeXML($params['shiptocountrycode']);
            $lbl->residentialAddress = $params['residentialaddress'];


            $lbl->shipfromCompanyName = Infomodus_Upslabel_Helper_Help::escapeXML(Mage::getStoreConfig('upslabel/shipfrom/companyname'));
            $lbl->shipfromAttentionName = Infomodus_Upslabel_Helper_Help::escapeXML(Mage::getStoreConfig('upslabel/shipfrom/attentionname'));
            $lbl->shipfromPhoneNumber = Infomodus_Upslabel_Helper_Help::escapeXML(Mage::getStoreConfig('upslabel/shipfrom/phonenumber'));
            $lbl->shipfromAddressLine1 = Infomodus_Upslabel_Helper_Help::escapeXML(Mage::getStoreConfig('upslabel/shipfrom/addressline1'));
            $lbl->shipfromCity = Infomodus_Upslabel_Helper_Help::escapeXML(Mage::getStoreConfig('upslabel/shipfrom/city'));
            $lbl->shipfromStateProvinceCode = Infomodus_Upslabel_Helper_Help::escapeXML(Mage::getStoreConfig('upslabel/shipfrom/stateprovincecode'));
            $lbl->shipfromPostalCode = Infomodus_Upslabel_Helper_Help::escapeXML(Mage::getStoreConfig('upslabel/shipfrom/postalcode'));
            $lbl->shipfromCountryCode = Infomodus_Upslabel_Helper_Help::escapeXML(Mage::getStoreConfig('upslabel/shipfrom/countrycode'));

            $lbl->serviceCode = array_key_exists('serviceCode',$params)?$params['serviceCode']:'';
            $lbl->serviceDescription = $configMethod->getUpsMethodName(array_key_exists('serviceCode',$params)?$params['serviceCode']:'');

            $lbl->weightUnits = array_key_exists('weightunits',$params)?$params['weightunits']:'';
            $lbl->weightUnitsDescription = Infomodus_Upslabel_Helper_Help::escapeXML(array_key_exists('weightunitsdescription',$params)?$params['weightunitsdescription']:'');

            $lbl->includeDimensions = array_key_exists('includedimensions',$params)?$params['includedimensions']:'';
            $lbl->unitOfMeasurement = array_key_exists('unitofmeasurement',$params)?$params['unitofmeasurement']:'';
            $lbl->unitOfMeasurementDescription = Infomodus_Upslabel_Helper_Help::escapeXML(array_key_exists('unitofmeasurementdescription',$params)?$params['unitofmeasurementdescription']:'');

            $lbl->packagingReferenceNumberCode = Mage::getStoreConfig('upslabel/profile/packagingreferencenumbercode');
            $lbl->packagingReferenceNumberValue = Mage::getStoreConfig('upslabel/profile/packagingreferencenumbervalue');

            $lbl->codYesNo = array_key_exists('cod',$params)?$params['cod']:'';
            $lbl->currencyCode = array_key_exists('currencycode',$params)?$params['currencycode']:'';
            $lbl->codMonetaryValue = array_key_exists('codmonetaryvalue',$params)?$params['codmonetaryvalue']:'';
            $lbl->codFundsCode = array_key_exists('codfundscode',$params)?$params['codfundscode']:'';
            $lbl->carbon_neutral = array_key_exists('carbon_neutral',$params)?$params['carbon_neutral']:'';
            if(array_key_exists('invoicelinetotalyesno',$params) && $params['invoicelinetotalyesno']>0){
                $lbl->invoicelinetotal = array_key_exists('invoicelinetotal',$params)?$params['invoicelinetotal']:'';
            }
            else {
                $lbl->invoicelinetotal = '';
            }
            $lbl->testing = $params['testing'];

            if ($type == 'shipment') {
                $upsl = $lbl->getShip();
                if($params['default_return']==1){
                    $upsl2 = $lbl->getShipFrom();
                }
            }
            else if($type == 'refund') {
                $upsl = $lbl->getShipFrom();
            }
            else if($type == 'ajaxprice_shipment') {
                $upsl = $lbl->getShipPrice();
                echo $upsl;
                exit;
            }
            else if($type == 'ajaxprice_refund'){
                $upsl = $lbl->getShipPriceFrom();
                echo $upsl;
                exit;
            }
            if (!array_key_exists('error', $upsl) || !$upsl['error']) {
                foreach($upsl['arrResponsXML'] AS $upsl_one){
                    $upslabel = Mage::getModel('upslabel/upslabel');
                    $upslabel->setTitle('Order ' . $order_id . ' TN' . $upsl_one['trackingnumber']);
                    $upslabel->setOrderId($order_id);
                    $upslabel->setShipmentId($shipment_id);
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
                $upsl_one_def='';
                if($params['default_return']==1){
                    if (!array_key_exists('error', $upsl2) || !$upsl2['error']) {
                        foreach($upsl2['arrResponsXML'] AS $upsl_one){
                            $upslabel = Mage::getModel('upslabel/upslabel');
                            $upslabel->setTitle('Order ' . $order_id . ' TN' . $upsl_one['trackingnumber']);
                            $upslabel->setOrderId($order_id);
                            $upslabel->setShipmentId($shipment_id);
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
                    }
                }
                if ($type == 'shipment') {
                    $backLink = $this->getUrl('adminhtml/sales_order_shipment/view/shipment_id/' . $shipment_id);

                }
                else {
                    $backLink = $this->getUrl('adminhtml/sales_order_creditmemo/view/creditmemo_id/' . $shipment_id);
                }
                if ($params['addtrack'] == 1 && $type == 'shipment') {
                    $trTitle = 'United Parcel Service';
                    $shipment = Mage::getModel('sales/order_shipment')->load($shipment_id);
                    foreach($upsl['arrResponsXML'] AS $upsl_one1){
                        $track = Mage::getModel('sales/order_shipment_track')
                            ->setNumber(trim($upsl_one1['trackingnumber']))
                            ->setCarrierCode('ups')
                            ->setTitle($trTitle);
                        $shipment->addTrack($track);
                    }
                    $shipment->save();
                }
                Mage::register('order_id', $order_id);
                Mage::register('shipment_id', $shipment_id);
                Mage::register('upsl', $upsl);
                if($params['default_return']==1){
                    Mage::register('upsl2', $upsl2);
                }
                Mage::register('backLink', $backLink);
                Mage::register('type', $type);
                Mage::register('error', array());
            }
            else {
                Mage::register('error', $upsl);
            }
        }
        else {
            if ($type == 'shipment') {
                $backLink = $this->getUrl('adminhtml/sales_order_shipment/view/shipment_id/' . $shipment_id);

            }
            else {
                $backLink = $this->getUrl('adminhtml/sales_order_creditmemo/view/creditmemo_id/' . $shipment_id);
            }
            Mage::register('order_id', $collection->getOrderId());
            Mage::register('shipment_id', $shipment_id);
            Mage::register('upsl', $colls->getData());
            Mage::register('backLink', $backLink);
            Mage::register('type', $type);
            Mage::register('error', array());
        }
        $this->renderLayout();
    }

    public function intermediateAction()
    {
        $configOptions = new Infomodus_Upslabel_Model_Config_Options;
        $configMethod = new Infomodus_Upslabel_Model_Config_Upsmethod;
        $this->loadLayout();
        //$block = $this->getLayout()->getBlock('intermediate');
        $order_id = $this->getRequest()->getParam('order_id');
        $type = $this->getRequest()->getParam('type');
        $this->imOrder = Mage::getModel('sales/order')->load($order_id);
        Mage::register('order', $this->imOrder);
        $shippingAddress = $this->imOrder->getShippingAddress();
        Mage::register('shipTo', $shippingAddress);
        $shipment_id = $this->getRequest()->getParam('shipment_id');
        $discountAmount = 0;
        $taxAmount = 0;
        if ($type == 'shipment') {
            $this->imShipment = Mage::getModel('sales/order_shipment')->load($shipment_id);
            $shippingAmount = $this->imShipment->getShippingAddress()->getOrder()->getData();
            Mage::register('shippingAmount', $shippingAmount['shipping_amount']);

        }
        else {
            $this->imShipment = Mage::getModel('sales/order_creditmemo')->load($shipment_id);
        }
        Mage::register('shipment', $this->imShipment);
        Mage::register('type', $type);
        $shipmentAllItems = $this->imShipment->getAllItems();
        $totalPrice = 0;
        $totalWight = 0;
        $totalShipmentQty = 0;
        foreach ($shipmentAllItems AS $item) {
            $itemData=$item->getData();
            $totalPrice += $itemData['price'] * $itemData['qty'];
            $totalWight += $itemData['weight'] * $itemData['qty'];
            $totalShipmentQty += $itemData['qty'];
        }
        $totalQty = 0;
        foreach ($this->imOrder->getAllItems() AS $item) {
            $itemData=$item->getData();
            $totalQty += $itemData['qty_ordered'];
        }
        if ($type == 'shipment') {
            $sootItems = $totalShipmentQty/$totalQty;
            if(Mage::getStoreConfig('upslabel/profile/cod_discount') == 1){
               $discountAmount = $shippingAmount['discount_amount']*$sootItems;
            }
            $taxAmount = $shippingAmount['tax_amount']*$sootItems;
        }
        Mage::register('shipmentTotalPrice', $totalPrice-abs($discountAmount)+$taxAmount);
        Mage::register('shipmentTotalWeight', $totalWight);
        $ship_method = $this->imOrder->getShippingMethod();
        $shipByUps = preg_replace("/^ups_.{1,4}$/", 'ups', $ship_method);
        $shipByUpsCode = $configMethod->getUpsMethodNumber(preg_replace("/^ups_(.{2,4})$/", '$1', $ship_method));
        //echo $shipByUpsCode;
        Mage::register('shipByUps', $shipByUps);
        Mage::register('shipByUpsCode', $shipByUpsCode);
        $shipByUpsMethodName = $configMethod->getUpsMethodName($shipByUpsCode);
        Mage::register('shipByUpsMethodName', $shipByUpsMethodName);
        Mage::register('shipByUpsMethods', $configMethod->getUpsMethods());
        Mage::register('unitofmeasurement', $configOptions->getUnitOfMeasurement());
        Mage::register('paymentmethod', $this->imOrder->getPayment()->getMethodInstance()->getCode());
        $this->renderLayout();
    }

    public function deletelabelAction()
    {
        $order_id = $this->getRequest()->getParam('order_id');
        $this->loadLayout();
        $this->_addLeft($this->getLayout()->createBlock('upslabel/adminhtml_upslabel_label_del'));
        $this->renderLayout();
    }

    public function printAction()
    {
        $imname = $this->getRequest()->getParam('imname');
        $path = Mage::getBaseDir('media') . DS . 'upslabel' . DS . 'label' . DS;
        $path_url = Mage::getBaseUrl('media') . DS . 'upslabel' . DS . 'label' . DS;
        echo '<html>
            <head>
            <title>Print Shipping Label</title>
            </head>
            <body>
            <img src="' . $path_url . $imname . '" />
            <script>
            window.onload = function(){window.print();}
            </script>
            </body>
            </html>';
        exit;
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('upslabel/upslabel')->load($id);

        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }

            Mage::register('upslabel_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('upslabel/items');

            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('upslabel/adminhtml_upslabel_edit'))
                ->_addLeft($this->getLayout()->createBlock('upslabel/adminhtml_upslabel_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('upslabel')->__('Item does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            $model = Mage::getModel('upslabel/upslabel');
            $model->setData($data)
                ->setId($this->getRequest()->getParam('id'));

            try {
                if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
                    $model->setCreatedTime(now())
                        ->setUpdateTime(now());
                } else {
                    $model->setUpdateTime(now());
                }

                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('upslabel')->__('Item was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('upslabel')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('upslabel/upslabel');

                $model->setId($this->getRequest()->getParam('id'))
                    ->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massDeleteAction()
    {
        $upslabelIds = $this->getRequest()->getParam('upslabel');
        if (!is_array($upslabelIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($upslabelIds as $upslabelId) {
                    $upslabel = Mage::getModel('upslabel/upslabel')->load($upslabelId);
                    $upslabel->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($upslabelIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massStatusAction()
    {
        $upslabelIds = $this->getRequest()->getParam('upslabel');
        if (!is_array($upslabelIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($upslabelIds as $upslabelId) {
                    $upslabel = Mage::getSingleton('upslabel/upslabel')
                        ->load($upslabelId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($upslabelIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function exportCsvAction()
    {
        $fileName = 'upslabel.csv';
        $content = $this->getLayout()->createBlock('upslabel/adminhtml_upslabel_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName = 'upslabel.xml';
        $content = $this->getLayout()->createBlock('upslabel/adminhtml_upslabel_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType = 'application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK', '');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename=' . $fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
}