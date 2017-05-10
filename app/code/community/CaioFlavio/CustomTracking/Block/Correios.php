Su<?php
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
			return $trackingData;
		}

		public function getTrackingType($trackingInfo){
			return $trackingInfo{0}->type;
		}

		public function getNotFoundMessage(){
			return Mage::getStoreConfig('customtracing/correios/message_notfound', Mage::app()->getStore()->getId());
		}
	}