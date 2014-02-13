<?php

namespace Ws\EventsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContext;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Category
 *
 * @ORM\Table(name="events_event")
 * @ORM\Entity(repositoryClass="Ws\EventsBundle\Entity\EventRepository")
 */
class Event 
{
	/**
     * @ORM\Id @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="My\UserBundle\Entity\User", fetch="EAGER")
     */
    private $organizer;

    /**
     * @ORM\Column(name="title", type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(
     *              min = "5",
     *              max = "150")
     */
    private $title;

    /**
     * @Gedmo\Slug(fields={"title"})
     * @ORM\Column(length=255, unique=true)
     */
    private $slug;

    /**
     * @ORM\Column(name="date", type="date", nullable=true)
     * @Assert\Date()
     */
    private $date;

    /**
     * @ORM\Column(name="time", type="time")
     * @Assert\Time()
     */
    private $time;

    /**
     * @ORM\ManyToOne(targetEntity="My\WorldBundle\Entity\Location", fetch="EAGER")     
     */
    private $location;

    /**
     * @ORM\ManyToOne(targetEntity="Ws\EventsBundle\Entity\Serie", fetch="EAGER", cascade={"persist"})     
     */
    private $serie;

    /**
     * @ORM\ManyToOne(targetEntity="Ws\SportsBundle\Entity\Sport", fetch="EAGER")
     */
    private $sport;

    /**
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(name="address", type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     */
    private $address;

    /**
     * @ORM\Column(name="nbmin", type="smallint")
     * @Assert\GreaterThanOrEqual(
     *                  value = "2"
     * )
     */
    private $nbmin = 2;

    /**
     * @ORM\Column(name="nbmax", type="smallint", nullable=true)
     */
    private $nbmax;

    /**
     * @ORM\Column(name="phone", type="string", length=20, nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(name="date_depot", type="datetime", nullable=true)
     */
    private $date_depot;

    /**
     * @ORM\Column(name="occurence", type="smallint")
     */
    private $occurence = 1;

    /**
     * @ORM\Column(name="confirmed", type="boolean")
     */
    private $confirmed = 0;

    /**
     * @ORM\Column(name="online", type="boolean")
     */
    private $online = 1;

    function __construct(){

        $this->date_depot = new \DateTime();
    }

    
    /**
    * @Assert\True(message = "La date doit être dans le futur")
    */
    public function isFutur()
    {        
        if(empty($this->date)) return true;
        if($this->date > new \DateTime()) return true;
        return false;
    }
    

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Event
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Event
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Event
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set time
     *
     * @param \DateTime $time
     * @return Event
     */
    public function setTime($time)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * Get time
     *
     * @return \DateTime 
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Set city_id
     *
     * @param integer $cityId
     * @return Event
     */
    public function setCityId($cityId)
    {
        $this->city_id = $cityId;

        return $this;
    }

    /**
     * Get city_id
     *
     * @return integer 
     */
    public function getCityId()
    {
        return $this->city_id;
    }

    /**
     * Set city_name
     *
     * @param string $cityName
     * @return Event
     */
    public function setCityName($cityName)
    {
        $this->city_name = $cityName;

        return $this;
    }

    /**
     * Get city_name
     *
     * @return string 
     */
    public function getCityName()
    {
        return $this->city_name;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Event
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set address
     *
     * @param string $address
     * @return Event
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string 
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set nbmin
     *
     * @param integer $nbmin
     * @return Event
     */
    public function setNbmin($nbmin)
    {
        $this->nbmin = $nbmin;

        return $this;
    }

    /**
     * Get nbmin
     *
     * @return integer 
     */
    public function getNbmin()
    {
        return $this->nbmin;
    }

    /**
     * Set nbmax
     *
     * @param integer $nbmax
     * @return Event
     */
    public function setNbmax($nbmax)
    {
        $this->nbmax = $nbmax;

        return $this;
    }

    /**
     * Get nbmax
     *
     * @return integer 
     */
    public function getNbmax()
    {
        return $this->nbmax;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return Event
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string 
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set date_depot
     *
     * @param \DateTime $dateDepot
     * @return Event
     */
    public function setDateDepot($dateDepot)
    {
        $this->date_depot = $dateDepot;

        return $this;
    }

    /**
     * Get date_depot
     *
     * @return \DateTime 
     */
    public function getDateDepot()
    {
        return $this->date_depot;
    }

    /**
     * Set confirmed
     *
     * @param boolean $confirmed
     * @return Event
     */
    public function setConfirmed($confirmed)
    {
        $this->confirmed = $confirmed;

        return $this;
    }

    /**
     * Get confirmed
     *
     * @return boolean 
     */
    public function getConfirmed()
    {
        return $this->confirmed;
    }

    /**
     * Set occurence
     *
     * @param smallint $occurence
     * @return Event
     */
    public function setOccurence($occurence)
    {
        $this->occurence = $occurence;

        return $this;
    }

    /**
     * Get occurence
     *
     * @return smallint 
     */
    public function getOccurence()
    {
        return $this->occurence;
    }

    /**
     * Set online
     *
     * @param boolean $online
     * @return Event
     */
    public function setOnline($online)
    {
        $this->online = $online;

        return $this;
    }

    /**
     * Get online
     *
     * @return boolean 
     */
    public function getOnline()
    {
        return $this->online;
    }

    /**
     * Set serie
     *
     * @param \Ws\EventsBundle\Entity\Serie $serie
     * @return Event
     */
    public function setSerie(\Ws\EventsBundle\Entity\Serie $serie = null)
    {
        $this->serie = $serie;

        return $this;
    }

    /**
     * Get serie
     *
     * @return \Ws\EventsBundle\Entity\Serie 
     */
    public function getSerie()
    {
        return $this->serie;
    }

    /**
     * Set sport
     *
     * @param \Ws\SportsBundle\Entity\Sport $sport
     * @return Event
     */
    public function setSport(\Ws\SportsBundle\Entity\Sport $sport = null)
    {
        $this->sport = $sport;

        return $this;
    }

    /**
     * Get sport
     *
     * @return \Ws\SportsBundle\Entity\Sport 
     */
    public function getSport()
    {
        return $this->sport;
    }

    /**
     * Set organizer
     *
     * @param \My\UserBundle\Entity\User $organizer
     * @return Event
     */
    public function setOrganizer(\My\UserBundle\Entity\User $organizer = null)
    {
        $this->organizer = $organizer;

        return $this;
    }

    /**
     * Get organizer
     *
     * @return \My\UserBundle\Entity\User 
     */
    public function getOrganizer()
    {
        return $this->organizer;
    }

    /**
     * Set location
     *
     * @param \My\WorldBundle\Entity\Location $location
     * @return Event
     */
    public function setLocation(\My\WorldBundle\Entity\Location $location = null)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return \My\WorldBundle\Entity\Location 
     */
    public function getLocation()
    {
        return $this->location;
    }
}
