<?php

namespace Ws\ConvertSQLBundle\Caller;

use Ws\ConvertSQLBundle\Caller\AbstractCaller;

class LocationCaller extends AbstractCaller
{
	
	public function findLocationFromData()
	{

		$states = $this->em->getRepository('MyWorldBundle:Location')->findAllDataFromCode($this->entry);

		if(empty($states)) return null;

		$location = $this->em->getRepository('MyWorldBundle:Location')->findLocationFromStates($states);
		
		if(empty($location)) return null;
		
		return $location;
	}
}