<?php

class Indies_Partialsubscription_Model_Web_Service_Client_Response_Simple extends Varien_Object
{

    protected $_fields;

    public function setOnceFields(array $fields)
    {
        $this->_fields = $fields;
    }

    public function reset()
    {
        $this->setData(array());
        return $this;
    }

    public function setData($key, $value = null)
    {
        if ($key instanceof StdClass) {
            foreach ($key as $prop => $value) {
                parent::setData($prop, $value);
            }
            return $this;
        } else {
            return parent::setData($key, $value);
        }
    }
}
