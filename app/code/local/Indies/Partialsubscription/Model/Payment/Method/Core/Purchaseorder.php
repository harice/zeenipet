<?php


class Indies_Partialsubscription_Model_Payment_Method_Core_Purchaseorder extends Mage_Payment_Model_Method_Purchaseorder
{
    protected static $poIsset = false;

    /**
     * Validate payment method information object
     *
     * @param   Mage_Payment_Model_Info $info
     * @return  Mage_Payment_Model_Abstract
     */
    public function validate()
    {
        if (Indies_Partialsubscription_Model_Subscription::isIterating()) {
            if (!self::$poIsset) {
                $info = $this->getInfoInstance();
                if ($info) {
                    $poNumber = $info->getData('po_number');
                    if ($poNumber) {
                        $info->setPoNumber($poNumber . '-' . Mage::getModel('core/date')->date('dmy'));
                        self::$poIsset = true;
                    }
                }
            }

            return $this;
        } else {
            return parent::validate();
        }
    }
}