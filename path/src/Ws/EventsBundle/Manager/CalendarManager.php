<?php

namespace Ws\EventsBundle\Manager;

use My\ManagerBundle\Manager\AbstractManager;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

class CalendarManager extends AbstractManager
{
	protected $em;
	private $params = array();
	private $computed = false;
	private $full = array();
	private $default = array(
		'country' => 'FR',
		'date' => '2014-04-01',
		'city_id'=>null,
		'city_name'=>null,
		'area'=>null,
		'sports'=> array(), //array(67,68,72)
		'nbdays'=>7,
		'type' => array(), //array('pro','asso','person')
		);

	private $params2IgnoreInCookie = array('PHPSESSID','hl');
	private $typeAllowed = array('person','asso','pro');
	private $cookies = array();
	private $query = array();
	private $uri = array();


	public function findCalendarByParams()
	{
		$params = $this->computeParams();
		return $this->em->getRepository('WsEventsBundle:Event')->findCalendarEvents($params);
	}

	public function setCookieParams($cookies)
	{
		foreach ($cookies as $key => $value) {
			//if array unserialize it
			if(strpos($value,'[array]') === 0) $value = unserialize(str_replace('[array]','',$value));
			$cookies[$key] = $value;
		}
		$this->cookies = $cookies;
	}

	public function setRequestParams($params)
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

	public function computeParams()
	{
		if($this->computed) return $this->params;
		$params = array_merge(
						$this->default,
						$this->cookies,
						$this->query,
						$this->uri
						);
		$this->params = $this->prepareParams($params);
		$this->computed = true;
		return $this->params;
	}
	public function getSearchParams()
	{
		return array(
			'raw' => $this->getRawSearchParams(),
			'full' => $this->getFullSearchParams(),
			'url' => $this->getUrlStringParam(),
			);
	}
	public function getRawSearchParams()
	{		
		return $this->computeParams();
	}

	public function getFullSearchParams()
	{
		
		$this->full['country'] = $this->em->getRepository('MyWorldBundle:Country')->findCountryByCode($this->params['country']);
		$this->full['location'] = $this->em->getRepository('MyWorldBundle:Location')->findLocationByCityId($this->params['city_id']);
		$this->em->clear();
		$this->full['area'] = (!empty($this->params['area']))? '+'.$this->params['area'].'km' : '';
		$this->full['nbdays'] = $this->params['nbdays'];
		$this->full['date'] = $this->params['date'];
		$this->full['sports'] = array();
		$repo = $this->em->getRepository('WsSportsBundle:Sport');
		foreach ($this->params['sports'] as $k => $id) {
			$this->full['sports'][] = $repo->findOneById($id);
		}

		return $this->full;
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
				$s .= $type.'+';
			}
			$s = trim($s,'+');
		}
		$s .= '/';
		$s .= $this->params['nbdays'];		
		$s .= '/';
		$s .= $this->params['date'];

		return $s;
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

	private function prepareParams($params = array())
	{
		if(empty($params)) return array();

		$params = $this->prepareCountryParams($params);
		$params = $this->prepareCityParams($params);
		$params = $this->prepareAreaParams($params);
		$params = $this->prepareSportParams($params);
		$params = $this->prepareNbDaysParams($params);
		$params = $this->prepareTypeParams($params);
		$params = $this->prepareStartDate($params);

		return $params;
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
		return $params;
	}


	private function prepareTypeParams($params)
	{
		if(is_string($params['type'])) $a = explode('+',trim($params['type'],'+'));
		if(is_array($params['type'])) $a = $params['type'];
		foreach ($a as $k => $type) {
			if(!in_array($type,$this->typeAllowed)) unset($a[$k]);
		}    	
		$params['type'] = $a;
		return $params;
	}

	private function prepareCountryParams($params)
	{
		//replace country name by country code
		if(isset($params['country']) && strlen($params['country'])>2 ) $params['country'] = $this->em->getRepository('MyWorldBundle:Country')->findCodeByCountryName($params['country']);

		return $params;
	}

	private function prepareCityParams($params)
	{		
		//unset city_id is not numeric
		if(isset($params['city_id']) && !is_numeric($params['city_id']))  unset($params['city_id']);

		//unset city_name if 'all'
		if(isset($params['city_name']) && $params['city_name'] == 'all') unset($params['city_name']);
		//splid city_name and area if so
		if(isset($params['city_name'])) {
			if(strpos($params['city_name'],'+')>0){
				$r = explode('+',$params['city_name'],2);       
				$city = $this->em->getRepository('MyWorldBundle:City')->findCityByName($r[0],$params['country']);     	            
				if(isset($city)) $params['city_id'] = $city->getId();           
				if(isset($r[1]) && is_numeric($r[1])) $params['area'] = (int) $r[1];            	                   				
			} else {
				$city = $this->em->getRepository('MyWorldBundle:City')->findCityByName($params['city_name'],$params['country']);  
				if(isset($city)) $params['city_id'] = $city->getId();
			}
		}  		

		return $params;
	}

    private function prepareAreaParams($params)
    {
    	//remove "+" and "km"
	if(isset($params['area']) && is_string($params['area'])) $params['area'] = (int) trim(str_replace('km','',str_replace('+','',$params['area'])));
	//set to null if not numeric
	if(!is_numeric($params['area']) || $params['area'] == 0) $params['area'] = null;
	//set a maximum
	if(isset($params['area']) && $params['area'] > 200) $params['area'] = 200;

	return $params;
    }

    private function prepareSportParams($params)
    {    	    	
        if(isset($params['sports'])) {
        	//unset if sport isn't defined
        	if($params['sports'] == 'all' || $params['sports']==NULL )	{
        		$params['sports'] = array();
        		return $params;
        	}
        	//split sports in an array
        	if(is_string($params['sports']) && strpos($params['sports'],'+')>0)
            	$sports = explode('+',trim($params['sports'],'+')); 
            else
            	$sports = array($params['sports']);

            //set sports id in an array
            foreach ($sports as $k => $sport) {
            		if(is_numeric($sport)) $sports[$k] = $sport;
            		elseif(is_string($sport)) {
            			//find by slug
                		$sport = $this->em->getRepository('WsSportsBundle:Sport')->findOneBySlug($sport);
                		if(isset($sport)) $sports[$k] = $sport->getId();            			
            		}                
                	else unset($sports[$k]);
            }   
            $params['sports'] = $sports;                        
        }
        return $params;
    }

    private function prepareNbDaysParams($params)
    {
    	//check days is numeric
        if(isset($params['nbdays']) && is_numeric($params['nbdays']))
        	$params['nbdays'] = (int) $params['nbdays'];
        else
        	$params['nbdays'] = $this->default['nbdays'];
       
        return $params;
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