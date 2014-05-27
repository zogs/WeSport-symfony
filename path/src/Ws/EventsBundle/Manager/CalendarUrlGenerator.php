<?php

namespace Ws\EventsBundle\Manager;

use Symfony\Component\Routing\RouterInterface;

class CalendarUrlGenerator {

	private $router;
	private $params;
	private $url = '';
	private $fragments = array();
	private $defaults = array(
		'country' => 'allcountry',
		'city' => 'allcity',
		'sports' => 'allsports',
		'type' => 'alltype',
		'nbdays' => 7,
		'date' => 'allweek',
		'time' => 'allday',
		'price' => 'allprice',
		'organizer' => 'allorganizer'
		);

	public function __construct()
	{
		$this->defaults['date'] = \date('Y-m-d');
	}

	public function setRouter(RouterInterface $router)
	{
		$this->router = $router;
	}

	public function setParams($params)
	{
		$this->params = $params;
	}

	public function getSearchUrl()
	{
		//get the default route url
		$defaultUrl = $this->getRouteUrl();
		//get the params ( in the right order)
		preg_match_all('/\{([a-z]+)\}/',$defaultUrl,$defaultParams);		
		//get each fragment param
		foreach ($defaultParams[1] as $k => $param) {			
			$this->fragments[$param] = call_user_func(array($this,'get'.ucfirst($param).'Param'));
		}
		//replace null fragment with default value
		foreach ($this->fragments as $k => $value) {
			if($value === null) $this->fragments[$k] = $this->defaults[$k];
		}
		//remove last fragments if = defaults
		$this->fragments = array_reverse($this->fragments);
		foreach ($this->fragments as $k => $value) {
			if($value == $this->defaults[$k]) unset($this->fragments[$k]);
			else break;			
		}
		$this->fragments = array_reverse($this->fragments);

		//clear lasts url fragments if they are useless
		$this->url = implode('/',$this->fragments);

		//exit($this->url);
		//return url
		return $this->url;
	}

	private function getRouteUrl()
	{
		return $this->router->getRouteCollection()->get('en__RG__ws_events_calendar')->getPath();
	}

	private function getCountryParam()
	{
		return $this->params['country']->getName();		
	}

	private function getCityParam()
	{
		//if a city is enquire
		if(isset($this->params['location']) && method_exists($this->params['location'], 'getCity') && $this->params['location']->getCity()->getId() != NULL){
			$str = $this->params['location']->getCity()->getName();					
			if(!empty($this->params['area'])) {
				$str .= '+'.str_replace('km','',$this->params['area']);
			}
			return $str;
		}
		else
			return null;	
	}

	private function getSportsParam()
	{
		if(!empty($this->params['sports'])){			
			$str = '';
			foreach ($this->params['sports'] as $k => $sport) {
				$str .= $sport['slug'].'-';
			}	
			$str = trim($str,'-');
			return $str;				
		}
		else
			return null;
	}

	private function getTypeParam()
	{
		if(!empty($this->params['type'])){

			if(count($this->params['type'])>=3) return null;
			$str = '';
			foreach ($this->params['type'] as $k => $type) {
				$str .= $type.'-';
			}
			$str = trim($str,'-');
			return $str;
		}
		else 
			return null;
	}

	private function getNbdaysParam()
	{
		if(!empty($this->params['nbdays']))
			return $this->params['nbdays'];
		else
			return null;
	}

	private function getDateParam()
	{
		if(!empty($this->params['date']))
			return $this->params['date'];
		else
			return null;
	}

	private function getTimeParam()
	{		
		if(!empty($this->params['time']) && $this->params['time']['start'] !== '00:00:00' && $this->params['time']['end'] !== '24:00:00'){
			return $this->params['time']['start'].'-'.$this->params['time']['end'];
		}
		else
			return null;
	}

	public function getPriceParam()
	{
		if(!empty($this->params['price'])){
			return $this->params['price'];
		}
		else
			return null;
	}

	public function getOrganizerParam()
	{
		if(!empty($this->params['organizer'])){
			return $this->params['organizer']->getUsername();
		}
		else
			return null;
	}

}