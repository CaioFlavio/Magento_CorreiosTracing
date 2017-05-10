<?php
Class CaioFlavio_CustomTracking_IndexController extends Mage_Core_Controller_Front_Action{
    public function preDispatch(){
        parent::preDispatch();
        $action = $this->getRequest()->getActionName();
        $loginUrl = Mage::helper('customer')->getLoginUrl();
        $customerOrder 	 = Mage::getModel('sales/order')->load($this->getRequest()->getParam('order'))->getCustomerId();
        $customerSession = Mage::getModel('customer/session')->getCustomerId();
        if (!Mage::getSingleton('customer/session')->authenticate($this, $loginUrl) || $customerSession != $customerOrder) {
        	Mage::getSingleton('core/session')->addError('Não foi possível abrir o rastreio desse pedido.'); 
            $this->_redirectReferer();
            // $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        }  			      	
    }

	public function indexAction(){
		$this->loadLayout();
		$this->getLayout()->getBlock('content')->append($this->getLayout()->createBlock('custom_tracking/correios')->setTemplate('custom_tracking/correios.phtml'));
		$this->renderLayout();
	}

	public function correiosAction(){
		$orderid = $this->getRequest()->getParam('order');
		$this->loadLayout();
		$this->getLayout()->getBlock('content')->append($this->getLayout()->createBlock('custom_tracking/correios')->setOrderId($orderid)->setTemplate('custom_tracking/correios.phtml'));
		$this->renderLayout();		
	}
}