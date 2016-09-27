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

require_once dirname(__FILE__) . DS . '..' . DS . 'Pdf' . DS . 'Dompdf.php';

class PdfTemplates_Helper_Pdf {

	/**
	 * @param $form SFG_Engine
	 */
	public function prepareFormPDF(&$form) {
		$formId = $form->form_id;
		$form->loadForm();
		$form->parseXML();

		$data_arr = $_SESSION['sfg'][$formId];
		$templateModel = new PdfTemplate_Model_Template();
		$templateConfig = $templateModel->load($formId);
		if (isset($templateConfig['template'])) {
			$templateConfig = unserialize($templateConfig['template']);
		}
		$template = new Varien_Object($templateConfig);

		$config = $this->getDefaultConfig();
		$config->setHeader($this->parseTemplate($template->getHeaderContent(), $data_arr));
		$config->setCssStyles($template->getBodyStyles());
		$config->setFooter($this->parseTemplate($template->getFooterContent(), $data_arr));
		$config->setHeaderHeight($template->getHeaderHeight());
		$config->setFooterHeight($template->getFooterHeight());
		$config->setCanShowPageNumbers($template->getShowPageNumbers());
		$config->setPageNumbersAlign($template->getPageNumbersAlign());
		$config->setCanShowHeaderSeparator($template->getShowLineSeparatorHeaderBody());
		$config->setCanShowFooterSeparator($template->getShowLineSeparatorBodyFooter());
		$config->setPageSize($template->getPageSize());

		$_SESSION['sfg'][$formId]['attach_admin_email'] = (int)$template->getAttachAdminEmail();
		$_SESSION['sfg'][$formId]['attach_user_email'] = (int)$template->getAttachUserEmail();

		$bodyContent = $this->parseTemplate($template->getBodyContent(), $data_arr);
		$config->setTemplateParts(explode("{pdf_page_break}", $bodyContent));

		$pdfFile = $this->generatePdf($config);
		$filename = md5(date("r"));
		$name = $filename.".pdf";
		$filename = "_".$name;
		$path = SFG_ENGINE . 'files' . DS;
		file_put_contents($path . $filename, $pdfFile);

		$pdfNames = array(
			'name'     => ($template->getAttachFileName() ? $template->getAttachFileName() : 'pdf_template') . '.pdf',
			'tmp_name' => $path.$filename
		);

		$_SESSION['sfg'][$formId]['files']['pdf_document'] = $pdfNames;
		$form->files['pdf_document'] = $pdfNames;
	}

	private function parseTemplate($template, $data) {
		foreach ($data['data'] as $key => $value) {
			if(is_array($value)){
				$value = implode('|', $value);
			}
			$template = preg_replace('/\{'.$key.'\}/i', $value, $template);
		}

		$template = preg_replace("/\{.+}/", "", $template);
		$template = str_replace("<!-- pagebreak -->", "{pdf_page_break}", $template);

		//$template = str_replace('src="../', 'src="'.$GLOBALS['mosConfig_absolute_path'].'/', $template);
		//if (isset($_SERVER['DOCUMENT_ROOT'])) $template = str_replace($_SERVER['DOCUMENT_ROOT'], '/', $template);

		return $template;
	}

	/**
	 * @param $data array
	 * @return string
	 */
	public function prepareRecordPdf($data, $formName, $sfgAliases) {
		$config = $this->getDefaultConfig();
		$headerHtml = '<h4>Smart Former Gold - ' . $this->getSfgHelper()->__('Record Details') . '</h4>';
		$headerHtml .= '<h4>' . $this->getSfgHelper()->__('Form') . ': ' . $formName . '</h4>';
		$config->setHeader($headerHtml);
		$bodyHtml = '<table style="border: 1px solid #666666;border-collapse: collapse;width:100%;">';
		foreach ($data as $key => $value) {
			$bodyHtml .= '<tr><td style="border: 1px solid #666666;padding:3px;padding-right:20px;">'. (isset($sfgAliases[$key]) ? $sfgAliases[$key] : $key) .'</td><td style="border: 1px solid #666666;padding:3px;">'. $value .'</td></tr>';
		}
		$bodyHtml .= '</table>';
		$templateParts = array($bodyHtml);
		$config->setTemplateParts($templateParts);
		return $this->generatePdf($config);
	}
	
	public function prepareAllRecordsPdf($records, $formName, $sfgAliases) {
		if (count($records) == 1) {
			return $this->prepareRecordPdf($records[0], $formName, $sfgAliases);
		}
		$config = $this->getDefaultConfig();
		$config->setPaperOrientation('landscape');
		$headerHtml = '<h4>Smart Former Gold - ' . $this->getSfgHelper()->__('List of Records') . '</h4>';
		$headerHtml .= '<h4>' . $this->getSfgHelper()->__('Form') . ': ' . $formName . '</h4>';
		$config->setHeader($headerHtml);
		$bodyHtml = '<table style="border: 1px solid #666666;border-collapse: collapse;width:100%;">';
		$bodyHtml .= '<tr>';
		foreach ($records[0] as $key => $value) {
			$bodyHtml .= '<td style="border: 1px solid #666666; padding: 3px;">'. (isset($sfgAliases[$key]) ? $sfgAliases[$key] : $key) .'</td>';
		}
		$bodyHtml .= '</tr>';
		for ($i = 0; $i < count($records); $i++) {
			$bodyHtml .= '<tr>';
			foreach ($records[$i] as $key => $value) {
				$bodyHtml .= '<td style="border: 1px solid #666666;padding:3px;padding-right:20px;">'. $value .'</td>';
			}
			$bodyHtml .= '</tr>';
		}
		$bodyHtml .= '</table>';
		$templateParts = array($bodyHtml);
		$config->setTemplateParts($templateParts);
		return $this->generatePdf($config);
	}

	private function getDefaultConfig() {
		$data = array(
			'can_show_header_separator' => true,
			'can_show_footer_separator' => true,
			'can_show_page_number'      => true,
			'page_numbers_align'        => 'center',
			'page_size'                 => 'A4',
			'header_height'             => 50,
			'footer_height'             => 50,
			'template_parts'            => array(),
			'header'                    => '',
			'footer'                    => '',
			'styles'                    => '',
			'paper_orientation'         => 'portrait',
		);
		$config = new Varien_Object($data);
		return $config;
	}

	/**
	 * @param $config Varien_Object
	 * @return string = content of the pdf file
	 */
	public function generatePdf($config) {
		@ini_set('max_execution_time', 180);

		$pdf = new PdfTemplates_Pdf_Dompdf();
		$pdf->load($config);
		$pdf->render();
		return $pdf->output();
	}

	/**
	 * @return Itoris_Sfg_Helper_Data
	 */
	public function getSfgHelper() {
		return Mage::helper('sfg');
	}
}

?>