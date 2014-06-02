<?php

namespace Ws\EventsBundle\Manager;

use Symfony\Component\Routing\RouterInterface;

class CalendarUrlGenerator {

	private $router;
	private $search;
	private $url = '';
	private $fragments = array();
	public $defaults = array(
		'country' => 'allcountry',
		'city' => 'allcity',
		'area' => null, 
		'sports' => 'allsports',
		'type' => 'alltype',
		'nbdays' => 7,
		'date' => 'allweek',
		'time' => 'allday',
		'price' => 'allprice',
		'organizer' => 'allorganizer'
		);

	public function __construct(RouterInterface $router)
	{
		$this->router = $router;

		$this->defaults['date'] = \date('Y-m-d');
	}

	public function setRouter(RouterInterface $router)
	{
		$this->router = $router;
	}

	public function setSearch($search)
	{
		$this->search = $search;

		return $this;
	}

	public function getUrl()
	{
		//compute parameters
		$fragments = $this->computeFragments();
		//replace default value
		$fragments = $this->fragmentsReplaceDefaults($fragments);
		//remove last fragments if defaults
		$fragments = array_reverse($fragments);
		foreach ($fragments as $k => $value) {
			if($value == $this->defaults[$k]) unset($fragments[$k]);
			else break;			
		}
		$fragments = array_reverse($fragments);

		//clear lasts url fragments if they are useless
		$this->url = implode('/',$fragments);

		//return url
		return $this->url;
	}

	public function getUrlParams($withDefault = false)
	{
		$params = $this->computeFragments();
		if($withDefault == true) $params = $this->fragmentsReplaceDefaults($params);

		return $params;
	}

	public function getShortUrlParams()
	{
		$params = $this->getUrlParams(false);
		$params = array_reverse($params);
		foreach ($params as $param => $value) {			
			if(empty($value) || $value == $this->defaults[$param]){
				unset($params[$param]);
			}
			else break;
		}
		return array_reverse($params);
	}

	private function computeFragments()
	{		
		$routeParams = $this->getRouteParams();	
		//get each route params value
		foreach ($routeParams as $k => $param) {			
			$this->fragments[$param] = call_user_func(array($this,'get'.ucfirst($param).'Param'));
		}

		return $this->fragments;		
	}

	private function fragmentsReplaceDefaults($fragments)
	{
		//replace with default value if needed
		foreach ($fragments as $k => $value) {
			if($value === null) $fragments[$k] = $this->defaults[$k];
		}
		return $fragments;
	}

	public function getRouteParams()
	{
		$route = $this->getRouteUrl();
		//get the params ( in the right order)
		preg_match_all('/\{([a-z]+)\}/',$route,$routeParams);
		return $routeParams[1];
	}

	private function getRouteUrl()
	{
		return $this->router->getRouteCollection()->get('en__RG__ws_events_calendar')->getPath();
	}

	private function getCountryParam()
	{
		return $this->search->getCountry()->getName();		
	}

	private function getCityParam()
	{
		//if a city is enquire
		if($this->search->hasLocation()){
			$str = $this->search->getLocation()->getCity()->getName();					
			if($this->search->hasArea()) {
				$str .= '+'.str_replace('km','',$this->search->getArea());
			}
			return $str;
		}
		else
			return null;	
	}

	private function getAreaParam()
	{
		return null; //is contained in the city param string
	}

	private function getSportsParam()
	{
		if($this->search->hasSports()){			
			$str = '';
			foreach ($this->search->getSports() as $k => $sport) {
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
		if($this->search->hasType()){
			$str = '';
			foreach ($this->search->getType() as $k => $type) {
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
		if($this->search->hasNbDays())
			return $this->search->getNbDays();
		else
			return null;
	}

	private function getDateParam()
	{
		if($this->search->hasDate()){
			$d = \DateTime::createFromFormat('Y-m-d',$this->search->getDate());
			if($d !== false && !array_sum($d->getLastErrors())) return $d->format('dMy');
			return $this->search->getDate();
		}
		else
			return null;
	}

	private function getTimeParam()
	{		
		if($this->search->hasTime()){
			$time = $this->search->getTime();
			return substr($time['start'],0,5).'-'.substr($time['end'],0,5);
		}
		else
			return null;
	}

	public function getPriceParam()
	{
		if($this->search->hasPrice()){
			return $this->search->getPrice();
		}
		else
			return null;
	}

	public function getOrganizerParam()
	{
		if($this->search->hasOrganizer()){	
			return $this->search->getOrganizer()->getUsername();
		}
		else
			return null;
	}

}