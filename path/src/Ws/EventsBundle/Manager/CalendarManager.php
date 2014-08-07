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
	private $search = array(
		'date' => null,
		'country' => null,
		'city' => null,
		'area' => null,
		'sports' => null,
		'nbdays' => null,
		'type' => null,
		'time' => null,
		'price' => null,
		'level' => null,
		'organizer' => null,
		);
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
		'time' => array('start'=>'00:00:00','end'=>'00:00:00'),
		'timestart'=> null,
		'timeend' => null,
		'price' => null,
		'level' => array('all','beginner','average','confirmed','expert'),
		'organizer' => null,
		'dayofweek'=>array(),

		);

	private $flashbag;
	private $serializer;
	private $urlGenerator;
	private $params_cookie_ignored = array('PHPSESSID','hl','organizer','city_id','city_name','sport_name','sport_id','dayofweek');


	public function __construct(Container $container)
	{
		parent::__construct($container);

		$this->params = $this->default;
		$this->search = new Search();
		$this->urlGenerator = $container->get('calendar.url.generator');
		$this->serializer = $container->get('jms_serializer');
		$this->flashbag = $container->get('flashbag');
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
		
		$events = array();
		$nb = $this->search->getNbDays();
		$repo = $this->em->getRepository('WsEventsBundle:Event');
		
		for ($i=1; $i <= $nb; $i++) { 

			$this->search->setDate($day);			
			$events[$day] = $repo->findEvents($this->getSearch());
			$day = date("Y-m-d", strtotime($day. " +1 day"));
		}
		//prevent memory leak
		$this->em->clear();
		//save the search in cookie
		$this->saveSearchCookie();

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

	public function addParams($params)
	{		
		$this->addParameters($params);
	}

	public function addParamsURI($params)
	{
		foreach ($params as $key => $value) {
			if( NULL === $params[$key]) unset($params[$key]);
		}
		$this->addParameters($params);
	}

	public function addParamsFromCookies($cookies)
	{		
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

		$this->cookies = $a;
		$this->addParameters($a);		
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
	}

	public function addParamsDate($date)
	{
		$this->addParameters(array('date'=>$date));		
	}


	private function addParameters($params)
	{
		$this->params = array_merge($this->params,$params);
	}

	public function resetParams($resetCookie = true)
	{
		$this->params = $this->default;
		if($resetCookie == true) $this->resetCookie();
	}

	private function resetCookie()
	{
		$response = new Response();
		$allparams = array_merge((array)$this->search,$this->default);		
		foreach ($allparams as $key => $value) {						

			$cookie1 = new Cookie('calendar_param_'.$key,'',time() - 3600, '/');
			$cookie2 = new Cookie($key,'',time() - 3600, '/');											
			$response->headers->setCookie($cookie1);
			$response->headers->setCookie($cookie2);			
		}			
		$response->send();	
	}

	private function saveSearchCookie()
	{		
		$response = new Response();		

		foreach ($this->search as $key => $value) {			

			if(in_array($key,$this->params_cookie_ignored)) continue; //some params dont go in cookie	

			if(isset($value)){
				if(is_array($value)) $value = '[array]'.serialize($value);
				if(is_object($value)) $value = '[obj]'.$value->getId();
				$cookie = new Cookie('calendar_param_'.$key,$value,time() + 3600 * 24 * 7, '/');				
			} else {
				$cookie = new Cookie('calendar_param_'.$key,'',time() - 3600, '/');				
			}		
			
			$response->headers->setCookie($cookie);		
		}		
		$response->send();		
	}


	public function getSearch()
	{
		$this->search->setRawData($this->getParams());
		$this->urlGenerator->setSearch($this->search);
		$this->search->setUrl($this->urlGenerator->getUrl());
		$this->search->setUrlParams($this->urlGenerator->getUrlParams());
		$this->search->setShortUrlParams($this->urlGenerator->getShortUrlParams());
		return $this->search;
	}

	public function getSerializedSearch()
	{		
		return $this->serializer->serialize($this->search,'json');
	}

	public function setSerializedSearch($json)
	{
		exit('to be tested');
		$data = json_decode($json);
		foreach ($data as $key => $value) {
			if($key=='country') $data->$key = new Country((array)$value);
			if($key=='city') $data->$key = new City((array)$value);
			if($key=='organizer') $data->$key = new User((array)$value);
			
		}
		$this->search = (array)$data;
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

		return $this->search;
	}

	public function prepareDateParams()
	{
		$today = \date('Y-m-d');	
		$cookie_date = (isset($this->cookies['date']))? $this->cookies['date'] : $today;		

		if(isset($this->params['date'])) {
			if($this->params['date'] == 'now') $day = $today;
			elseif($this->params['date'] == 'next')  $day = date('Y-m-d',strtotime($cookie_date.' + '.$this->params['nbdays'].' days'));
			elseif($this->params['date'] == 'prev') $day = date('Y-m-d',strtotime($cookie_date.' - '.$this->params['nbdays'].' days'));
			elseif($this->params['date'] == 'none') $day = null;
			else $day = $this->formatDate($this->params['date']);
		}		
		else $day = $today;

		$this->search->setDate($day);
		$this->params['date'] = $day;
		return;
	}


	private function prepareCountryParams()
	{	
		//replace country name by country code
		if(isset($this->params['country']) && is_numeric($this->params['country']))
			$country = $this->em->getRepository('MyWorldBundle:Country')->findOneById($this->params['country']);
		elseif(isset($this->params['country']) && strlen($this->params['country'])>2 )
			$country = $this->em->getRepository('MyWorldBundle:Country')->findCountryByName($this->params['country']);
		elseif(isset($this->params['country']) && strlen($this->params['country']) <=2)
			$country = $this->em->getRepository('MyWorldBundle:Country')->findCountryByCode($this->params['country']);

		if(!isset($country) && $this->isFlashbagActive()) $this->flashbag->add('Veuillez choisir un pays','info');

		$this->search->setCountry($country);
		return;
	}

	private function prepareCityParams()
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
		elseif(is_string($findme)) $city = $this->em->getRepository('MyWorldBundle:City')->findCityByName($findme,$this->search->getCountry()->getCode());
			
		if(isset($city) && $city->exist()){
			$location = $this->em->getRepository('MyWorldBundle:Location')->findLocationByCityId($city->getId());		
			$this->search->setLocation($location);
			if(!empty($area)) $this->prepareAreaParams($area);
		}
		elseif(isset($findme)){

			if($this->isFlashbagActive()) $this->flashbag->add("Cette ville n'a pas été trouvé... Et vous sûr que ça s'écrit comme ça ?",'warning');
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
    			$sports = array_merge($sports,explode('-',trim($this->params['sports'],'-')));
    		if(is_array($this->params['sports']))
    			$sports = array_merge($sports,$this->params['sports']);
    	}

    	if(!empty($this->params['sport_name'])){
    		if(is_string($this->params['sport_name']))
    			$sports = array_merge($sports,explode('-',trim($this->params['sport_name'],'-')));
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
       	foreach ($sports as $k => $slug) {
    		
    		if(is_numeric($slug))
    			$sport = $repo->findRawById($slug);
    		elseif(is_string($slug))
    			$sport = $repo->findRawBySlug($slug);  
    		elseif(is_array($slug))
    			$sport = $slug;

    		if(empty($sport) && $this->isFlashbagActive() ) $this->flashbag->add('Pardon mais ce sport ne nous dit rien... '.$slug.'??','error');

    		$sports[$k] = $sport;  		      		
    	}    

    	//avoid doublon
    	$ids = array();
    	foreach ($sports as $k => $sport) {
			if(in_array($sport['id'], $ids)) unset($sports[$k]);
			$ids[] = $sport['id'];
    	}

    	$sports = array_values($sports); //reset keys value for futur use

    	$this->search->setSports($sports);
    	return;
    }


	private function prepareTypeParams()
	{
		//return null
		if(empty($this->params['type'])) return; //if not set
		if(is_string($this->params['type']) && $this->params['type'] == $this->urlGenerator->defaults['type']) return; //if equal to URL default value

		$a = array();
		if(is_string($this->params['type'])) $a = explode('-',trim($this->params['type'],'-'));
		if(is_array($this->params['type'])) $a = $this->params['type'];		
		foreach ($a as $k => $type) {
			if(is_numeric($type)){
				if(!array_key_exists($type, Event::$valuesAvailable['type'])) unset($a[$k]);
				else $a[$k] = $type;
			}
			else if(is_string($type)){
				if(!in_array($type,Event::$valuesAvailable['type'])) unset($a[$k]);	
				else $a[$k] = array_search($type, Event::$valuesAvailable['type']);
			}			
		}    	

		if(count(array_diff(Event::$valuesAvailable['type'],$a)) == 0) {
			unset($this->params['type']);		
			$a = null;
		}
		
		if(empty($a)) $a = null;
		$this->search->setType($a);

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
		
		if(count(array_diff(Event::$valuesAvailable['level'],$a)) == 0) {
			unset($this->params['level']);
			$a = null;
		}

		if(empty($a)) $a = null;
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
    	//return null 
    	if(empty($this->params['time']) && empty($this->params['timeend']) && empty($this->params['timestart'])) return; //if time not set
    	if(is_string($this->params['time']) && $this->params['time'] == $this->urlGenerator->defaults['time']) return; //if equal to URL default value
    	if(is_array($this->params['time']) && empty($this->params['timeend']) && empty($this->params['timestart']) && count(array_diff($this->default['time'],$this->params['time'])) == 0) return; //if equal to array default value

    	$time = array();
    	if(is_string($this->params['time'])){
    		$r = explode('-',$this->params['time'],2);
    		if(empty($r)) return null;
    		$time['start'] = $this->formatTime($r[0]);
    		$time['end'] = $this->formatTime($r[1]);    		
    	}
    	if(is_array($this->params['time'])) $time = $this->params['time'];
    	if(isset($this->params['timestart'])) $time['start'] = $this->formatTime($this->params['timestart']);
    	if(isset($this->params['timeend'])) $time['end'] = $this->formatTime($this->params['timeend']);

    	$this->search->setTime($time);
    	return;
    }

    private function preparePriceParams()
    {
    	if(isset($this->params['price']) && !is_numeric($this->params['price'])) return null;
    	if(is_string($this->params['price']) && $this->params['price'] == $this->urlGenerator->defaults['price']) return; //if equal to URL default value

    	$this->search->setPrice($this->params['price']);
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

    private function formatTime($t){  

    	if(is_string($t)){
    		if(preg_match('/^[0-9]{2}\:[0-9]{2}\:[0-9]{2}$/',$t)) return $t;  
    		if(preg_match('/^[0-9]{2}\:[0-9]{2}$/',$t)) return $t.':00';  
    		if(preg_match('/^[0-9]{1}\:[0-9]{2}$/',$t)) return '0'.$t.':00';  
    		if(preg_match('/^[0-9]{1}\:[0-9]{1}$/',$t)) return '0'.$t.'0:00';
    		if(preg_match('/^[0-9]{2}\:[0-9]{1}$/',$t)) return $t.'0:00';   	    
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