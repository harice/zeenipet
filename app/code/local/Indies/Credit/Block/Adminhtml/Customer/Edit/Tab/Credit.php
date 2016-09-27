<?php 
class Indies_Credit_Block_Adminhtml_Customer_Edit_Tab_Credit
 extends Mage_Adminhtml_Block_Template
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    public function __construct()
    {
        $this->setTemplate('credit/credit.phtml');
    }

    public function getCredittabInfo(){
        $customer = Mage::registry('current_customer');
        $customtab = 'Credit Options Contents Here...';
        return $customtab;
    }

    /**
    * Return Tab label
    *
    * @return string
    **/
    public function getTabLabel()
    {
        return $this->__('Credit');
    }

    /**
    * Return Tab title
    *
    * @return string
    **/
    public function getTabTitle()
    {
        return $this->__('Credit Options');
    }
 
    /**
    * Can show tab in tabs
    *
    * @return boolean
    **/
    public function canShowTab()
    {
        $customer = Mage::registry('current_customer');
        return (bool)$customer->getId();
    }
 
    /**
    * Tab is hidden
    *
    * @return boolean
    **/
    public function isHidden()
    {
        return false;
    }
 
    /**
    * Defines after which tab, this tab should be rendered
    *
    * @return string
    **/
    public function getAfter()
    {
        return 'tags';
    }
}
?>