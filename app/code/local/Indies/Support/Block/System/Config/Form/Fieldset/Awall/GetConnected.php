<?php
/**
 * @category   Indies Services
 * @package    Indies_Support
 * @version    1.0.0
 * @copyright  Copyright (c) 2012-2013 Indies Services (http://www.indieswebs.com)
 */

class Indies_Support_Block_System_Config_Form_Fieldset_Awall_GetConnected extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{
	public function render(Varien_Data_Form_Element_Abstract $element)
    {
		$html = $this->_getHeaderHtml($element);
		$html .='<div><p>Connect with us for new extensions, themes, free upgrades, etc.</p></div>';
		$html .='<table>
					<tr>
				    	<td>Blog:</td>
				        <td><a target="_blank" href="http://indieswebs.com/blog">http://indieswebs.com/blog</a></td>
				    </tr>
    
   					<tr>
				    	<td>Facebook:</td>
				        <td><a target="_blank" href="http://www.facebook.com/IndiesWebs">http://www.facebook.com/IndiesWebs</a></td>
				    </tr>
	
					<tr>
				    	<td>Twitter:</td>
				        <td><a target="_blank" href="http://www.twitter.com/IndiesWebs">http://www.twitter.com/IndiesWebs</a></td>
				    </tr>
	
					<tr>
				    	<td>Google+:</td>
				        <td><a target="_blank" href="http://gplus.to/IndiesWebs">http://gplus.to/IndiesWebs</a></td>
				    </tr>
				</table>';

		$html .='
		<div style="margin-left:24px; margin-top:28px; width:300px;">
        	<div class="facebook" style="float:left;">
            	<iframe src="//www.facebook.com/plugins/like.php?href=http%3A%2F%2Fwww.facebook.com%2FIndiesWebs&amp;send=false&amp;layout=button_count&amp;width=450&amp;show_faces=false&amp;font=arial&amp;colorscheme=light&amp;action=like&amp;height=21&amp;appId=138434699644956" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:82px; height:21px;" allowTransparency="true"></iframe>
          	</div>
          	<div class="twitter" style="float:left;">
             	<a href="https://twitter.com/IndiesWebs" class="twitter-follow-button" data-show-count="false" data-show-screen-name="false">Follow @IndiesWebs</a>
             	<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>             
          	</div> 
          	<div class="google" style="float:left; margin-left:20px;">
            	<div class="g-plusone" data-size="medium" data-href="https://plus.google.com/100234253868759009400"></div>
                	<script type="text/javascript">
						(function() 
						{
							var po = document.createElement("script"); po.type = "text/javascript"; po.async = true;
							po.src = "https://apis.google.com/js/plusone.js";
							var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(po, s);
						})
						();
	               </script>
    	        </div>
        	</div>';

		$html .= $this->_getFooterHtml($element);
		return $html;
	}

}