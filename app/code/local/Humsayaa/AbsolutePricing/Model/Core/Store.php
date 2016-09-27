<?php
/**
 * Humsayaa AbsolutePricing
 *
 * Store model
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      AbsolutePricing Team <enquiries@absolutepricing.com>
**/

class Humsayaa_AbsolutePricing_Model_Core_Store extends Mage_Core_Model_Store
{
    /**
     * Round price
     *
     * @param mixed $price
     * @return float
     */
    public function roundPrice($price)
    {
		return (float)$price;
    }

}