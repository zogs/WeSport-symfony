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
		'timestart' => 0,
		'timeend' => 24,
		'price' => 0,
		'organizer' => null,

		);

	private $serializer;
	private $urlGenerator;
	private $params_cookie_ignored = array('PHPSESSID','hl','organizer');
	private $params_allowed = array(
		'type'=> array('person','asso','pro')
		);

	public function __construct(Container $container)
	{
		parent::__construct($container);

		$this->urlGenerator = new CalendarUrlGenerator();
		$this->serializer = $container->get('jms_serializer');
	}

	public function findCalendar()
	{
		$this->computeParams();
		return $this->em->getRepository('WsEventsBundle:Event')->findCalendarEvents($this->search);
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
			elseif(strpos($value,'[obj]') === 0) $value = str_replace('[obj]','',$value);
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

		$a = $this->search;
		$a['raw'] = $this->getParams();
		$a['url'] = $this->getSearchUrl($a);

		return $a;
	}

	public function getSearchUrl($params)
	{
		$this->urlGenerator->setRouter($this->router);
		$this->urlGenerator->setParams($params);
		return $this->urlGenerator->getSearchUrl();
	}

	public function saveSearchCookies()
	{		
		$response = new Response();
		foreach ($this->params as $key => $value) {			

			if(in_array($key,$this->params_cookie_ignored)) continue;			
			if(isset($value)){			
				if(is_array($value)) $value = '[array]'.serialize($value);
				if(is_object($value)) $value = '[obj]'.$value->getId();
				$cookie = new Cookie($key,$value,time() + 3600 * 24 * 7);
				$response->headers->setCookie($cookie);
			}
		}
		
		$response->send();		
	}

	private function prepareParams()
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
		$this->prepareOrganizerParams();

		return $this->params;
	}

	public function prepareDateParams()
	{
		$today = \date('Y-m-d');
		$cookie_date = (isset($this->cookies['date']) && $this->isFormattedDate($this->cookies['date']) == true)? $this->cookies['date'] : $today;		

		if(isset($this->params['date'])) {
			if($this->params['date'] == 'now') $day = $today;
			elseif($this->params['date'] == 'next')  $day = date('Y-m-d',strtotime($cookie_date.' + '.$this->params['nbdays'].' days'));
			elseif($this->params['date'] == 'prev') $day = date('Y-m-d',strtotime($cookie_date.' - '.$this->params['nbdays'].' days'));
			elseif($this->isFormattedDate($this->params['date'])) $day = $this->params['date'];
			else $day = $today;	
		}		
		else $day = $today;

		$this->search['date'] = $day;
		$this->params['date'] = $day;
		return;
	}


	private function prepareTypeParams()
	{
		$t = array();
		if(is_string($this->params['type'])) $t = explode('-',trim($this->params['type'],'-'));
		if(is_array($this->params['type'])) $t = $this->params['type'];		
		foreach ($t as $k => $type) {
			if(!in_array($type,$this->params_allowed['type'])) unset($t[$k]);
		}    	

		$this->search['type'] = $t;

		if(count(array_diff($this->params_allowed['type'],$t)) == 0) unset($this->params['type']);

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

		$this->search['country'] = $country;

		return;
	}

	private function prepareCityParams()
	{		
		if($this->params['city'] == $this->urlGenerator->defaults['city']) return;

		$city = null;
		if(!empty($this->params['city'])){
			if(strpos($this->params['city'],'+') > 0) {
				$r = explode('+',$this->params['city'],2);       
				$city = $this->em->getRepository('MyWorldBundle:City')->findCityByName($r[0],$this->search['country']->getCode());     	                      
				if(isset($r[1])) $this->prepareAreaParams($r[1]);
			}
			else {
				$city = $this->em->getRepository('MyWorldBundle:City')->findCityByName($this->params['city'],$this->search['country']->getCode());
			}
		}
		elseif(!empty($this->params['city_id']) && is_numeric($this->params['city_id'])){
			$city = $this->em->getRepository('MyWorldBundle:City')->find($this->params['city_id']);
		}
		elseif(!empty($this->params['city_name'])){
			$city = $this->em->getRepository('MyWorldBundle:City')->findCityByName($this->params['city_name'],$this->search['country']->getCode());
		}


		\Doctrine\Common\Util\Debug::dump($city);
		exit();
		$this->search['city'] = $city;		

		return;

	}

    private function prepareAreaParams($area = 0)
    {
		//remove "+" and "km"
		if(isset($this->params['area'])) $area = (int) trim(str_replace('km','',str_replace('+','',$this->params['area'])));
		//set to null if not numeric
		if(!is_numeric($area) || $area == 0) return null;
		//set a maximum
		if($area > 200) $area = 200;

		$this->search['area'] = $area;
		return;
    }


    private function prepareSportsParams()
    {    	
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
       	foreach ($sports as $k => $sport) {
    		
    		if(is_numeric($sport))
    			$sport = $repo->findRawById($sport);
    		elseif(is_string($sport))
    			$sport = $repo->findRawBySlug($sport);    		  
    	}

    	//avoid doublon
    	$sports = array_unique($sports); 
		
    	$this->search['sports'] = $sports;

    	unset($sports);
    	unset($sport);
    	
    	
    	return;
    }

    private function prepareNbdaysParams()
    {
    	//check days is numeric
	    if(isset($this->params['nbdays']) && is_numeric($this->params['nbdays']))
	    	$nb = $this->params['nbdays'];
	    else
	    	$nb = $this->default['nbdays'];

	    $this->search['nbdays'] = $nb;
	    return;
    }

    private function prepareTimeParams()
    {
    	if($this->params['time'] == $this->urlGenerator->defaults['time']) return;

    	$time = array();
    	if(isset($this->params['time']) && !empty($this->params['time'])){
    		$r = explode('-',$this->params['time'],2);
    		if(empty($r)) return null;
    		$time['start'] = $this->formatTime($r[0]);
    		$time['end'] = $this->formatTime($r[1]);    		
    	}
    	if(isset($this->params['timestart']) && is_numeric($this->params['timestart'])) $time['start'] = $this->formatTime($this->params['timestart']);
    	if(isset($this->params['timeend']) && is_numeric($this->params['timeend'])) $time['end'] = $this->formatTime($this->params['timeend']);

    	$this->search['time'] = $time;
    	return;
    }

    private function preparePriceParams()
    {
    	if(isset($this->params['price']) && !is_numeric($this->params['price'])) return null;

    	$this->search['price'] = $this->params['price'];
		return;
    }

    private function prepareOrganizerParams()
    {
    	if(!empty($this->params['organizer'])){
	    	if(is_numeric($this->params['organizer'])){
	    		$user = $this->em->getRepository('MyUserBundle:User')->findOneById($this->params['organizer']); 
	    	}
	    	elseif(is_string($this->params['organizer'])){
	    		$user = $this->em->getRepository('MyUserBundle:User')->findOneByUsername($this->params['organizer']);    			    		
	    	}

	    	$this->search['organizer'] = $user;
	    	return;
    		
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