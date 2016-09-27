<?php


class Indies_Partialsubscription_Model_Payment_Method_Core_Argofire extends Acreativellc_Argofire_Model_Argofire
{
    /**
     * Validate payment method information object
     *
     * @param   Mage_Payment_Model_Info $info
     * @return  Mage_Payment_Model_Abstract
     */
    public function validate()
    {

        if (Indies_Partialsubscription_Model_Subscription::isIterating()) {
            return $this;
        } else {
            return parent::validate();
        }
    }

    public function capture(Varien_Object $payment, $amount)
    {
        if (Indies_Partialsubscription_Model_Subscription::isIterating()) {
            $Subscription = Indies_Partialsubscription_Model_Subscription::getInstance()->processPayment($payment->getOrder());
            return $this;
        }

        if (Mage::getModel('sarp/sequence')->load($payment->getOrder()->getId(), 'order_id')->getId()) {
            if (Mage::getModel('sarp/sequence')->load($payment->getOrder()->getId(), 'order_id')->getStatus() != Indies_Partialsubscription_Model_Sequence::STATUS_FAILED) {
                return $this;
            }
        }

        return parent::capture($payment, $amount);
    }

    public function authorize(Varien_Object $payment, $amount)
    {
        if (Indies_Partialsubscription_Model_Subscription::isIterating()) {
            $Subscription = Indies_Partialsubscription_Model_Subscription::getInstance()->processPayment($payment->getOrder());
            return $this;
        }
        return parent::authorize($payment, $amount);
    }
}