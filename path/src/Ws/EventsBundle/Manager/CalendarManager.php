<?php

namespace Ws\EventsBundle\Manager;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\DependencyInjection\Container;

use My\ManagerBundle\Manager\AbstractManager;
use Ws\EventsBundle\Manager\CalendarUrlGenerator;
use Ws\EventsBundle\Entity\Event;
use Ws\EventsBundle\Entity\Search;
use My\WorldBundle\Entity\Country;
use My\WorldBundle\Entity\City;
use My\UserBundle\Entity\User;

class CalendarManager extends AbstractManager
{
	protected $em;
	private $params = array();
	private $cookies = array();
	private $query = array();
	private $uri = array();
	private $computed = false;	
	private $default = array(
		'country' => null,
		'date' => null,
		'city' => null,
		'city_id'=>null,
		'city_name'=>null,
		'location'=>null,
		'area'=>null,
		'sports'=> null, //array(67,68,72)
		'nbdays'=>7,
		'type' => null,	
		'time' => array(),	
		'timestart'=> null,
		'timeend' => null,
		'price' => null,
		'level' => array('all'),
		'organizer' => null,
		'dayofweek'=>array(),

		);

	private $flashbag;
	private $urlGenerator;
	private $params_cookie_ignored = array('PHPSESSID','hl','organizer','city_id','city_name','sport_name','sport_id','dayofweek','time');


	public function __construct(Container $container)
	{
		parent::__construct($container);

		$this->params = $this->default;
		$this->search = new Search();
		$this->urlGenerator = $container->get('calendar.url.generator');
		$this->flashbag = $container->get('flashbag');
		$this->response = new Response();		
	}

	public function findCalendar()
	{
		//prepare search
		$this->prepareParams();

		//disable sql logging
		$this->em->getConnection()->getConfiguration()->setSQLLogger(null);

		//get first day of the week
		$day = $this->search->getDate();
		if($day == null) throw new Exception("First day of the week is missing", 1);
		//stock the first day for later
		$firstDay = $day;
		
		$events = array();
		$nb = $this->search->getNbDays();
		$repo = $this->em->getRepository('WsEventsBundle:Event');
		
		for ($i=1; $i <= $nb; $i++) { 

			$this->search->setDate($day);			
			$events[$day] = $repo->findEvents($this->getSearch());
			$day = date("Y-m-d", strtotime($day. " +1 day"));
		}
		
		//reset date to the first day of the week
		$this->search->setDate($firstDay);
		//prevent memory leak
		$this->em->clear();
		//save the search in cookie
		$this->saveCookies();

		return $events;
	}

	public function disableFlashbag()
	{
		$this->flashbag = null;
	}	

	public function isFlashbagActive()
	{
		if(isset($this->flashbag)) return true;
		return false;
	}

	public function getParams()
	{
		return $this->params;
	}


	public function setSearch($search)
	{
		if($search === null)
			$this->search = new Search();
		else 
			$this->search = $search;

		return $this;
	}

	public function addParams($params)
	{		
		$this->addParameters($params);

		return $this;
	}

	public function addParamsURI($params)
	{
		foreach ($params as $key => $value) {
			if( NULL === $params[$key]) unset($params[$key]);
		}

		$this->addParameters($params);

		return $this;
	}

	public function addParamsFromCookies($cookies = array())
	{		
		if(null === $cookies) return $this;
		
		$a = array();
		foreach ($cookies as $k => $value) {
			if(in_array($k,$this->params_cookie_ignored)) continue;
			if(strpos('calendar_param_',$k)==0){												
				if(strpos($value,'[array]') === 0) $value = unserialize(str_replace('[array]','',$value));//if array unserialize it				
				elseif(strpos($value,'[obj]') === 0) $value = str_replace('[obj]','',$value);
				$param = str_replace('calendar_param_','',$k);
				$a[$param] = $value;				
			}
		}	

		//set cookies parameters
		$this->cookies = $a;

		//add cookies parameters to global parameters (except date)
		if(isset($a['date'])) unset($a['date']);
		$this->addParameters($a);

		return $this;		
	}

	public function addParamsFromUrl($url)
	{
		$params = array();
		$urlParams = explode('/', str_replace(' ','+',$url));
		$index = $this->urlGenerator->getRouteParams();
		foreach ($urlParams as $key => $value) {
			$params[$index[$key]] = $value;
		}

		$this->addParameters($params);	

		return $this;
	}

	public function addParamsDate($date)
	{
		$this->addParameters(array('date'=>$date));

		return $this;		
	}


	public function addParameters($params)
	{
		if(empty($params)) return $this;

		$this->params = array_merge($this->params,$params);

		return $this;
	}

	public function setAutoLocation($bool = null)
	{
		$this->search->autoLocation = $bool;
	}

	public function getAutoLocation()
	{
		return $this->search->autoLocation;
	}

	public function resetParams($resetCookie = true)
	{
		$this->params = $this->default;
		if($resetCookie == true) $this->resetCookie();

		return $this;
	}


	public function resetSearch()
	{
		$this->search = new Search();

		return $this;
	}

	public function resetCookie()
	{
	
		$allparams = $this->default;		
		foreach ($allparams as $key => $value) {						
			$response = new Response();		
			$response->headers->clearCookie('calendar_param_'.$key);
			$response->headers->clearCookie($key);																
			$response->send();	
		}			

		return $this;
	}

	public function saveCookies()
	{						
		foreach ($this->params as $key => $value) {			
			
			if(in_array($key,$this->params_cookie_ignored)) continue; //some params dont go in cookie	

			if(isset($value)){
				if(is_array($value)) $value = '[array]'.serialize($value);
				if(is_object($value)) $value = '[obj]'.$value->getId();
				$cookie = new Cookie('calendar_param_'.$key,$value,time() + 3600 * 24 * 7, '/',null,false,false);				
			} else {
				$cookie = new Cookie('calendar_param_'.$key,'',time() - 3600, '/');				
			}						

			$this->response->headers->setCookie($cookie);		
	
		}		
		$this->response->sendHeaders();
	}

	public function getResponse()
	{
		return $this->response;
	}

	public function getSearch()
	{				
		return $this->search;
	}
	

	public function prepareParams()
	{
		if(empty($this->params)) return $this->params = array();

		$this->prepareDateParams();
		$this->prepareCountryParams();
		$this->prepareCityParams();
		$this->prepareAreaParams();
		$this->prepareTypeParams();
		$this->prepareSportsParams();
		$this->prepareNbdaysParams();
		$this->prepareTimeParams();
		$this->preparePriceParams();
		$this->prepareLevelParams();
		$this->prepareOrganizerParams();
		$this->prepareDayOfWeekParams();		

		$this->prepareUrl();

		return $this;
	}

	public function prepareDateParams()
	{	
		//get current date to today's date
		$cdate = new \DateTime('now');
		$cdate = $cdate->format('Y-m-d');

		//set current date to cookie date except if it's a past date
		if(isset($this->cookies['date'])){
			$cookieDate = \DateTime::createFromFormat('Y-m-d',$this->cookies['date']);			
			if($cookieDate->format('Y-m-d') >= $cdate) $cdate = $this->cookies['date'];
		}
		
		//calcul date params
		if(isset($this->params['date'])) {			
			if($this->params['date'] == 'now') $date = $cdate;
			elseif($this->params['date'] == 'next')  $date = date('Y-m-d',strtotime($cdate.' + '.$this->params['nbdays'].' days'));
			elseif($this->params['date'] == 'prev') $date = date('Y-m-d',strtotime($cdate.' - '.$this->params['nbdays'].' days'));
			elseif($this->params['date'] == 'none') $date = null;
			else $date = $this->formatDate($this->params['date']);
		}		
		else $date = $cdate;

		$this->search->setDate($date);
		$this->params['date'] = $date;

		return;
	}


	public function prepareCountryParams()
	{	
		//replace country name by country code
		if(isset($this->params['country']) && is_numeric($this->params['country']))
			$country = $this->em->getRepository('MyWorldBundle:Country')->findOneById($this->params['country']);
		elseif(isset($this->params['country']) && strlen($this->params['country'])>2 )
			$country = $this->em->getRepository('MyWorldBundle:Country')->findCountryByName($this->params['country']);
		elseif(isset($this->params['country']) && strlen($this->params['country']) <=2)
			$country = $this->em->getRepository('MyWorldBundle:Country')->findCountryByCode($this->params['country']);

		if(!isset($country)) return;

		$this->search->setCountry($country);
		return;
	}

	public function prepareCityParams()
	{	
		$city = null;
		$findme = null;
		if(!empty($this->params['location']) && is_array($this->params['location']) ){			
			if(!empty($this->params['location']['city_id'])) $this->params['city_id'] = $this->params['location']['city_id'];
			if(!empty($this->params['location']['city_name'])) $this->params['city_name'] = $this->params['location']['city_name'];
		}
		if(!empty($this->params['city']) && $this->params['city'] != $this->urlGenerator->defaults['city']){			
			if(strpos($this->params['city'],'+') > 0) {
				$r = explode('+',$this->params['city'],2); 
				$findme = $r[0];				    
				if(isset($r[1])) $area = $r[1];
			} else {
				$findme = $this->params['city'];				
			}
		}
		if(!empty($this->params['city_name'])) $findme = $this->params['city_name'];
		if(!empty($this->params['city_id']) && is_numeric($this->params['city_id'])) $findme = $this->params['city_id'];					

		if(is_numeric($findme)) $city = $this->em->getRepository('MyWorldBundle:City')->find($findme);
		elseif(is_string($findme)) {
			if($this->search->getCountry()) $city = $this->em->getRepository('MyWorldBundle:City')->findCityByName($findme,$this->search->getCountry()->getCode());
			else $city = $this->em->getRepository('MyWorldBundle:City')->findCityByName($findme);
		}
			
		if(isset($city) && $city->exist()){
			$location = $this->em->getRepository('MyWorldBundle:Location')->findLocationByCityId($city->getId());	
			$this->search->setLocation($location);
			if(!empty($area)) $this->params['area'] = $area;
		}
		elseif(isset($findme)){

			if($this->isFlashbagActive()) $this->flashbag->add("Cette ville n'a pas été trouvé... Peut être une erreur d'orthographe ?",'warning');
		}				
		return;
	}

    private function prepareAreaParams($area = 0)
    {    	
    	//return null if no city or no location
    	if($this->search->hasLocation() == false || ($this->search->hasLocation() === true && $this->search->getLocation()->hasCity() === false)) return null;
	//remove "+" and "km"
	if(!empty($this->params['area'])) $area = (int) trim(str_replace('km','',str_replace('+','',$this->params['area'])));
	//set to null if not numeric
	if(!is_numeric($area) || $area == 0) return null;
	//set a maximum
	if($area > 200) $area = 200;

	$this->search->setArea($area);
	return;
    }


    private function prepareSportsParams()
    {    	
    	if(empty($this->params['sports']) && empty($this->params['sport_name']) && empty($this->params['sport_id'])) return;
    	if(isset($this->params['sports']) && empty($this->params['sport_name']) && empty($this->params['sport_id']) && is_string($this->params['sports']) && $this->params['sports'] == $this->urlGenerator->defaults['sports']) return;    	
    	
    	$sports = array();

    	if(!empty($this->params['sports'])){
    		if(is_string($this->params['sports']))
    			$sports = array_merge($sports,explode('+',trim($this->params['sports'],'+')));
    		if(is_array($this->params['sports']))
    			$sports = array_merge($sports,$this->params['sports']);
    	}

    	if(!empty($this->params['sport_name'])){
    		if(is_string($this->params['sport_name']))
    			$sports = array_merge($sports,explode('+',trim($this->params['sport_name'],'+')));
    		if(is_array($this->params['sport_name']))
    			$sports = array_merge($sports,$this->params['sport_name']);

    	}
    	if(!empty($this->params['sport_id']) && is_numeric($this->params['sport_id'])){
    		$sports[] = $this->params['sport_id'];
    	}

    	//avoid doublon
    	$sports = array_unique($sports);    		

    	//find sports in database   
    	$repo = $this->em->getRepository('WsSportsBundle:Sport');
       	foreach ($sports as $i => $key) {
    		
    		if(is_numeric($key))
    			$sport = $repo->findOneById($key);
    		elseif(is_string($key))
    			$sport = $repo->findOneBySlug($key);  
    		elseif(is_array($key))
    			$sport = $key;
    		
    		if(empty($sport) && $this->isFlashbagActive() ) $this->flashbag->add('Ce sport est inconnu au bataillon... '.$key.'??','error');

    		$sports[$i] = $sport;  		      		
    	}        	

    	//avoid doublon
    	$ids = array();

    	foreach ($sports as $k => $sport) {
			if(in_array($sport->getId(), $ids)) unset($sports[$k]);
			$ids[] = $sport->getId();
    	}

    	$this->search->setSports($sports);
    	return;
    }


	private function prepareTypeParams()
	{

		//return null
		if(empty($this->params['type'])) return; //if not set
		if(is_string($this->params['type']) && $this->params['type'] == $this->urlGenerator->defaults['type']) return; //if equal to URL default value

		$a = array();
		$r = array();
		if(is_string($this->params['type'])) $a = explode('-',trim($this->params['type'],'-'));
		if(is_array($this->params['type'])) $a = $this->params['type'];		
		foreach ($a as $k => $type) {
			if(is_numeric($type)){
				if(!array_key_exists($type, Event::$valuesAvailable['type'])) continue;
				$r[$type] = Event::$valuesAvailable['type'][$type];
			}
			else if(is_string($type)){
				if(!in_array($type,Event::$valuesAvailable['type'])) continue;	
				$r[array_search($type, Event::$valuesAvailable['type'])] = $type;
			}			
		}    	
		
		$r = array_values($r);

		$this->search->setType($r);

		return;
	}

	private function prepareLevelParams()
	{
		if(empty($this->params['level'])) return ;		
		if(is_string($this->params['level']) && $this->params['level'] == $this->urlGenerator->defaults['level']) return; //if equal to URL default value

		$a = array();
		if(is_string($this->params['level'])) $a = explode('-',trim($this->params['level'],'-'));
		if(is_array($this->params['level'])) $a = $this->params['level'];
		foreach ($a as $k => $level) {
			if(is_numeric($level)){
				if(!array_key_exists($level, Event::$valuesAvailable['level'])) unset($a[$k]);
				else $a[$k] = $level;
			}
			else if(is_string($level)){
				if(!in_array($level,Event::$valuesAvailable['level'])) unset($a[$k]);	
				else $a[$k] = array_search($level, Event::$valuesAvailable['level']);		
			}
		}
		
		$this->search->setLevel($a);		

		return;
	}


    private function prepareNbdaysParams()
    {
    	//check days is numeric
	    if(isset($this->params['nbdays']) && is_numeric($this->params['nbdays']))
	    	$nb = $this->params['nbdays'];
	    else
	    	$nb = $this->default['nbdays'];

	    $this->search->setNbDays($nb);
	    return;
    }

    private function prepareTimeParams()
    {    	
    	if(empty($this->params['time']) && empty($this->params['timeend']) && empty($this->params['timestart'])) return; //if time not set
    	if(is_string($this->params['time']) && $this->params['time'] == $this->urlGenerator->defaults['time']) return; //if equal to URL default value    	
    	

    	$time = array();
    	if(is_string($this->params['time'])){
    		$r = explode('-',$this->params['time'],2);    		
    		if(count($r) != 2) throw new \Exception("Time in url is not aknowledge");
    		$time['start'] = $this->formatTime($r[0]);
    		$time['end'] = $this->formatTime($r[1]);    		
    	}
    	    	
    	if(isset($this->params['timestart'])) $time['start'] = $this->formatTime($this->params['timestart']);
    	if(isset($this->params['timeend'])) $time['end'] = $this->formatTime($this->params['timeend']);
    	
    	
    	if(isset($time['start'])){
    		$d = \DateTime::createFromFormat('H:i:s',$time['start']);
    		$this->search->setTimeStart($d);
    	}

    	if(isset($time['end'])){
    		$d = \DateTime::createFromFormat('H:i:s',$time['end']);
    		$this->search->setTimeEnd($d);
    	} 

    	return;
    }

    private function preparePriceParams()
    {
    	$price = null;
    	if(isset($this->params['price'])){
    		if(is_numeric($this->params['price'])) $price = $this->params['price'];
    		elseif(is_string($this->params['price'])){
    			if($this->params['price'] == $this->urlGenerator->defaults['price']) return;
    			$price = str_replace('€', '', $this->params['price']);
    			if(!is_numeric($price)) $price = null;
    		}
    	}

    	$this->search->setPrice($price);
		return;
    }

    private function prepareOrganizerParams()
    {
    	if(empty($this->params['organizer'])) return;
		if(is_string($this->params['organizer']) && $this->params['organizer'] == $this->urlGenerator->defaults['organizer']) return; //if equal to URL default value    	

    	$user = null;
    	if(is_numeric($this->params['organizer'])){
    		$user = $this->em->getRepository('MyUserBundle:User')->findOneById($this->params['organizer']); 
    	}
    	elseif(is_string($this->params['organizer'])){
    		$user = $this->em->getRepository('MyUserBundle:User')->findOneByUsername($this->params['organizer']);    			    		
    	}

    	if($this->isFlashbagActive() && !isset($user) || (isset($user) && $user->getId() == 0))  $this->flashbag->add("Cet utilisateur n'existe pas : ".$this->params['organizer'],'error');

    	$this->search->setOrganizer($user);
    	return;
    		
    	
    }

    private function prepareDayOfWeekParams()
    {
    	if(empty($this->params['dayofweek'])) return;

    	if(is_array($this->params['dayofweek'])) $days = $this->params['dayofweek'];
    	if(is_string($this->params['dayofweek'])) $days = explode('-',$this->params['dayofweek']);

    	$days = array_values($days); 

    	$this->search->setDayOfWeek($days);
    	return;
    }

    private function prepareUrl()
    {
    	$this->search->setUrl($this->urlGenerator->setSearch($this->search)->getUrl());
	$this->search->setUrlParams($this->urlGenerator->getUrlParams());
	$this->search->setShortUrlParams($this->urlGenerator->getShortUrlParams());
    }

    private function formatTime($t){  

    	if(empty($t)) return;
    	if(is_string($t)){
    		if(preg_match('/^[0-9]{2}\:[0-9]{2}\:[0-9]{2}$/',$t)) return $t;  
    		if(preg_match('/^[0-9]{2}\:[0-9]{2}$/',$t)) return $t.':00';  
    		if(preg_match('/^[0-9]{1}\:[0-9]{2}$/',$t)) return '0'.$t.':00';  
    		if(preg_match('/^[0-9]{1}\:[0-9]{1}$/',$t)) return '0'.$t.'0:00';
    		if(preg_match('/^[0-9]{2}\:[0-9]{1}$/',$t)) return $t.'0:00'; 
    		throw new \Exception("Time is not in a proper format");  	    
    	}
    	if(is_array($t)){
    		if(isset($t['hour']) && isset($t['minute'])) return $this->formatTime($t['hour'].':'.$t['minute']);
    	}
    	return;
    }


	private function formatDate($date)
	{
		if(preg_match('/[0-9]{4}\-[0-9]{2}\-[0-9]{1,2}/', $date)){
			$d = \DateTime::createFromFormat('Y-m-d',$date);
			if($d !== false && !array_sum($d->getLastErrors())) return $d->format('Y-m-d');
		}
		if(preg_match('/[0-9]{2}[a-zA-Z]{3}[0-9]{2}/',$date)){
			$d = \DateTime::createFromFormat('dMy',$date);
			if($d !== false && !array_sum($d->getLastErrors())) return $d->format('Y-m-d');	
		}
        
        return \date('Y-m-d');        
	}

}