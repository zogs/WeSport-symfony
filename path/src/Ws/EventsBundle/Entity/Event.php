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
 * @ORM\Entity(repositoryClass="Ws\EventsBundle\Repository\EventRepository")
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
     * @ORM\Column(name="type", type="integer")
     */
    private $type = 0;

    /**
     * @ORM\ManyToOne(targetEntity="Ws\EventsBundle\Entity\Spot", fetch="EAGER", cascade={"persist","remove"})     
     */
    private $spot;

    /**
     * @ORM\ManyToOne(targetEntity="Ws\EventsBundle\Entity\Serie", inversedBy="events", fetch="EAGER", cascade={"persist"})     
     */
    private $serie;

    /**
     * @ORM\ManyToOne(targetEntity="Ws\SportsBundle\Entity\Sport", fetch="EAGER")
     */
    private $sport;

    /**
     * @ORM\OneToMany(targetEntity="Ws\EventsBundle\Entity\Participation", mappedBy="event")
     */
    private $participations;

     /**
     * @ORM\OneToMany(targetEntity="Ws\EventsBundle\Entity\Invitation", mappedBy="event", cascade="persist")
     */
    private $invitations;

    /**
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

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
     * @ORM\Column(name="price", type="smallint")
     */
    private $price = 0;

    /**
     * @ORM\Column(name="level", type="integer")
     */
    private $level = 0;

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
        'level' => array('all','beginner','average','confirmed','expert'),
        'type' => array('person','asso','pro'),
        );

    function __construct(){

        $this->date_depot = new \DateTime();
        $this->participations = new ArrayCollection();
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
        $this->change_made = $changes;
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
            
            if($participation->getUser() == $user) return true;
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
}
