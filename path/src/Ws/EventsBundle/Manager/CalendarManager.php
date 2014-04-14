<?php

namespace Ws\EventsBundle\Manager;

use My\ManagerBundle\Manager\AbstractManager;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

class CalendarManager extends AbstractManager
{
	protected $em;
	private $params = array();
	private $default = array(
		'country' => 'FR',
		'date' => '2014-04-01',
		//'city_id'=>1848034,
		//'city_name'=>'Dijon',
		//'area'=>100,,
		//'sport'=> array(67,68,72)
		'nbdays'=>7,
		);

	private $fragments;
	private $request;
	private $response;


	public function findCalendarByParams($request,$params = array())
	{
		
		$this->setRequest($request);		
		$this->setUriFragments($params);		
		$this->buildParams();
		
		return $this->em->getRepository('WsEventsBundle:Event')->findCalendarEvents($this->params);
	

	}

	public function buildParams()
	{

		//params from cookie data
		$cookie = $this->getPreviousCookieParams();		
		//params from query GET parameters
		$query = $this->request->query->all();		
		//build params from URL fragments
		$fragments = $this->fragments;
		//merge params		
		$params = array_merge($this->default,$cookie,$query,$fragments);				
		//prepare params in the right format
		$params = $this->prepareParams($params);		
		//set cookie params
		$this->setCookieParams($params);
		//set params

		$this->params = $params;
		//return params
		return $params;
	}

	private function getURIFragments()
	{		
		return $this->fragments;
	}

	private function setUriFragments($fragments)
	{
		foreach ($fragments as $key => $value) {
			if( NULL === $fragments[$key]) unset($fragments[$key]);
		}
		$this->fragments = $fragments;
	}

	private function setRequest($request)
	{
		$this->request = $request;
	}

	private function getPreviousCookieParams(){

		$cookies = $this->request->cookies->all();
		foreach ($cookies as $key => $value) {
			if(strpos($value,'[array]') === 0) $value = unserialize(str_replace('[array]','',$value));
			$cookies[$key] = $value;
		}
		return $cookies;
	}

	public function setCookieParams($params)
	{		
		$response = new Response();
		foreach ($params as $key => $value) {			

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
		$previousDate = $this->request->cookies->get('date');
		if(empty($previousDate) || $this->isFormattedDate($previousDate) == false) $previousDate = $today;

		if(isset($params['date'])) {
			if($params['date'] == 'now') $day = $today;
			elseif($params['date'] == 'next')  $day = date('Y-m-d',strtotime($previousDate.' + '.$params['nbdays'].' days'));
			elseif($params['date'] == 'prev') $day = date('Y-m-d',strtotime($previousDate.' - '.$params['nbdays'].' days'));
			else {
				if($this->isFormattedDate($params['date'])){
					$day = $params['date'];
				}
				else
					$day = $today;			
			}
		}
		else $day = $today;

		$params['date'] = $day;
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

	public function prepareParams($params = array())
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
        //
        if(isset($params['area'])) unset($params['area']);
        //find city_id by city name
        if(isset($params['city']) && $params['city'] != 'all') {
            $r = explode('+',$params['city'],2);
            $city = $this->em->getRepository('MyWorldBundle:City')->findCityByName($r[0],$params['country']);
            if(isset($city)) {
                $params['city_id'] = (int) $city->getId();
                unset($params['city']);
                if(isset($r[1]) && is_numeric($r[1])) $params['area'] = (int) $r[1];            
            }
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

}