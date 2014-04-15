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
	private $default = array(
		'country' => 'FR',
		'date' => '2014-04-01',
		'city_id'=>null,
		'city_name'=>null,
		'area'=>0,
		'sport'=> null, //array(67,68,72)
		'nbdays'=>7,
		);

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
		return $this->computeParams();
	}

	public function saveSearchCookies()
	{		
		$response = new Response();
		foreach ($this->params as $key => $value) {			

			if(isset($value)){
				//echo $key.'='.$value.'<br>';
				if(is_array($value)) $value = '[array]'.serialize($value);
				$cookie = new Cookie($key,$value,time() + 3600 * 24 * 7);
				$response->headers->setCookie($cookie);
			}
		}
		$response->send();		
	}

	public function prepareStartDate($params = array())
	{

		$day = null;
		$today = \date('Y-m-d');
		$previousDate = $this->cookies['date'];
		if(empty($previousDate) || $this->isFormattedDate($previousDate) == false) $previousDate = $today;

		if(isset($params['date'])) {
			if($params['date'] == 'now') $day = $today;
			elseif($params['date'] == 'next')  $day = date('Y-m-d',strtotime($previousDate.' + '.$params['nbdays'].' days'));
			elseif($params['date'] == 'prev') $day = date('Y-m-d',strtotime($previousDate.' - '.$params['nbdays'].' days'));
			elseif($this->isFormattedDate($params['date'])) $day = $params['date'];
			else $day = $today;	
		}		
		else $day = $today;

		$params['date'] = $day;
		return $params;
	}


	private function prepareParams($params = array())
    {
    	if(empty($params)) return array();
        
        $params = $this->prepareCountryParams($params);
        $params = $this->prepareCityParams($params);
        $params = $this->prepareSportParams($params);
        $params = $this->prepareNbDaysParams($params);
        $params = $this->prepareStartDate($params);
        
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
        //unset area if not numeric
        if(isset($params['area']) && !is_numeric($params['area'])) unset($params['area']);
        //unset city_name if 'all'
        if(isset($params['city_name']) && $params['city_name'] == 'all') unset($params['city_name']);
        //splid city_name and area if so
        if(isset($params['city_name']) && strpos($params['city_name'],'+')>0) {
            $r = explode('+',$params['city_name'],2);            	            
            $params['city_name'] = $r[0];             
            if(isset($r[1]) && is_numeric($r[1])) $params['area'] = (int) $r[1];            	                   	
        }  


        return $params;
    }

    private function prepareSportParams($params)
    {    	
        if(isset($params['sports'])) {
        	
        	if($params['sports'] == 'all' || $params['sports']==NULL ){
        		unset($params['sports']);
        		return $params;
        	}
        	//if sports params is a string or have a "+" in it
        	if(is_string($params['sports']) && strpos($params['sports'],'+')>0)
            	$sports = explode('+',$params['sports']); 
            else
            	$sports = array($params['sports']);

            //find by slug
            foreach ($sports as $key => $slug) {
                $sport = $this->em->getRepository('WsSportsBundle:Sport')->findOneBySlug($slug);
                if(isset($sport)) $sports[$key] = $sport->getId();
                else unset($sports[$key]);
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