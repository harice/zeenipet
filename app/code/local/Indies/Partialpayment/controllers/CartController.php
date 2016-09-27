<?php

require_once 'Mage/Checkout/controllers/CartController.php';

class Indies_Partialpayment_CartController extends Mage_Checkout_CartController
{
	 /**
	 * Initialize shipping information
	 */
    public function estimatePostAction()
    {
        $country    = (string) $this->getRequest()->getParam('country_id');
        $postcode   = (string) $this->getRequest()->getParam('estimate_postcode');
        $city       = (string) $this->getRequest()->getParam('estimate_city');
        $regionId   = (string) $this->getRequest()->getParam('region_id');
        $region     = (string) $this->getRequest()->getParam('region');

        $this->_getQuote()->getShippingAddress()
            ->setCountryId($country)
            ->setCity($city)
            ->setPostcode($postcode)
            ->setRegionId($regionId)
            ->setRegion($region)
            ->setCollectShippingRates(true);
        $this->_getQuote()->save();

		$this->_redirect('checkout/cart', array('recalculate_quote' => 1)); 
    }


	public function estimateUpdatePostAction()
    {
        $code = (string)$this->getRequest()->getParam('estimate_method');		
        if (!empty($code)) {
            $this->_getQuote()->getShippingAddress()->setShippingMethod($code)->collectTotals()->save();
        }
        $this->_redirect('checkout/cart', array('recalculate_quote' => 1));
    }


	public function addAction()
    {
		$partialpaymentHelper = Mage::helper('partialpayment/partialpayment');

		if ($partialpaymentHelper->isPartialPaymentOptionFlexyPayments()) {
			Mage::getSingleton('core/session')->setJquery(1);
		}
		elseif ($partialpaymentHelper->isPartialPaymentOptional()) {
			Mage::getSingleton('core/session')->setJquery(1);
		}

		parent::addAction();
	}


	public function indexAction()
    {
		$partialpaymentHelper = Mage::helper('partialpayment/partialpayment');

		if (!$partialpaymentHelper->isPartialPaymentOptionFlexyPayments() && !$partialpaymentHelper->isPartialPaymentOptional()) {
			Indies_Fee_Model_Fee::$wholeCartFlag = 1;
			Mage::getSingleton('core/session')->setPP(1);
		}
		if(isset($_POST['allow_partial_payment']))
		{
			Indies_Fee_Model_Fee::$wholeCartFlag = $_POST['allow_partial_payment'];
			Mage::getSingleton('core/session')->setPP($_POST['allow_partial_payment']);		
		}

		$cart = Mage::getSingleton('checkout/cart');
		$cart->getQuote()->getItemsCount();
		Mage::getSingleton('core/session')->setPcart($cart->getQuote()->getItemsCount());

		if(Mage::app()->getRequest()->getParam('recalculate_quote'))
		{
			$this->_redirect('checkout/cart');
		}

		parent::indexAction();
	}


	protected function _emptyShoppingCart()
    {
       try {
            $this->_getCart()->truncate()->save();
            $this->_getSession()->setCartWasUpdated(true);
			Mage::getSingleton('core/session')->unsPP();
        } catch (Mage_Core_Exception $exception) {
            $this->_getSession()->addError($exception->getMessage());
        } catch (Exception $exception) {
            $this->_getSession()->addException($exception, $this->__('Cannot update shopping cart.'));
        }
    }


	public function deleteAction()
    {
		$cart = $this->_getCart();

        if ($cart->getQuote()->getItemsCount()==1) {
			Mage::getSingleton('core/session')->unsPP();
		}

        $id = (int) $this->getRequest()->getParam('id');

        if ($id) {
            try {
                $this->_getCart()->removeItem($id)->save();
            } catch (Exception $e) {
                $this->_getSession()->addError($this->__('Cannot remove the item.'));
                Mage::logException($e);
            }
        }
        $this->_redirectReferer(Mage::getUrl('*/*'));
    }
}