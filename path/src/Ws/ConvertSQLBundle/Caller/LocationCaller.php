<?php

namespace Ws\ConvertSQLBundle\Caller;

use Ws\ConvertSQLBundle\Caller\AbstractCaller;

class LocationCaller extends AbstractCaller
{
	
	public function findLocationFromData($fields = null)
	{
		//get location fields or the defaults ones
		$fields = (isset($fields))? $fields : array('CC1'=>'CC1','ADM1'=>'ADM1','ADM2'=>'ADM2','ADM3'=>'ADM3','ADM4'=>'ADM3','city'=>'city');


		//get the codes from the location fields
		$codes = array();
		if(is_array($fields)){
			foreach ($fields as $key => $value) {
				$codes[$key] = $this->entry[$value];
			}
		}

		$states = $this->em->getRepository('MyWorldBundle:Location')->findStatesFromCodes($codes);
		if(empty($states)) return null;

		$location = $this->em->getRepository('MyWorldBundle:Location')->findLocationFromStates($states);		
		if(empty($location)) return null;
		
		return $location;
	}
}