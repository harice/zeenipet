<?php
$installer = $this;

$installer->startSetup();

$installer->run("");

$installer->addAttribute('customer', 'credit_amount', array(
    'label'         => 'Credit Amount',
    'visible'       => false,
    'required'      => false,
    'type'          => 'varchar',
    'input'         => 'text',
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible'           => false,
    'required'          => false,
    'user_defined'      => true,
    'default'           => '',
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'visible_on_front'  => false,
    'unique'            => false,
    'sort_order'    => '2001',
));

$attributeId = $installer->getAttribute('customer', 'credit_amount', 'attribute_id');

if ($attributeId) {
    $installer->run("
        INSERT IGNORE INTO {$this->getTable('customer/form_attribute')} VALUES ('adminhtml_customer', {$attributeId});
    ");
}

$installer->endSetup();