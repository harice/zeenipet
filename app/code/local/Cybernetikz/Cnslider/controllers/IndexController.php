 <?php
/**
*	Author		: 	Cybernetikz
*	Author Email:   info@cybernetikz.com
*	Blog		: 	http://blog.cybernetikz.com
*	Website		: 	http://www.cybernetikz.com
*/

class Cybernetikz_Cnslider_IndexController extends Mage_Core_Controller_Front_Action
{
    /**
     * Pre dispatch action that allows to redirect to no route page in case of disabled extension through admin panel
     */
    public function preDispatch()
    {
        parent::preDispatch();
		
		Mage::helper('cybernetikz_cnslider')->getCnlv();
			
        if (!Mage::helper('cybernetikz_cnslider')->isEnabled()) {
            $this->setFlag('', 'no-dispatch', true);
            $this->_redirect('noRoute');
        }        
    }
    
    /**
     * Index action
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
	
	public function previewAction()
    {
		$slidersId = $this->getRequest()->getParam('id');
        if (!$slidersId) {
            return $this->_forward('noRoute');
        }
		
		$this->loadLayout();
        $this->renderLayout();
    }
	
	public function bannerpreviewAction()
    {
		$slidersId = $this->getRequest()->getParam('id');
        if (!$slidersId) {
            return $this->_forward('noRoute');
        }
		
		$this->loadLayout();
        $this->renderLayout();
    }

}