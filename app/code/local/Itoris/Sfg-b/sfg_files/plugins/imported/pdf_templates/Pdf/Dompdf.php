<?php
/**
 * User: alexander
 * Date: 1/12/12
 */
require_once dirname(__FILE__) . DS . 'dompdf' . DS . 'dompdf_config.inc.php';

require_once DOMPDF_INC_DIR . DS . 'frame.cls.php';
require_once DOMPDF_INC_DIR . DS . 'frame_decorator.cls.php';
require_once DOMPDF_INC_DIR . DS .'frame_reflower.cls.php';
require_once DOMPDF_INC_DIR . DS . 'positioner.cls.php';
require_once DOMPDF_INC_DIR . DS . 'canvas.cls.php';
require_once DOMPDF_INC_DIR . DS . 'cpdf_adapter.cls.php';

$files = scandir(DOMPDF_INC_DIR);
foreach($files as $fileName){
	if( !($fileName == 'cached_pdf_decorator.cls.php' ||
		$fileName == 'page_cache.cls.php' ||
			$fileName == 'tcpdf_adapter.cls.php'
		)
	) {
		$filePath = DOMPDF_INC_DIR . DS . $fileName;
		if(is_file($filePath)){
			require_once $filePath;
		}
	}
}

class PdfTemplates_Pdf_Dompdf extends DOMPDF{

	private $headerSeparatorCss = 'border-bottom:  1px solid #000000;';
	private $footerSeparatorCss = 'border-top:  1px solid #000000;';
	private $config = null;

	/**
	 * @param $config Varien_Object
	 */
	public function load($config) {
		$this->config = $config;
		$this->set_paper($config->getPageSize(), $config->getPaperOrientation());
		$html = '<head><meta http-equiv="Content-Type" content="text/html;charset=utf-8">'
			. $this->css($config->getCanShowHeaderSeparator(), $config->getCanShowFooterSeparator()) . '</head>';
		$html .= '<body><div class="header">'. $config->getHeader() .'</div>'
				.'<div class="footer">'. $config->getFooter() . $this->pager() .'</div>';
		foreach ($config->getTemplateParts() as $part) {
			$html .= '<div class="page_content">' . $part . '</div><div class="flyleaf"></div>';
		}
		$html .= '</body>';
		$this->load_html($html);
	}

	public function css($headerSeparatorShow = true, $footerSeparatorShow = true) {
		$footerHeight = $this->config->getFooterHeight();
		if ($this->config->getCanShowPageNumbers()) {
			$footerHeight += 40;
		}
		$html = '<style>
				*{margin:0;padding:0;font-family: dejavu serif;}
			  .flyleaf {
				page-break-after: always;
			  }

			  .pager {
				text-align: "'. $this->config->getPageNumbersAlign() .'";
			  }

			  .header {
			  	position: fixed;
			  	margin: 15px;
			  	top: 0px;
			  	height: '. $this->config->getHeaderHeight() .'px;
				'. ($headerSeparatorShow ? $this->headerSeparatorCss : '') .'
				overflow: hidden;
			  }
			  .footer{
			  	position: fixed;
			  	bottom: 0;
			  	margin: 15px;
			  	height: '. $footerHeight .'px;
			  	'. ($footerSeparatorShow ? $this->footerSeparatorCss : '') .'
			  }
			  .page_content{
			    padding: 15px;
			    padding-top: '. ($this->config->getHeaderHeight() + 15) .'px;
			    padding-bottom: '. ($footerHeight + 30) .'px;
			  }
			  '. $this->config->getCssStyles() .'
			</style>';

		return $html;
	}

	public function pager() {
		if($this->config->getCanShowPageNumbers()){
			switch($this->config->getPageSize()){
				case 'A4': $height = 842;
							$width = 596;
							break;
				case 'LETTER': $height = 792;
								$width = 612;
								break;
				case 'LEGAL': $height = 1008;
							  $width = 612;
							  break;
			}
			$top = $height - 35;
			if($this->config->getPageNumbersAlign() == 'right'){
				$left = $width - 70;
			}else if($this->config->getPageNumbersAlign() == 'center'){
				$left = (int)($width/2) - 25;
			}else{
				$left = 15;
			}
			$html = '<script type="text/php">
        		if ( isset($pdf) ) {
        		  $font = Font_Metrics::get_font("helvetica", "normal");
		          $pdf->page_text('. $left .', '. $top .', "page {PAGE_NUM} of {PAGE_COUNT}", $font, 10, array(0,0,0));
        		}
        	</script>';
			return $html;
		}
	}

}

?>