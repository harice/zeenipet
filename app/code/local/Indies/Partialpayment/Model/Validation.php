<?php

class Indies_Partialpayment_Model_Validation extends Mage_Core_Model_Config_Data {

	public static $totalInstallments;
	public static $minimumOrderAmount;
	public static $calculateInstallmentsOn;
	public static $downPayment;
	public static $totalNumberOfDays;
	public static $surchargeValue;
	public static $surchargeValues;
	public static $calculatePreOrderDiscountOn;
	public static $preOrderDiscount;
	public static $defaultCreditAmount;
	public static $remindBeforeDays;

    public function save()
    {
		$data = $this->_getData('fieldset_data');

		if(isset($data['partial_payment_options']) && $data['partial_payment_options'] == '2_installments') {
			self::$totalInstallments = 2;
		}

		if(isset($data['total_installments'])) {
			self::$totalInstallments = $data['total_installments'];
			if (self::$totalInstallments < 2) {
				Mage::throwException('Total Installments value must be at least 2.');
			}
		}

		if(isset($data['minimum_order_amount'])) {
			self::$minimumOrderAmount = $data['minimum_order_amount'];
			if (self::$minimumOrderAmount < 0) {
				Mage::throwException('Minimum Order Amount value must be at least 0.');
			}
		}

		if(isset($data['installment_calculation_type'])) {
			self::$calculateInstallmentsOn = $data['installment_calculation_type'];

			if(isset($data['first_installment_amount'])) {
				self::$downPayment = $data['first_installment_amount'];

				if (self::$calculateInstallmentsOn == 1 && self::$downPayment <= 0) {
					Mage::throwException('Down Payment value must be greater than 0.');
				}
				elseif (self::$calculateInstallmentsOn == 2 && (self::$downPayment <= 0 || self::$downPayment >= 100)) {
					Mage::throwException('Down Payment value must be between 0 and 100.');
				}
			}

			if(isset($data['total_no_days'])) {
				self::$totalNumberOfDays = $data['total_no_days'];

				if (self::$totalNumberOfDays < 1) {
					Mage::throwException('Total Number of Days must be greater than 0.');
				}
			}

			if(isset($data['single_surcharge_value'])) {
				self::$surchargeValue = $data['single_surcharge_value'];

				if (self::$calculateInstallmentsOn == 1 && self::$surchargeValue < 0) {
					Mage::throwException('Surcharge Value must be at least 0.');
				}
				elseif (self::$calculateInstallmentsOn == 2 && (self::$surchargeValue < 0 || self::$surchargeValue > 100)) {
					Mage::throwException('Surcharge Value must be between 0 and 100.');
				}
			}

			if(isset($data['multiple_surcharge_values'])) {
				self::$surchargeValues = $data['multiple_surcharge_values'];
				$multiple_surcharge_values = explode(',', self::$surchargeValues);

				if (self::$totalInstallments != count($multiple_surcharge_values)) {
					Mage::throwException('Please, enter ' . self::$totalInstallments . ' surcharge values.');
				}

				for ($i=0;$i<count($multiple_surcharge_values);$i++) {
					if ($i == 0 && $multiple_surcharge_values[$i] != 0) {
						Mage::throwException('First surcharge value must be 0.');
					}
					elseif (self::$calculateInstallmentsOn == 1 && $multiple_surcharge_values[$i] < 0) {
						Mage::throwException('Surcharge Value must be at least 0.');
					}
					elseif (self::$calculateInstallmentsOn == 2 && ($multiple_surcharge_values[$i] < 0 || $multiple_surcharge_values[$i] > 100)) {
						Mage::throwException('Surcharge Value must be between 0 and 100.');
					}
				}
			}
		}

		if(isset($data['outofstock_discount_calculation_type'])) {
			self::$calculatePreOrderDiscountOn = $data['outofstock_discount_calculation_type'];

			if(isset($data['outofstock_discount_amount'])) {
				self::$preOrderDiscount = $data['outofstock_discount_amount'];

				if (self::$calculatePreOrderDiscountOn == 1 && self::$preOrderDiscount < 0) {
					Mage::throwException('Pre Order Discount value must be at least 0.');
				}
				elseif (self::$calculatePreOrderDiscountOn == 2 && (self::$preOrderDiscount < 0 || self::$preOrderDiscount > 100)) {
					Mage::throwException('Pre Order Discount value must be between 0 and 100.');
				}
			}
		}

		if(isset($data['default_credit_amount'])) {
			self::$defaultCreditAmount = $data['default_credit_amount'];
			if (self::$defaultCreditAmount < 0) {
				Mage::throwException('Default Credit Amount value must be at least 0.');
			}
		}

		if(isset($data['remind_before_days'])) {
			self::$remindBeforeDays = $data['remind_before_days'];

			if (self::$remindBeforeDays < 1) {
				Mage::throwException('Remind before days must be greater than 0.');
			}
		}

        return parent::save();
    }
}