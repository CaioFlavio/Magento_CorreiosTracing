<?php
	class CaioFlavio_CustomTracking_Helper_Data extends Mage_Core_Helper_Abstract{
		protected function getModuleFrontName(){
		    $modules =  Mage::getConfig()
			   ->loadModulesConfiguration('config.xml')
			   ->getNode('frontend/routers');
			$module = $modules->customtracking;	
		    return $module->args->frontName;
		}

		public function getTrackingUrl($carrier, $orderid = ''){
			return Mage::getBaseUrl() . $this->getModuleFrontName() . '/index/' . $carrier . '/order/' . $orderid;
		}	

		public function validateCorreiosCode($trackNumber){
			/**
				Código do objeto formado por 2 letras, 9 números e 2 letras. Este campo diferencia letras maiúsculas e minúsculas (casesensitive). Exemplo: AA458226057BR (sempre informar tudo em letras maiúsculas).
				Fonte: Documentação webservice Correios.
			**/
			return (strlen($trackNumber) == 13 && strripos($trackNumber, 'BR') == 11) ? true : false;
		}	
	}