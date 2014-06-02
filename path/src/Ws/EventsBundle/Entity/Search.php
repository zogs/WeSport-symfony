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

/**
 * Search
 *
 * @ORM\Table(name="events_alert_search")
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
    public $date_created = null;

    /**
    * @ORM\Column(name="date", type="string", nullable=true)
    */
    private $date = null;

    /**
    * @ORM\ManyToOne(targetEntity="My\WorldBundle\Entity\Location")
    * @ORM\JoinColumn(name="location_id", referencedColumnName="id", nullable=false)
    */
    public $location = null;

    /**
    * @ORM\Column(name="area_distance", type="integer", nullable=true)
    */
    public $area = null;

    /**
    * @ORM\Column(name="sports", type="string", nullable=true)
    */
    public $sports = array();

    /**
    * @ORM\Column(name="nb_days", type="integer", nullable=true)
    */
    public $nb_days = null;

    /**
    * @ORM\Column(name="day_of_week", type="string", nullable=true)
    */
    public $day_of_week = array();

    /**
    * @ORM\Column(name="type", type="string")
    */
    public $type = array();

    /**
    * @ORM\Column(name="time", type="string")
    */
    public $time = array();

    /**
    * @ORM\Column(name="price", type="integer", nullable=true)
    */
    public $price = null;

    /**
    * @ORM\Column(name="organizer_id", nullable=true)
    * @ORM\ManyToOne(targetEntity="My\UserBundle\Entity\User")
    */
    public $organizer = null;


    public $country;
    public $timestart = null;
    public $timeend = null;   
    public $raw_data = null;
    public $url = null;
    public $url_params = null;
    public $short_url_params = null;
    private $default = array(
        'type' => array('pro','asso','person'),
        'time' => array('start'=>'00:00:00','end'=>'24:00:00'),
        'day_of_week' => array('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'),
        );

    /**
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->sports = json_encode($this->sports);
        $this->type = json_encode($this->type);
        $this->time = json_encode($this->time);
        $this->day_of_week = json_encode($this->day_of_week);
        $this->date_created = new \DateTime();
    }

    /**
     * @ORM\PostLoad
     */
    public function onPostLoad()
    {
        $this->sports = json_decode($this->sports);
        $this->type = json_decode($this->type);
        $this->time = json_decode($this->time);
        $this->day_of_week = json_decode($this->day_of_week);
    }



    public function getDate()
    {
        return $this->date;
    }

    public function hasDate()
    {
        if(isset($this->date)) return true;
        return false;
    }
    public function setDate($date)
    {
        $this->date = $date;
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
        if(isset($this->location)) return true;
        return false;
    }

    public function setLocation($location)
    {
        $this->location = $location;
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

    public function hasSports()
    {
        if(!empty($this->sports)) return true;
        return false;
    }    

    public function setSports($sports)
    {
        $this->sports = $sports;
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

    public function hasType()
    {
        if(isset($this->type) && count(array_diff($this->default['type'],$this->type)) != 0) return true;
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

    public function hasTime()
    {
        if(!empty($this->time) && count(array_diff($this->default['time'],$this->time)) != 0) return true;
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

    public function setTimeStart($t)
    {
        $this->timestart = $t;
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

    public function setTimeEnd($t)
    {
        $this->timeend = $t;
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
