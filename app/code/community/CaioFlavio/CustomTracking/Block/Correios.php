<?php
	class CaioFlavio_CustomTracking_Block_Correios extends Mage_Core_Block_Template{
		public function getObjectArray($orderid){
			$objectData 	= Mage::getModel('custom_tracking/correios')->getObjectData($orderid);
			$trakingCodes 	= $objectData->codes;
			foreach($trakingCodes as $key => $code){
				$trackingData[$code] = $objectData->items[$key];
			}
			return $trackingData;
		}

		public function getTrackingInfo($trackingCode){
			$trackingInfo  = Mage::getModel('custom_tracking/correios')->requestObject($trackingCode);
			if(!isset($trackingInfo->error)){
				foreach($trackingInfo->evento as $eventData){
					$trackingData[] = (object) array(
						'description' => $eventData->descricao,
						'type'		  => $trackingInfo->categoria,
						'from' 		  => (object) array(
							'agency'  => $eventData->local,
							'city'    => $eventData->cidade,
							'state'   => $eventData->uf,
							'local'	  => $eventData->cidade . ' / ' . $eventData->uf,
							'date'	  => $eventData->data,
							'hour'	  => $eventData->hora,
						),
						'to' 		  => (object) array(
							'agency'  => $eventData->destino->local,
							'city'	  => $eventData->destino->cidade,
							'uf'	  => $eventData->destino->uf,
							'local'	  => $eventData->destino->local . ', ' . $eventData->destino->cidade . ' / ' . $eventData->destino->uf,
						),
					);
				}
			}else{
				return (object) array('error' => true, 'errorType' => $trackingInfo->errorType);
			}
			return $trackingData;
		}

		public function getTrackingType($trackingInfo){
			return $trackingInfo{0}->type;
		}

		public function getNotFoundMessage($errorType = NULL){
			switch ($errorType) {
				case NULL:
					return Mage::getStoreConfig('customtracing/correios/notfound_error', Mage::app()->getStore()->getId());
				break;
				
				case 'connection':
					return Mage::getStoreConfig('customtracking/correios/ws_error', Mage::app()->getStore()->getId()); // 'O serviço dos correios apresenta instabilidade no momento, por favor tente mais tarde.';
				break;

				case 'execution':
					return Mage::getStoreConfig('customtracking/correios/internal_error', Mage::app()->getStore()->getId()); //'Erro interno ao processar o rastreio, por favor entre em contate conosco ou tente mais tarde.';
				break;

				default:
					return Mage::getStoreConfig('customtracking/correios/message_notfound', Mage::app()->getStore()->getId());
				break;
			}
		}
	}