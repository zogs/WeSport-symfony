<?php

namespace Ws\EventsBundle\Manager;

use Symfony\Component\Routing\RouterInterface;

class CalendarUrlGenerator {

	private $router;
	private $params;
	private $url = '';
	public static $defaults = array(
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
		self::$defaults['date'] = \date('Y-m-d');
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
		//call function for each param
		foreach ($defaultParams[1] as $k => $param) {			
			call_user_func(array($this,'add'.ucfirst($param).'Param'));
		}

		//clear lasts url fragments if they are useless
		$this->url = trim($this->url,'/');
		$fragments = explode('/',$this->url);
		foreach ($fragments as $key => $value) {
			if(end($fragments) == end(self::$defaults)){
				echo $value.'<br>';
				array_pop($fragments);
				array_pop(self::$defaults);
			} 
		}		
		$this->url = implode('/',$fragments);

		exit($this->url);
		//return url
		return $this->url;
	}

	private function getRouteUrl()
	{
		return $this->router->getRouteCollection()->get('en__RG__ws_events_calendar')->getPath();
	}

	private function addCountryParam()
	{
		$this->url .= $this->params['country']->getName();
		$this->url .= '/';		
	}

	private function addCityParam()
	{
		//if a city is enquire
		if(isset($this->params['location']) && method_exists($this->params['location'], 'getCity') && $this->params['location']->getCity()->getId() != NULL){
			$this->url .= $this->params['location']->getCity()->getName();					
			if(!empty($this->params['area'])) {
				$this->url .= str_replace('km','',$this->params['area']);
			}
		}
		else
			$this->url .= self::$defaults['city'];	
			
		$this->url .= '/';	
	}

	private function addSportsParam()
	{
		if(!empty($this->params['sports'])){			
			foreach ($this->params['sports'] as $k => $sport) {
				$this->url .= $sport['slug'].'-';
			}	
			$this->url = trim($this->url,'-');				
		}
		else
			$this->url .= self::$defaults['sports'];

		$this->url .= '/';
	}

	private function addTypeParam()
	{
		if(!empty($this->params['type'])){
			foreach ($this->params['type'] as $k => $type) {
				$this->url .= $type.'-';
			}
			$this->url = trim($this->url,'-');
		}
		else 
			$this->url .= self::$defaults['type'];

		$this->url .= '/';
	}

	private function addNbdaysParam()
	{
		if(!empty($this->params['nbdays']))
			$this->url .= $this->params['nbdays'];
		else
			$this->url .= self::$defaults['nbdays'];

		$this->url .= '/';
	}

	private function addDateParam()
	{
		if(!empty($this->params['date']))
			$this->url .= $this->params['date'];
		else
			$this->url .= self::$defaults['date'];

		$this->url .= '/';
	}

	private function addTimeParam()
	{
		if($this->params['time']['timestart'] != '00:00:00' || $this->params['time']['timeend'] !== '24:00:00'){
			$this->url .= $this->params['time']['timestart'].'-'.$this->params['time']['timeend'];
		}
		else
			$this->url .= self::$defaults['time'];

		$this->url .= '/';
	}

	public function addPriceParam()
	{
		if(!empty($this->params['price'])){
			$this->url .= $this->params['price'];
		}
		else
			$this->url .= self::$defaults['price'];

		$this->url .= '/';
	}

	public function addOrganizerParam()
	{
		if(!empty($this->params['organizer'])){
			$this->url .= $this->params['organizer']->getUsername();
		}
		else
			$this->url .= self::$defaults['organizer'];

		$this->url .= '/';
	}

	public function getUrlStringParam()
	{
		return '';
		$s = '';
		$s .= $this->full['country']->getName();
		$s .= '/';
		$s .= $this->full['location']->getCity()->getName();
		if(!empty($this->params['area'])) {
			$s .= '+'.$this->params['area'];
		}
		$s .= '/';
		if(empty($this->params['sports'])){
			$s .= 'all';
		} else {
			foreach ($this->full['sports'] as $k => $sport) {
				$s .= $sport->getSlug().'+';
			}	
			$s = trim($s,'+');		
		}
		$s .= '/';
		if(empty($this->params['sports'])){
			$s .= 'all';
		} else {
			foreach ($this->params['type'] as $k => $type) {
				$s .= $type.'-';
			}
			$s = trim($s,'-');
		}
		$s .= '/';
		$s .= $this->params['nbdays'];		
		$s .= '/';
		$s .= $this->params['date'];
		$s .= '/';
		$s .= (isset($this->params['startime'])? $this->params['timestart'].'-'.$this->params['timeend'] : 'allday');

		return $s;
	}
}