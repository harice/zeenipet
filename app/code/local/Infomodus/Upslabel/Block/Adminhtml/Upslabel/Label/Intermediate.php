<?php
/*
 * Author Rudyuk Vitalij Anatolievich
 * Email rvansp@gmail.com
 * Blog www.cervic.info
 */
?>
<?php

class Infomodus_Upslabel_Block_Adminhtml_Upslabel_Label_Intermediate extends Mage_Adminhtml_Block_Widget_Tabs {

    protected function _beforeToHtml() {

        echo '<form action="'.$this->getUrl('upslabel/adminhtml_upslabel/showlabel/order_id/' . $this->getRequest()->getParam('order_id')).'" method="get">';
        $onlyups = Mage::getStoreConfig('upslabel/profile/onlyups');
        if($onlyups==0){
        echo '<select name="intermediate">
<option value="1DM">Next Day Air Early AM</option>
<option value="1DA">Next Day Air</option>
<option value="1DP">Next Day Air Saver</option>
<option value="2DM">2nd Day Air AM</option>
<option value="2DA">2nd Day Air</option>
<option value="3DS">3 Day Select</option>
<option value="GND">Ground</option>
<option value="EP">Worldwide Express Plus</option>
<option value="ES">Worldwide Express</option>
<option value="SV">Worldwide Saver (Express)</option>
<option value="EX">Worldwide Expedited</option>
<option value="ST">Standard</option>
<option value="ND">UPS Express (NA1)</option>
</select><br /><br />';
        }
           
echo '<label for="shipmentDescription">Shipment Description (only for international shipments)</label><br />
    <textarea id="shipmentDescription" name="description" cols="100" rows="10">'.Mage::getStoreConfig('upslabel/profile/description').'</textarea>
<br />    
<input type="submit" value="Continue">
</form>';

        return parent::_beforeToHtml();
    }
}