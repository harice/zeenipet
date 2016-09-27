<?php
 
class Indies_Partialpaymentadmin_Model_Observer
{
	public function CreateProcessDataBefore($observer)
	{
		$postData = Mage::app()->getRequest()->getPost();
		//Mage::log($postData);

		if (!isset($postData['item'])) {
			return;
		}

		if (isset($postData['update_items']) && $postData['update_items']) {
			$partialpaymentHelper = Mage::helper('partialpayment/partialpayment');
			$items = $postData['item'];
			$quote = Mage::getSingleton('adminhtml/session_quote')->getQuote();

			if ($partialpaymentHelper->isApplyToWholeCart()) {
				foreach ($quote->getAllItems() as $id => $item) {
					if (isset($items[$item->getId()]['allow_partial_payment'])) {
						if (empty($items[$item->getId()]['allow_partial_payment']) || $items[$item->getId()]['allow_partial_payment'] == 0) {
							Indies_Fee_Model_Fee::$wholeCartFlag = 0;
							Mage::getSingleton('core/session')->setPP(0);
							return;
						}
					}
					else {
						Indies_Fee_Model_Fee::$wholeCartFlag = 0;
						Mage::getSingleton('core/session')->setPP(0);
						return;
					}
				}
				Indies_Fee_Model_Fee::$wholeCartFlag = $items[$item->getId()]['allow_partial_payment'];
				Mage::getSingleton('core/session')->setPP($items[$item->getId()]['allow_partial_payment']);
			}
			else {
				foreach ($quote->getAllItems() as $id => $item) {
					if (isset($items[$item->getId()]['allow_partial_payment'])) {
						$options = $item->getOptions();
						foreach ($options as $option) {
							if($option->getCode() == 'info_buyRequest')
							{
								$unserialized = unserialize($option->getValue());
								$unserialized['allow_partial_payment'] = $items[$item->getId()]['allow_partial_payment'];
								$option->setValue(serialize($unserialized));
							}
						}
						$item->setOptions($options)->save();
					}
				}
			}
		}
	}
}