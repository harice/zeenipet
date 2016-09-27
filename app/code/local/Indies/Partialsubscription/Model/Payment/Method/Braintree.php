<?php
//$path = Mage::getBaseDir('app/code/local/Braintree/lib/Braintree.php');
//require_once($path);

class Indies_Partialsubscription_Model_Payment_Method_Braintree extends Indies_Partialsubscription_Model_Payment_Method_Abstract
{
	
	protected $_formBlockType = 'braintree/form';
    protected $_infoBlockType = 'payment/info';

    protected $_code = 'braintree';
    protected $_isGateway               = true;
    protected $_canAuthorize            = true;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = false;
    protected $_canRefund               = true;
    protected $_canVoid                 = true;
    protected $_canUseInternal          = true;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = true;
    protected $_canSaveCc               = false;
    protected $_merchantAccountId       = '';
    protected $_useVault                = false;
	
	public function __construct()
    {
		
        parent::__construct();
        if ($this->getConfigData('active') == 1)
        {		
            Braintree_Configuration::environment($this->getConfigData('environment'));
            Braintree_Configuration::merchantId($this->getConfigData('merchant_id'));
            Braintree_Configuration::publicKey($this->getConfigData('public_key'));
            Braintree_Configuration::privateKey($this->getConfigData('private_key'));
            $this->_merchantAccountId = $this->getConfigData('merchant_account_id');
            $this->_useVault = $this->getConfigData('use_vault');
        }
    }
    

    /**
     * This function is run when subscription is created and new order creates
     * @param Indies_Partialsubscription_Model_Subscription $Subscription
     * @param Mage_Sales_Model_Order     $Order
     * @param Mage_Sales_Model_Quote     $Quote
     * @return Indies_Partialsubscription_Model_Payment_Method_Abstract
     */
    public function onSubscriptionCreate(Indies_Partialsubscription_Model_Subscription $Subscription, Mage_Sales_Model_Order $Order, Mage_Sales_Model_Quote $Quote)
    {
        $this->createSubscription($Subscription, $Order, $Quote);
        return $this;
    }

    public function createSubscription($Subscription, $Order, $Quote)
    {

       /*
	   $orders = Mage::getModel('sales/order')->getCollection()
        ->setOrder('increment_id','DESC')
        ->setPageSize(1)
        ->setCurPage(1);

		echo $orders->getFirstItem()->getIncrementId();*/

        return $this;

    }

    /**
     * Processes payment for specified order
     * @param Mage_Sales_Model_Order $Order
     * @return
     */
    public function processOrder(Mage_Sales_Model_Order $PrimaryOrder, Mage_Sales_Model_Order $Order = null)
    {

    }
	
}
