<?php 
Class CaioFlavio_CustomTracking_Model_Correios extends Mage_Core_Model_Abstract{

	private $webserviceUrl = 'http://webservice.correios.com.br/service/rastro/Rastro.wsdl';
	/*customtracking/correios/
		webserviceUrl: Método a ser Consumido pela requisição com certificado de autenticação: http://webservice.correios.com.br/service/rastro/Rastro.wsdl
			Fonte: Documentação Webservice Correios
	*/	

	private $webserviceUser = '';
	/*
		webserviceUser: Informado pela área comercial dos Correios na ativação do serviço. Este campo diferencia letras maiúsculas e minúsculas.
		Fonte: Documentação Webservice Correios
		Configuravel em: Painel Administrativo > Sistema > Configuração > Aba lateral Rastreio de Pedidos > Geral > Nome de Usuário
	*/	

	private $webservicePass = '';
	/*
		webservicePass: Informado pela área comercial dos Correios na ativação do serviço. Este campo diferencia letras maiúsculas e minúsculas
		Fonte: Documentação Webservice Correios
		Configuravel em: Painel Administrativo > Sistema > Configuração > Aba lateral Rastreio de Pedidos > Geral > Senha de acesso
	*/

	private $webserviceType = 'L';
	/*
		webserviceType: Definição de como a lista de identificadores de objetos deverá ser interpretada pelo servidor SRO. 
		Pode ser de dois tipos:
		L: lista de objetos. O servidor fará a consulta individual de cada identificador informado. 
		F: intervalo de objetos. O servidor fará a consulta sequencial do primeiro ao último objeto informado.
		Fonte: Documentação Webservice Correios
		Configuravel em: Painel Administrativo > Sistema > Configuração > Aba lateral Rastreio de Pedidos > Geral > Tipo de Consulta
	*/

	private $webserviceResult = 'T';
	/*
		webserviceResult: Delimitação do escopo da resposta a ser dada à consulta do rastreamento de cada objeto.
		Pode ser de dois tipos:
		T: serão retornados todos os eventos do objeto.
		U: será retornado apenas oúltimo evento do objeto.
		Fonte: Documentação Webservice Correios
		Configuravel em: Painel Administrativo > Sistema > Configuração > Aba lateral Rastreio de Pedidos > Geral > Tipo de retorno
	*/

	private $webserviceLanguage = '101';
	/*
		webserviceLanguage: Definição do idioma que será apresentado pelo servidor SRO.
		Pode ser de dois tipos:
		101: Serão retornados todos os eventos no idioma Português.
		102: Serão retornados todos os eventos no idioma Inglês.
	*/

	private $connectionTimeout = 3;
	/*
		Tempo máximo para aguardar reposta da webservice dos correios.
	*/


	public function __construct(){
		$this->storeid 				= Mage::app()->getStore()->getId();
		$this->webserviceUrl  		= (is_null(Mage::getStoreConfig('customtracking/correios/url', $this->storeid))) 		? $this->webserviceUrl  	: Mage::getStoreConfig('customtracking/correios/url', $this->storeid);
		$this->webserviceUser 		= (is_null(Mage::getStoreConfig('customtracking/correios/user',$this->storeid))) 		? $this->webserviceUser 	: Mage::getStoreConfig('customtracking/correios/user', $this->storeid);
		$this->webservicePass 		= (is_null(Mage::getStoreConfig('customtracking/correios/password', $this->storeid))) 	? $this->webservicePass 	: Mage::getStoreConfig('customtracking/correios/password', $this->storeid);
		$this->webserviceType 		= (is_null(Mage::getStoreConfig('customtracking/correios/type'))) 						? $this->webserviceType		: Mage::getStoreConfig('customtracking/correios/type', $this->storeid);
		$this->webserviceResult 	= (is_null(Mage::getStoreConfig('customtracking/correios/result'))) 					? $this->webserviceResult 	: Mage::getStoreConfig('customtracking/correios/result', $this->storeid);
		$this->webserviceLanguage 	= (is_null(Mage::getStoreConfig('customtracking/correios/language', $this->storeid))) 	? $this->webserviceLanguage : Mage::getStoreConfig('customtracking/correios/language', $this->storeid);
		$this->connectionTimeout 	= (is_null(Mage::getStoreConfig('customtracking/correios/timeout', $this->storeid))) 	? $this->connectionTimeout 	: Mage::getStoreConfig('customtracking/correios/timeout', $this->storeid);


		/*
			Os parametros abaixo são necessários para aumentar a velocidade de resposta da webservice.
		*/
		try {
			$this->soapClient =  new SoapClient($this->webserviceUrl, 
				array(
					'connection_timeout' => $this->connectionTimeout,					 
					'cache_wsdl' 		 => WSDL_CACHE_BOTH,	
				)
			); 			
		} catch (Exception $e) {
			return $e->getMessage();
		}

	}

	public function requestObject($objectCode){
		$params = array(
		        'usuario'   => $this->webserviceUser,
		        'senha'     => $this->webservicePass,
		        'tipo'      => $this->webserviceType,
		        'resultado' => $this->webserviceResult,
		        'lingua'    => $this->webserviceLanguage,
		        'objetos'   => trim($objectCode)
			);
		$this->request = $this->soapClient->buscaEventos($params);

		return $this->request->return->objeto;
	}

	protected function getShipmentItems($itemsCollection){
		foreach($itemsCollection as $item){
			$itemsData[] = (object) array(
				'producName' => $item->getName(),
				'productSku' => $item->getSku(),
				'quantity'	 => $item->getQty(),
			);
		}
		return $itemsData;
	}

	protected function getTrackInfo($tracks){
		foreach($tracks as $trackInfo){
			if(Mage::helper('custom_tracking')->validateCorreiosCode($trackInfo->getNumber()))
				$trackingCodes[] = $trackInfo->getNumber();
		}
		return $trackingCodes;
	}

	public function getObjectData($orderid){
		$order = Mage::getModel('sales/order')->load($orderid);
		if ($order->getShippingDescription()){
			$shippingMethod 	= $order->getShippingMethod();
			$shipmentCollection = Mage::getResourceModel('sales/order_shipment_collection')->setOrderFilter($order)->load();
			$i = 0;
			foreach ($shipmentCollection as $shipment){
				$tracks = $shipment->getAllTracks();
				if($tracks){
					$shipmentsItems['items'][$i] = $this->getShipmentItems($shipment->getItemsCollection());
					$shippmenstInfo['codes'] = $this->getTrackInfo($tracks);
				}
				$i++;
			}
		}
		return (object) array_merge($shipmentsItems, $shippmenstInfo);
	}
}