<?php

namespace Ws\EventsBundle\Manager;

use My\ManagerBundle\Manager\AbstractManager;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

use Ws\EventsBundle\Manager\CalendarUrlGenerator;

class CalendarManager extends AbstractManager
{
	protected $em;
	private $params = array();
	private $computed = false;	
	private $default = array(
		'country' => 'FR',
		'date' => null,
		'city_id'=>null,
		'city_name'=>null,
		'area'=>null,
		'sports'=> array(), //array(67,68,72)
		'nbdays'=>7,
		'type' => array('pro','asso','person'),
		'timestart' => 0,
		'timeend' => 24,
		'price' => 0,
		'organizer' => null,

		);

	private $full = array(
		'sports' => array(),
		'type' => array()
		);

	private $params2IgnoreInCookie = array('PHPSESSID','hl','organizer');
	private $typeAllowed = array('person','asso','pro');
	private $cookies = array();
	private $query = array();
	private $uri = array();


	public function findCalendar()
	{
		$params = $this->computeParams();	
		return $this->em->getRepository('WsEventsBundle:Event')->findCalendarEvents($params);
	}

	public function getParams()
	{
		return $this->params;
	}

	public function setGetParams($params)
	{
		$this->query = $params;
	}

	public function setUriParams($params)
	{
		foreach ($params as $key => $value) {
			if( NULL === $params[$key]) unset($params[$key]);
		}
		$this->uri = $params;
	}

	public function setCookieParams($cookies)
	{
		foreach ($cookies as $k => $value) {
			//if array unserialize it
			if(strpos($value,'[array]') === 0) $value = unserialize(str_replace('[array]','',$value));
			$cookies[$k] = $value;
		}
		$this->cookies = $cookies;
	}

	public function setDateWeek($date)
	{
		$this->uri['date'] = $date; //override all date params
	}


	public function computeParams()
	{
		if($this->computed) return $this->params;
		$this->params = array_merge(
						$this->default,
						$this->cookies,
						$this->query,
						$this->uri
						);		

		$this->prepareParams();		
		$this->computed = true;
		return $this->params;
	}
	public function getSearchData()
	{
		$a = $this->getSearch();
		$a['raw'] = $this->getRawParams();
		$a['url'] = $this->getSearchUrl($a);
		return $a;
	}

	public function getRawParams()
	{		
		return $this->computeParams();
	}

	public function getSearch()
	{
		$this->full['country'] = $this->em->getRepository('MyWorldBundle:Country')->findCountryByCode($this->params['country']);
		if(isset($this->params['city_id']))
			$this->full['location'] = $this->em->getRepository('MyWorldBundle:Location')->findLocationByCityId($this->params['city_id']);
		else
			$this->full['location'] = $this->em->getRepository('MyWorldBundle:Location')->findLocationByCountryCode($this->params['country']);

		$this->full['area'] = (!empty($this->params['area']))? '+'.$this->params['area'].'km' : '';
		$this->full['nbdays'] = $this->params['nbdays'];
		$this->full['date'] = $this->params['date'];

		if(!empty($this->params['type']))
			$this->full['type'] = $this->params['type'];
		else
			$this->full['type'] = $this->default['type'];

		
		$this->full['sports'] = array();
		$repo = $this->em->getRepository('WsSportsBundle:Sport');
		if(!empty($this->params['sports']) && $this->params['sports'] != 'all'){			
			foreach ($this->params['sports'] as $k => $id) {
				$this->full['sports'][] = $repo->findRowsById($id);
			}			
		}

		$this->full['time'] = array('timestart'=>$this->params['timestart'],'timeend'=>$this->params['timeend']);
		$this->full['price'] = $this->params['price'];

		if(!empty($this->params['organizer']))
			$this->full['organizer'] = $this->em->getRepository('MyUserBundle:User')->findOneById($this->params['organizer']);

		//free memory
		$this->em->clear();

		return $this->full;
	}

	public function getSearchUrl($params)
	{
		$generator = new CalendarUrlGenerator();
		$generator->setRouter($this->router);
		$generator->setParams($params);

		$url = $generator->getSearchUrl();
		
		return $url;
	}

	public function saveSearchCookies()
	{		
		$response = new Response();
		foreach ($this->params as $key => $value) {			

			if(in_array($key,$this->params2IgnoreInCookie)) continue;			
			if(isset($value)){
				//echo $key.'='.$value.'<br>';
				if(is_array($value)) $value = '[array]'.serialize($value);
				$cookie = new Cookie($key,$value,time() + 3600 * 24 * 7);
				$response->headers->setCookie($cookie);
			}
		}
		$response->send();		
	}

	private function prepareParams()
	{
		if(empty($this->params)) return $this->params = array();

		$this->prepareCountryParams($this->params);
		$this->prepareCityParams($this->params);
		$this->prepareAreaParams($this->params);
		$this->prepareSportParams($this->params);
		$this->prepareNbDaysParams($this->params);
		$this->prepareTypeParams($this->params);
		$this->prepareStartDate($this->params);
		$this->prepareTimeParams($this->params);
		$this->preparePriceParams($this->params);
		$this->prepareOrganizerParams($this->params);

		return $this->params;
	}

	public function prepareStartDate($params = array())
	{
		$today = \date('Y-m-d');
		$cookie_date = (isset($this->cookies['date']) && $this->isFormattedDate($this->cookies['date']) == true)? $this->cookies['date'] : $today;		

		if(isset($params['date'])) {
			if($params['date'] == 'now') $day = $today;
			elseif($params['date'] == 'next')  $day = date('Y-m-d',strtotime($cookie_date.' + '.$params['nbdays'].' days'));
			elseif($params['date'] == 'prev') $day = date('Y-m-d',strtotime($cookie_date.' - '.$params['nbdays'].' days'));
			elseif($this->isFormattedDate($params['date'])) $day = $params['date'];
			else $day = $today;	
		}		
		else $day = $today;

		$params['date'] = $day;
		return $this->params = $params;
	}


	private function prepareTypeParams($params)
	{
		if(is_string($params['type'])) $a = explode('-',trim($params['type'],'-'));
		if(is_array($params['type'])) $a = $params['type'];		
		foreach ($a as $k => $type) {
			if(!in_array($type,$this->typeAllowed)) unset($a[$k]);
		}    	
		$params['type'] = $a;		
		if(count(array_diff($this->typeAllowed,$params['type'])) == 0) unset($params['type']);		
		return $this->params = $params;
	}

	private function prepareCountryParams($params)
	{
		//replace country name by country code
		if(isset($params['country']) && strlen($params['country'])>2 ) $params['country'] = $this->em->getRepository('MyWorldBundle:Country')->findCodeByCountryName($params['country']);

		return $this->params = $params;
	}

	private function prepareCityParams($params)
	{		

		//unset city_id if not numeric
		if(isset($params['city_id']) && !is_numeric($params['city_id']))  unset($params['city_id']);

		//unset city_name if set to 'all'
		if(isset($params['city_name']) && $params['city_name'] == 'all') unset($params['city_name']);

		//unset city_id if city_name is not defined
		if(isset($params['city_id']) && is_numeric($params['city_id']) && empty($params['city_name'])) unset($params['city_id']);

		//split city_name and area if so
		if(!empty($params['city_name'])) {
			if(strpos($params['city_name'],'+')>0){
				$r = explode('+',$params['city_name'],2);       
				$city = $this->em->getRepository('MyWorldBundle:City')->findCityByName($r[0],$params['country']);     	            
				if(isset($city)) $params['city_id'] = $city->getId();           
				if(isset($r[1]) && is_numeric($r[1])) $params['area'] = (int) $r[1];            	                   				
			} else {
				$city = $this->em->getRepository('MyWorldBundle:City')->findCityByName($params['city_name'],$params['country']);  
				if(isset($city)) $params['city_id'] = $city->getId();
			}
			//free memory
			unset($city);
			$this->em->clear();
		}  		
;
		return $this->params = $params;
	}

    private function prepareAreaParams($params)
    {
		//remove "+" and "km"
		if(isset($params['area']) && is_string($params['area'])) $params['area'] = (int) trim(str_replace('km','',str_replace('+','',$params['area'])));
		//set to null if not numeric
		if(!is_numeric($params['area']) || $params['area'] == 0) $params['area'] = null;
		//set a maximum
		if(isset($params['area']) && $params['area'] > 200) $params['area'] = 200;

		return $this->params = $params;
    }

    private function prepareSportParams($params)
    {    	
    	$sports = array();

    	//if all sports
    	if(isset($params['sports']) && $params['sports'] == 'all') {        	
        		$params['sports'] = array();
        		return $params;
    	}

    	//if none sports
    	if(empty($params['sports']) && empty($params['sport_name']) && empty($params['sport_id'])){
    		$params['sports'] = array();
    		return $params;
    	}

    	//if sport_id
    	if(!empty($params['sport_id']) && is_numeric($params['sport_id'])){
    		$sports[] = $params['sport_id'];
    	}

    	//merge with sports[]
    	if(!empty($params['sports'])){
    		if(is_string($params['sports']))
    			$sports = array_merge($sports,explode('-',trim($params['sports'],'-')));
    		if(is_array($params['sports']))
    			$sports = array_merge($sports,$params['sports']);
    	}

    	//merge with sport_name
    	if(!empty($params['sport_name']) && is_string($params['sport_name'])) {
    		$sports = array_merge($sports,explode('-',trim($params['sport_name'],'-')));
    	}

    	//avoid doublon
    	$sports = array_unique($sports);    		

    	//find sports in database
    	$a = array();    
    	$repo = $this->em->getRepository('WsSportsBundle:Sport');
       	foreach ($sports as $k => $sport) {
    		
    		if(is_numeric($sport))
    			$sport = $repo->findRowsById($sport);
    		elseif(is_string($sport))
    			$sport = $repo->findRowsBySlug($sport);

    		if(isset($sport) && !in_array($sport['id'],$a)){
    			$a[] = $sport['id'];
    		}    		
    	}
		
    	$params['sports'] = $a;
    	unset($sports);
    	unset($sport);
    	
    	
    	return $this->params = $params;
    }

    private function prepareNbDaysParams($params)
    {
    	//check days is numeric
        if(isset($params['nbdays']) && is_numeric($params['nbdays']))
        	$params['nbdays'] = (int) $params['nbdays'];
        else
        	$params['nbdays'] = $this->default['nbdays'];

        return $this->params = $params;
    }

    private function prepareTimeParams($params)
    {

    	if(isset($params['time']) && !empty($params['time'])){
    		$r = explode('-',$params['time'],2);
    		if(empty($r)) return $this->params;
    		$params['timestart'] = $this->formatTime($r[0]);
    		$params['timeend'] = $this->formatTime($r[1]);    		
    	}
    	if(isset($params['timestart']) && is_numeric($params['timestart'])) $params['timestart'] = $this->formatTime($params['timestart']);
    	if(isset($params['timeend']) && is_numeric($params['timeend'])) $params['timeend'] = $this->formatTime($params['timeend']);

    	unset($params['time']);

    	return $this->params = $params;
    }

    private function preparePriceParams($params)
    {
    	if(isset($params['price']) && !is_numeric($params['price'])) unset($params['price']);

		return $this->params = $params;    	
    }

    private function prepareOrganizerParams($params)
    {
    	if(is_numeric($params['organizer'])) return $this->params = $params;
    	if(is_string($params['organizer'])){
    		$u = $this->em->getRepository('MyUserBundle:User')->findOneByUsername($params['organizer']);    		
    		if(isset($u)) $params['organizer'] = $u->getId();
    		else unset($params['organizer']);
    		return $this->params = $params;
    	}
    }

    private function formatTime($time){
    	if(is_numeric($time) && $time >= 0 && $time <=9) return '0'.(int)$time.':00:00';
    	if(is_numeric($time) && $time >=10 && $time <=24) return (int)$time.':00:00';
    	return '00:00:00';
    }


	private function isFormattedDate($date)
	{
		$d = \DateTime::createFromFormat("Y-m-d",$date);
        if($d !== false && !array_sum($d->getLastErrors()))
            return $date;
        else 
            return false;
	}

}