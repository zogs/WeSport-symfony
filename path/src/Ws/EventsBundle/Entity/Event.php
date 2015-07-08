<?php

namespace Ws\EventsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContext;

use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Event
 *
 * @ORM\Table(name="events_event")
 * @ORM\Entity(repositoryClass="Ws\EventsBundle\Repository\EventRepository")
* @ORM\HasLifecycleCallbacks()
 */
class Event 
{
	/**
     * @ORM\Id @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="My\UserBundle\Entity\User", inversedBy="events", fetch="EAGER")
     */
    private $organizer;

    /**
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    private $title = null;

    /**
     * @ORM\Column(length=255, unique=true)
     * @Gedmo\Slug(updatable=true, unique=true, fields={"title"})
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
    * @ORM\Column(name="public", type="boolean")
    */
    private $public = true;

    /**
     * @ORM\Column(name="type", type="string")
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity="Ws\EventsBundle\Entity\Spot", fetch="EAGER", cascade={"persist"})     
     */
    private $spot;

    /**
     * @ORM\ManyToOne(targetEntity="My\WorldBundle\Entity\Location", fetch="EAGER")     
     */
    private $location;

    /**
     * @ORM\ManyToOne(targetEntity="Ws\EventsBundle\Entity\Serie", inversedBy="events", fetch="EAGER", cascade={"persist"}) 
     * @ORM\JoinColumn(name="serie", nullable=true)    
     */
    private $serie = null;

    /**
     * @ORM\ManyToOne(targetEntity="Ws\SportsBundle\Entity\Sport", fetch="EAGER")
     */
    private $sport;

    /**
     * @ORM\OneToMany(targetEntity="Ws\EventsBundle\Entity\Participation", mappedBy="event", cascade={"remove"}, orphanRemoval=true)
     */
    private $participations;

     /**
     * @ORM\OneToMany(targetEntity="Ws\EventsBundle\Entity\Invitation", mappedBy="event", cascade={"persist","remove"})
     */
    private $invitations;

    /**
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(name="nbmin", type="smallint", nullable=true)
     * @Assert\GreaterThanOrEqual(
     *                  value = "2"
     * )
     */
    private $nbmin = null;

    /**
     * @ORM\Column(name="nbmax", type="smallint", nullable=true)
     */
    private $nbmax;

    /**
     * @ORM\Column(name="price", type="smallint")
     */
    private $price = 0;

    /**
     * @ORM\Column(name="level", type="string", nullable=true)
     */
    private $level = null;

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

    public $changes_made;

    static public $valuesAvailable = array(
        'level' => array(
            'beginner'=>'beginner',
            'confirmed'=>'confirmed',
            'expert'=>'expert',
            ),
        'type' => array(
            'person'=>'person',
            'asso'=>'asso',
            'pro'=>'pro',
            ),
        );

    public function __construct(){

        $this->date_depot = new \DateTime();
        $this->participations = new ArrayCollection();
        $this->type = 'person';
    }


    /**
     * Get Timing
     */
    public function getTiming()
    {
        if($this->date < date('Y-m-d') ) return 'past';
        if($this->date > date('Y-m-d') ) return 'tocome';
        if($this->date == date('Y-m-d') && $this->time >= date('H:i:s')) return 'tocome';
        if($this->date == date('Y-m-d') && $this->time < date('H:i:s')) return 'past';
        if($this->date == date('Y-m-d') && $this->time == date('H:i:s')) return 'current';
    }

    public function isAdmin(\My\UserBundle\Entity\User $user)
    {
        if($this->organizer == $user) return true;
        return false;
    }

    public function setChanges($changes)
    {
        $this->changes_made = $changes;
    }
    public function getChanges()
    {
        return $this->changes_made;
    }

    /**
     * Set id
     *
     * @param integer 
     */
    public function setId($id)
    {
        $this->id = $id;
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
        if(!empty($this->title)) return $this->title;
        if(isset($this->sport)) return $this->sport->getName();
        return null;
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
     * Set level
     *
     * @param \string $level
     * @return Event
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level
     *
     * @return \integer 
     */
    public function getLevel()
    {
        return $this->level;
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
     * Set nbmin
     *
     * @param integer $nbmin
     * @return Event
     */
    public function setNbmin($nbmin)
    {  

        $this->nbmin = ($nbmin >= 0)? $nbmin : 0;

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
     * Set spot
     *
     * @param \Ws\EventsBundle\Entity\Spot $spot
     * @return Event
     */
    public function setSpot(\Ws\EventsBundle\Entity\Spot $spot = null)
    {
        $this->spot = $spot;

        return $this;
    }

    /**
     * Get spot
     *
     * @return \Ws\EventsBundle\Entity\Spot 
     */
    public function getSpot()
    {
        return $this->spot;
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
     * add participation
     */
    public function addParticipation(\Ws\EventsBundle\Entity\Participation $participation)
    {
        $this->participations[] = $participation;
        return $this;
    }

    /**
     * remove participation
     */
    public function removeParticipation(\Ws\EventsBundle\Entity\Participation $participation)
    {
        $this->participations->removeElement($participation);
    }

    /**
     * count participation
     */
    public function countParticipation()
    {
        return count($this->participations);
    }

    public function removeOrganizerParticipation()
    {
        foreach($this->getParticipations() as $p){
            if($p->getUser() === $this->getOrganizer()) $this->removeParticipation($p);
        }
    }

    /**
     * get participations
     */
    public function getParticipations($withOrganizer = true)
    {
        if(false == $withOrganizer) $this->removeOrganizerParticipation();
        return $this->participations;
    }

    public function isUserParticipate($user)
    {
        foreach ($this->participations as $participation) {
            
            if($participation->getUser() === $user) return true;
        }
        return false;
    }


    /**
     * Set type
     *
     * @param string $type
     * @return Event
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return self::$valuesAvailable['type'][$this->type];
    }

    /**
     * Get type number
     *
     * @return string 
     */
    public function getTypeNumber()
    {
        return $this->type;
    }

    /**
     * Add invitations
     *
     * @param \Ws\EventsBundle\Entity\Invitation $invitations
     * @return Event
     */
    public function addInvitation(\Ws\EventsBundle\Entity\Invitation $invitations)
    {
        $this->invitations[] = $invitations;

        return $this;
    }

    /**
     * Remove invitations
     *
     * @param \Ws\EventsBundle\Entity\Invitation $invitations
     */
    public function removeInvitation(\Ws\EventsBundle\Entity\Invitation $invitations)
    {
        $this->invitations->removeElement($invitations);
    }

    /**
     * Get invitations
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getInvitations()
    {
        return $this->invitations;
    }

    /**
     * Set invitations
     *
     * @return Event
     */
    public function setInvitations(\Ws\EventsBundle\Entity\Invitation $invitations)
    {
        $this->invitations[] = $invitations;

        return $this;
    }

    /**
     * Set price
     *
     * @param integer $price
     * @return Event
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return integer 
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set public
     *
     * @param boolean $public
     * @return Event
     */
    public function setPublic($public)
    {
        $this->public = $public;

        return $this;
    }

    /**
     * Get public
     *
     * @return boolean 
     */
    public function getPublic()
    {
        return $this->public;
    }

    /**
     * Is public
     *
     * @return boolean 
     */
    public function isPublic()
    {
        return ($this->public == 1)? true: false;
    }
}
