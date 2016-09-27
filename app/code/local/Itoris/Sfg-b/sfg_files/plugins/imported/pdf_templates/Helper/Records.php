<?php 
/**
 * ITORIS
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the ITORIS's Magento Extensions License Agreement
 * which is available through the world-wide-web at this URL:
 * http://www.itoris.com/magento-extensions-license.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to sales@itoris.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extensions to newer
 * versions in the future. If you wish to customize the extension for your
 * needs please refer to the license agreement or contact sales@itoris.com for more information.
 *
 * @category   ITORIS
 * @package    ITORIS_SFG_PLUGIN_PDF_TEMPLATES
 * @copyright  Copyright (c) 2012 ITORIS INC. (http://www.itoris.com)
 * @license    http://www.itoris.com/magento-extensions-license.html  Commercial License
 */

class PdfTemplates_Helper_Records {

	public function sortRecords($records, $sort, $direction) {
		for ($i = 0; $i < count($records); $i++) {
			for ($j = 0; $j < count($records); $j++) {
				if ($j) {
					if (isset($records[$j][$sort])) {
						if ($sort == 'id') {
							$compareResult = ($records[$j - 1][$sort] < $records[$j][$sort]) ? -1 : 1;
						} else {
							$compareResult = strcmp($records[$j - 1][$sort], $records[$j][$sort]);
						}
						if (($compareResult == -1 && $direction)
							|| ($compareResult == 1 && !$direction)
						) {
							$tempRecord = $records[$j - 1];
							$records[$j - 1] = $records[$j];
							$records[$j] = $tempRecord;
						}
					}
				}
			}
		}
		return $records;
	}

	public function filterRows($rows, $filters) {
		$result = array();
		$filters = $this->prepareFilters($filters);
		foreach ($rows as $row) {
			$valid = false;
			$dontCheck = false;
			for ($i = 0; $i < count($filters); $i++) {
				if ($i) {
					if ($filters[$i - 1]['union'] == 'and' && !$valid) {
						$dontCheck = true;
					} elseif ($filters[$i - 1]['union'] == 'or') {
						if ($valid) {
							break;
						} else {
							$dontCheck = false;
						}
					}
				}
				if (!$dontCheck) {
					$valid = $this->checkValue($filters[$i], $row);
				}
			}
			if ($valid) {
				$result[] = $row;
			}
		}
		return $result;
	}

	public function checkValue($filter, $row) {
		$value = $row[$filter['name']];
		switch ($filter['condition']) {
			case 'like':
				return (bool)strstr($value, $filter['expr']);
			case 'not like':
				return !strstr($value, $filter['expr']);
			case '=':
				return $value == $filter['expr'];
			case '<>':
				return $value != $filter['expr'];
			case '<':
				return $value < $filter['expr'];
			case '<=':
				return $value <= $filter['expr'];
			case '>':
				return $value > $filter['expr'];
			case '>=':
				return $value >= $filter['expr'];
			default:
				return false;
		}
	}

	public function prepareFilters($filters) {
		$result = array();
		$filtersParams = explode('|', $filters);
		for ($i = 0; $i < count($filtersParams); $i++) {
			if (!($i % 4) && empty($filtersParams[$i])) {
				break;
			}
			switch ($i % 4) {
				case 0:
					$result[count($result)] = array();
					$result[count($result) - 1]['name'] = $filtersParams[$i];
					break;
				case 1:
					$result[count($result) - 1]['condition'] = $filtersParams[$i];
					break;
				case 2:
					$result[count($result) - 1]['expr'] = $filtersParams[$i];
					break;
				case 3:
					$result[count($result) - 1]['union'] = $filtersParams[$i];
					break;
			}
		}
		return $result;
	}

	public function getSfgAliases($elements, $mapping) {
		$aliases = array();
		foreach ($elements as $element) {
			$sfgAlias = $element['sfgalias'];
			foreach ($element['attributes'] as $attribute) {
				if ($attribute['name'] == 'name') {
					$dbField = $this->getDbFieldName($mapping, $attribute['value']);
					if (empty($sfgAlias)) {
						$sfgAlias = $attribute['value'];
						if (empty($sfgAlias)) {
							$sfgAlias = $dbField;
						}
					}
					$aliases[$dbField] = $sfgAlias;
					break;
				}
			}
		}
		return $aliases;
	}

	public function getDbFieldName($mapping, $sfgField) {
		foreach ($mapping as $field) {
			if ($field['sfgfield'] == $sfgField) {
				return $field['dbfield'];
			}
		}
	}
}
?>