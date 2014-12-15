<?php

namespace Ws\EventsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Events;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

use My\WorldBundle\Entity\Country;
use My\WorldBundle\Entity\City;
use My\UserBundle\Entity\User;
use My\WorldBundle\Entity\Location;
use Ws\EventsBundle\Entity\Event;

/**
 * Search
 *
 * @ORM\Table(name="events_search")
 * @ORM\Entity(repositoryClass="Ws\EventsBundle\Repository\AlertRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Search
{    
    /**
     * @ORM\Id @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
    * @ORM\Column(name="date_created", type="datetime")
    */
    private $date_created = null;

     /**
    * @ORM\ManyToOne(targetEntity="My\UserBundle\Entity\User")
    * @ORM\JoinColumn(name="user_id")
    */
    private $user;

    /**
    * @ORM\Column(name="date", type="string", nullable=true)
    */
    private $date = null;

    /**
    * @ORM\ManyToOne(targetEntity="My\WorldBundle\Entity\Location", fetch="EAGER")
    * @ORM\JoinColumn(name="location_id", nullable=true)
    */
    private $location = null;

    /**
    * @ORM\Column(name="area_distance", type="integer", nullable=true)
    */
    private $area = null;

    /**
    * @ORM\ManyToMany(targetEntity="Ws\SportsBundle\Entity\Sport", fetch="EAGER")
    * @ORM\JoinTable(name="events_search_sports")
    */
    private $sports = null;

    /**
    * @ORM\Column(name="nb_days", type="integer", nullable=true)
    */
    private $nb_days = null;

    /**
    * @ORM\Column(name="day_of_week", type="string", nullable=true)
    */
    private $day_of_week = array();

    /**
    * @ORM\Column(name="type", type="string")
    */
    private $type = array();

    /**
    * @ORM\Column(name="timestart", type="datetime", nullable=true)
    */
    private $timestart = null;

    /**
    * @ORM\Column(name="timeend", type="datetime", nullable=true)
    */
    private $timeend = null;

    /**
    * @ORM\Column(name="price", type="integer", nullable=true)
    */
    private $price = null;

    /**
    * @ORM\Column(name="level", type="string")
    */
    private $level = array();


    /**
    * @ORM\ManyToOne(targetEntity="My\UserBundle\Entity\User")
    * @ORM\JoinColumn(name="organizer_id", nullable=true)
    */
    public $organizer = null;

    public $country; 
    public $raw_data = null;
    public $url = null;
    public $url_params = null;
    public $short_url_params = null;
    private $default = array( //usefull ?
        'day_of_week' => array('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'),
        );

    public function __construct()
    {
        $this->sports = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function onPrePersist()
    {
        if(is_array($this->type)) $this->type = json_encode($this->type, JSON_FORCE_OBJECT);
        if(is_array($this->level)) $this->level = json_encode($this->level, JSON_FORCE_OBJECT);
        if(is_array($this->day_of_week)) $this->day_of_week = json_encode($this->day_of_week, JSON_FORCE_OBJECT);
        $this->date_created = new \DateTime();
    }

    /**
     * @ORM\PostLoad
     */
    public function onPostLoad()
    {;
        $this->type = json_decode($this->type, JSON_FORCE_OBJECT);
        $this->level = json_decode($this->level, JSON_FORCE_OBJECT);
        $this->day_of_week = json_decode($this->day_of_week, JSON_FORCE_OBJECT);
    }

    public function getValues()
    {
        return array(
            'location' => $this->getLocation(),
            'area' => $this->getArea(),
            'sports' => $this->getSport(),
            'nb_days' => $this->getNbDays(),
            'day_of_week' => $this->getDayOfWeek(),
            'type' => $this->getType(),
            'time' => $this->getTime(),
            'price' => $this->getPrice(),
            'level' => $this->getLevel(),
            'country' => $this->getCountry(),
            'timestart' => $this->getTimeStart(),
            'timeend' => $this->getTimeEnd()
            );
    }

    public function getHtmlLocationString()
    {
        $html = '';
        if($this->hasLocation()){
            $html = '<strong>'.$this->location->getCity()->getName();
            if($this->area != null) $html .= ' (+'.$this->area.'km)';
            $html .= '</strong> ';
            $html .= '<span>'.$this->location->getLastState()->getName().' - </span>';
            $html .= '<span>'.$this->location->getCountry()->getName().'</span>';
        }

        return $html;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function hasDate()
    {
        if(isset($this->date) && $this->date != 'none') return true;
        return false;
    }
    public function setDate($date)
    {
        $this->date = $date;
    }

    public function setUser(User $user)
    {
    	$this->user = $user;
    	return $this;
    }

    public function getUser()
    {
    	return $this->user;
    }

    public function setAlert(Alert $alert)
    {
        $this->alert = $alert;
    }

    public function getAlert()
    {
        return $this->alert;
    }

    public function hasAlert()
    {
        if(isset($this->alert)) return true;
        return false;
    }

    public function getCountry()
    {
        return $this->country;
    }

    public function hasCountry()
    {
        if(isset($this->country)) return true;
        return false;
    }

    public function setCountry(Country $country)
    {
        $this->country = $country;
    }

    public function getLocation()
    {
        return $this->location;
    }

    public function hasLocation()
    {
        if(isset($this->location) && $this->location->getId() != null) return true;
        return false;
    }

    public function setLocation(Location $location)
    {
        $this->location = $location;
        return $this;
    }

    public function getArea()
    {
        return $this->area;
    }

    public function hasArea()
    {
        if(isset($this->area) && $this->area != 0) return true;
        return false;
    }

    public function setArea($area)
    {
        $this->area = $area;
    }

    public function getSports()
    {
        return $this->sports;
    }

    public function getSportsArrayIds()
    {
        $a = array();
        foreach ($this->sports as $k => $sport) {
            $a[] = $sport->getId();
        }
        return $a;
    }

    public function addSport($sport)
    {
        $this->sports->add($sport);
    }

    public function hasSports()
    {
        return !$this->sports->isEmpty();
    }    

    public function setSports($sports)
    {
        if(is_array($sports)){
            foreach ($sports as $i => $sport) {
                $this->sports->add($sport);
            }
        }
        elseif(is_a($sports,'\Doctrine\Common\Collections\ArrayCollection')){
            $this->sports = $sports;            
        }
        else{
            throw new \Exception("Sports must be an array or a Doctrine Collection", 1);
            
        }
    }

    public function getNbDays()
    {
        return $this->nb_days;
    }

    public function hasNbDays()
    {
        if(isset($this->nb_days) && $this->nb_days != 0) return $this->nb_days;
        return false;
    }

    public function setNbDays($nb)
    {
        $this->nb_days = $nb;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getTypeNames()
    {
        return array_map(function($n){ return Event::$valuesAvailable['type'][$n]; },$this->type);
    }

    public function getTypeKeys()
    {
        return array_keys($this->type);
    }

    public function hasType()
    {
        if(!empty($this->type) && count($this->type) != count(Event::$valuesAvailable['type'])) return true;
        return false;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getDayOfWeek()
    {
        return $this->day_of_week;
    }

    public function hasDayOfWeek()
    {
        if(!empty($this->day_of_week)) return true;
        return false;
    }

    public function setDayOfWeek($days)
    {
        $this->day_of_week = $days;
    }

    public function getTime($w = null)
    {
        if($w == 'start') return $this->time['start'];
        elseif($w == 'end') return $this->time['end'];
        else return $this->time;

    }

    public function hasTime($t = null)
    {
        if($t=='start' && !empty($this->time['start'])) return true;
        if($t=='end' && !empty($this->time['end'])) return true;
        if($t==null && ( isset($this->time['start']) || isset($this->time['end']) ) ) return true;
        return false;       
    }
    
    public function setTime($time)
    {
        if(is_array($time)){
            if(isset($time['start'])) $this->setTimeStart($time['start']);
            if(isset($time['end'])) $this->setTimeEnd($time['end']);
            $this->time = $time;            
        }
    }

    public function getTimeStart()
    {
        return $this->timestart;
    }

    public function hasTimeStart()
    {
        if(isset($this->timestart)) return true;
        return false;
    }

    public function setTimeStart(\Datetime $datetime)
    {
        $this->timestart = $datetime;
    }

    public function getTimeEnd()
    {
        return $this->timeend;
    }

    public function hasTimeEnd()
    {
        if(isset($this->timeend)) return true;
        return false;
    }

    public function setTimeEnd(\Datetime $datetime)
    {
        $this->timeend = $datetime;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function hasPrice()
    {
        if(isset($this->price)) return true;
        return false;
    }

    public function setPrice($price)
    {
        $this->price = $price;
    }

    public function getLevel()
    {        
        return $this->level;
    }

    public function getLevelNames()
    {
        return array_map(function($i){ return Event::$valuesAvailable['level'][$i];},$this->level);
    }

    public function hasLevel()
    {
        if(isset($this->level['0'])) return false; //"all level"
        if(!empty($this->level) && count($this->level) != count(Event::$valuesAvailable['level'])) return true;        
        return false;
    }

    public function setLevel($level)
    {
        $this->level = $level;
    }

    public function getOrganizer()
    {
        return $this->organizer;
    }

    public function hasOrganizer()
    {
        if(isset($this->organizer)) return true;
        return false;
    }

    public function setOrganizer(User $organizer)
    {
        $this->organizer = $organizer;
    }

    public function getRawData()
    {
        return $this->raw_data;
    }

    public function hasRawData()
    {
        if(isset($this->raw_data)) return true;
        return false;
    }

    public function setRawData($data)
    {
        $this->raw_data = $data;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function hasUrl()
    {
        if(isset($this->url)) return $url;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function getUrlParams()
    {
        return $this->url_params;
    }

    public function hasUrlParams()
    {
        if(isset($this->url_params)) return $url_params;
    }

    public function setUrlParams($a)
    {
        $this->url_params = $a;
    }

    public function getShortUrlParams()
    {
        return $this->short_url_params;
    }

    public function hasShortUrlParams()
    {
        if(isset($this->short_url_params)) return $short_url_params;
    }

    public function setShortUrlParams($a)
    {
        $this->short_url_params = $a;
    }


}
