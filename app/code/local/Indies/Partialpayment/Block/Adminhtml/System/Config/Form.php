<?php

class Indies_Partialpayment_Block_Adminhtml_System_Config_Form extends Mage_Adminhtml_Block_System_Config_Form
{
 	public function initFields($fieldset, $group, $section, $fieldPrefix='', $labelPrefix='')
    {
		$m = new Mage;
		$version = $m->getVersion();

		if ($version >= '1.7.0.2')
		{
				parent::initFields($fieldset, $group, $section, $fieldPrefix, $labelPrefix);
		}
		else
		{
			if (!$this->_configDataObject) {
				$this->_initObjects();
			}

			// Extends for config data
			$configDataAdditionalGroups = array();

			foreach ($group->fields as $elements) {
				$elements = (array)$elements;
				// sort either by sort_order or by child node values bypassing the sort_order
				if ($group->sort_fields && $group->sort_fields->by) {
					$fieldset->setSortElementsByAttribute((string)$group->sort_fields->by,
						($group->sort_fields->direction_desc ? SORT_DESC : SORT_ASC)
					);
				} else {
					usort($elements, array($this, '_sortForm'));
				}

				foreach ($elements as $e) {
					if (!$this->_canShowField($e)) {
						continue;
					}

					/**
					 * Look for custom defined field path
					 */
					$path = (string)$e->config_path;
					if (empty($path)) {
						$path = $section->getName() . '/' . $group->getName() . '/' . $fieldPrefix . $e->getName();
					} elseif (strrpos($path, '/') > 0) {
						// Extend config data with new section group
						$groupPath = substr($path, 0, strrpos($path, '/'));
						if (!isset($configDataAdditionalGroups[$groupPath])) {
							$this->_configData = $this->_configDataObject->extendConfig(
								$groupPath,
								false,
								$this->_configData
							);
							$configDataAdditionalGroups[$groupPath] = true;
						}
					}

					$id = $section->getName() . '_' . $group->getName() . '_' . $fieldPrefix . $e->getName();

					if (isset($this->_configData[$path])) {
						$data = $this->_configData[$path];
						$inherit = false;
					} else {
						$data = $this->_configRoot->descend($path);
						$inherit = true;
					}
					if ($e->frontend_model) {
						$fieldRenderer = Mage::getBlockSingleton((string)$e->frontend_model);
					} else {
						$fieldRenderer = $this->_defaultFieldRenderer;
					}

					$fieldRenderer->setForm($this);
					$fieldRenderer->setConfigData($this->_configData);

					$helperName = $this->_configFields->getAttributeModule($section, $group, $e);
					$fieldType  = (string)$e->frontend_type ? (string)$e->frontend_type : 'text';
					$name       = 'groups['.$group->getName().'][fields]['.$fieldPrefix.$e->getName().'][value]';
					$label      =  Mage::helper($helperName)->__($labelPrefix).' '.Mage::helper($helperName)->__((string)$e->label);
					$hint       = (string)$e->hint ? Mage::helper($helperName)->__((string)$e->hint) : '';

					if ($e->backend_model) {
						$model = Mage::getModel((string)$e->backend_model);
						if (!$model instanceof Mage_Core_Model_Config_Data) {
							Mage::throwException('Invalid config field backend model: '.(string)$e->backend_model);
						}
						$model->setPath($path)
							->setValue($data)
							->setWebsite($this->getWebsiteCode())
							->setStore($this->getStoreCode())
							->afterLoad();
						$data = $model->getValue();
					}

					$comment    = $this->_prepareFieldComment($e, $helperName, $data);
					$tooltip    = $this->_prepareFieldTooltip($e, $helperName);

					if ($e->depends) {
						foreach ($e->depends->children() as $dependent) {
							/* @var $dependent Mage_Core_Model_Config_Element */
							$dependentId = $section->getName()
								. '_' . $group->getName()
								. '_' . $fieldPrefix
								. $dependent->getName();
							$shouldBeAddedDependence = true;
							$dependentValue          = (string) $dependent;
							if (isset($dependent['separator'])) {
								$dependentValue = explode((string)$dependent['separator'], $dependentValue);
							}
							$dependentFieldName      = $fieldPrefix . $dependent->getName();
							$dependentField          = $group->fields->$dependentFieldName;
							/*
							 * If dependent field can't be shown in current scope and real dependent config value
							 * is not equal to preferred one, then hide dependence fields by adding dependence
							 * based on not shown field (not rendered field)
							 */
							if (!$this->_canShowField($dependentField)) {
								$dependentFullPath = $section->getName()
									. '/' . $group->getName()
									. '/' . $fieldPrefix
									. $dependent->getName();
								$dependentValueInStore = Mage::getStoreConfig($dependentFullPath, $this->getStoreCode());
								if (is_array($dependentValue)) {
									$shouldBeAddedDependence = !in_array($dependentValueInStore, $dependentValue);
								} else {
									$shouldBeAddedDependence = $dependentValue != $dependentValueInStore;
								}
							}
							if($shouldBeAddedDependence) {
								$this->_getDependence()
									->addFieldMap($id, $id)
									->addFieldMap($dependentId, $dependentId)
									->addFieldDependence($id, $dependentId, $dependentValue);
							}
						}
					}

					$field = $fieldset->addField($id, $fieldType, array(
						'name'                  => $name,
						'label'                 => $label,
						'comment'               => $comment,
						'tooltip'               => $tooltip,
						'hint'                  => $hint,
						'value'                 => $data,
						'inherit'               => $inherit,
						'class'                 => $e->frontend_class,
						'field_config'          => $e,
						'scope'                 => $this->getScope(),
						'scope_id'              => $this->getScopeId(),
						'scope_label'           => $this->getScopeLabel($e),
						'can_use_default_value' => $this->canUseDefaultValue((int)$e->show_in_default),
						'can_use_website_value' => $this->canUseWebsiteValue((int)$e->show_in_website),
					));
					$this->_prepareFieldOriginalData($field, $e);

					if (isset($e->validate)) {
						$field->addClass($e->validate);
					}

					if (isset($e->frontend_type)
						&& 'multiselect' === (string)$e->frontend_type
						&& isset($e->can_be_empty)
					) {
						$field->setCanBeEmpty(true);
					}

					$field->setRenderer($fieldRenderer);

					if ($e->source_model) {
						// determine callback for the source model
						$factoryName = (string)$e->source_model;
						$method = false;
						if (preg_match('/^([^:]+?)::([^:]+?)$/', $factoryName, $matches)) {
							array_shift($matches);
							list($factoryName, $method) = array_values($matches);
						}

						$sourceModel = Mage::getSingleton($factoryName);
						if ($sourceModel instanceof Varien_Object) {
							$sourceModel->setPath($path);
						}
						if ($method) {
							if ($fieldType == 'multiselect') {
								$optionArray = $sourceModel->$method();
							} else {
								$optionArray = array();
								foreach ($sourceModel->$method() as $value => $label) {
									$optionArray[] = array('label' => $label, 'value' => $value);
								}
							}
						} else {
							$optionArray = $sourceModel->toOptionArray($fieldType == 'multiselect');
						}
						$field->setValues($optionArray);
					}
				}
			}
			return $this;
		}
	}
}