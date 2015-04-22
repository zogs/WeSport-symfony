<?php

namespace My\UserBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use My\UserBundle\Entity\Avatar;
use My\UserBundle\Entity\Settings;
use Ws\StatisticBundle\Entity\UserStat as Statistic;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="My\UserBundle\Entity\UserRepository")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $facebookId = null;

     /**
     * @ORM\Column(type="string", length=10)
     */
    private $type = 'person';

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $firstname = '';

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $lastname = '';

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $birthday = null;

    /**
     * @ORM\OneToOne(targetEntity="My\UserBundle\Entity\Avatar", fetch="EAGER", cascade={"all"})
     * @ORM\JoinColumn(nullable=true, name="avatar_id", referencedColumnName="id")
     */
    private $avatar = null;

    /**
     * @ORM\OneToOne(targetEntity="My\UserBundle\Entity\Settings", cascade={"persist","remove"})
     */
    private $settings = null;

    /**
     * @ORM\OneToMany(targetEntity="Ws\EventsBundle\Entity\Alert", mappedBy="user", cascade={"persist","remove"})
     */
    private $alerts;

    /**
     * @ORM\OneToMany(targetEntity="Ws\EventsBundle\Entity\Search", mappedBy="organizer", cascade={"remove"})
     */
    private $searched;

    /**
     * @ORM\OneToMany(targetEntity="Ws\EventsBundle\Entity\Event", mappedBy="organizer", cascade={"persist","remove"})
     */
    private $events;

    /**
     * @ORM\OneToMany(targetEntity="Ws\EventsBundle\Entity\Serie", mappedBy="organizer", cascade={"persist","remove"})
     */
    private $series;

    /**
     * @ORM\OneToMany(targetEntity="Ws\EventsBundle\Entity\Invitation", mappedBy="inviter", cascade={"persist","remove"})
     */
    private $invitations;

    /**
     * @ORM\OneToMany(targetEntity="Ws\EventsBundle\Entity\Participation", mappedBy="user", cascade={"persist","remove"})
     */
    private $participations;

    /**
     * @ORM\OneToOne(targetEntity="Ws\StatisticBundle\Entity\UserStat", cascade={"persist","remove"})
     */
    private $statistic = null;

    /**
     * @ORM\Column(type="string", length=1, nullable=true)
     */
    private $gender = '';

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description = '';

    /**
     * @ORM\ManyToOne(targetEntity="My\WorldBundle\Entity\Location", fetch="EAGER", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true, name="location_id")
     */
    private $location = null;

    /**
     * @ORM\Column(type="date")
     */
    private $register_since = '';

    /**
    * @ORM\Column(type="integer", nullable=true)
    */
    private $updatedCount = '';

    /**
     * @ORM\Column(type="text", length=6, nullable=true, name="lang")
     */
    private $lang = '';



    public function __construct()
    {
        parent::__construct();

        $this->settings = new Settings();    
        $this->statistic = new Statistic();
        $this->statistic->setUser($this); 

    }
    /**
     * @ORM\PostLoad
     */
    public function init()
    {

        $this->type = 'person';        

    }

    public function isPerson()
    {
        if($this->type=='person' || empty($this->type)) return true;
        return false;
    }

    public function isAsso()
    {
        if($this->type=='asso') return true;
        return false;
    }

    public function isPro()
    {
        if($this->type=='pro') return true;
        return false;
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreatedAtValue()
    {
        if(empty($this->register_since)) $this->register_since = new \DateTime();
    }


    /**
     * @ORM\PreUpdate
     */
    public function countProfileUpdated()
    {
        $this->updatedCount++;
    }

    /**
     * Set id
     *
     * @param integer $id
     */
    public function setId($id)
    {
        $this->id = $id;

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
     * Set type
     *
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
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
     * Set firstname
     *
     * @param string $firstname
     * @return User
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string 
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     * @return User
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string 
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set birthday
     *
     * @param \DateTime $birthday
     * @return User
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * Get birthday
     *
     * @return \DateTime 
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * Has birthday
     *
     * @return Boolean
     */
    public function hasBirthday()
    {
        return (isset($this->birthday))? true : false;
    }

    /**
     * Set gender
     *
     * @param string $gender
     * @return User
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     *
     * @return string 
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return User
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
     * Set register_since
     *
     * @param \DateTime $registerSince
     * @return User
     */
    public function setRegisterSince($registerSince)
    {
        $this->register_since = $registerSince;

        return $this;
    }

    /**
     * Get register_since
     *
     * @return \DateTime 
     */
    public function getRegisterSince()
    {
        return $this->register_since;
    }

    /**
     * Set lang
     *
     * @param string $lang
     * @return User
     */
    public function setLang($lang)
    {
        $this->lang = $lang;

        return $this;
    }

    /**
     * Get lang
     *
     * @return string 
     */
    public function getLang()
    {
        return $this->lang;
    }

    

    /**
     * Set updatedCount
     *
     * @param integer $updatedCount
     * @return User
     */
    public function setUpdatedCount($updatedCount)
    {
        $this->updatedCount = $updatedCount;

        return $this;
    }

    /**
     * Get updatedCount
     *
     * @return integer 
     */
    public function getUpdatedCount()
    {
        return $this->updatedCount;
    }

    /**
     * Set location
     *
     * @param \My\WorldBundle\Entity\Location $location
     * @return User
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
     * Set avatar
     *
     * @param object $avatar
     * @return User
     */
    public function setAvatar(Avatar $avatar)
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * Get avatar
     *
     * @return object avatar 
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * @ORM\PrePersist
     */
    public function setDefaultAvatar()
    {        
        if(!isset($this->avatar)) $this->avatar = new Avatar();
    }

    /**
     * Get settings
     *
     * @return object settings 
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * Set Settings
     */
    public function setSettings(Settings $settings)
    {        
        $this->settings = $settings;
    }

    /**
     * Get Statistic
     *
     * @return object statistic 
     */
    public function getStatistic()
    {
        return $this->statistic;
    }

    /**
     * Set Statistic
     */
    public function setStatistic(Statistic $statistic)
    {        
        $this->statistic = $statistic;
    }

    /**
     * Set Salt
     */
    public function setSalt($salt)
    {        
        $this->salt = $salt;
    }



    /**
     * Set facebookId
     *
     * @param integer $facebookId
     * @return User
     */
    public function setFacebookId($facebookId)
    {
        $this->facebookId = $facebookId;

        return $this;
    }

    /**
     * Get facebookId
     *
     * @return integer 
     */
    public function getFacebookId()
    {
        return $this->facebookId;
    }

    /**
     * Add alerts
     *
     * @param \Ws\EventsBundle\Entity\Alert $alerts
     * @return User
     */
    public function addAlert(\Ws\EventsBundle\Entity\Alert $alerts)
    {
        $this->alerts[] = $alerts;

        return $this;
    }

    /**
     * Remove alerts
     *
     * @param \Ws\EventsBundle\Entity\Alert $alerts
     */
    public function removeAlert(\Ws\EventsBundle\Entity\Alert $alerts)
    {
        $this->alerts->removeElement($alerts);
    }

    /**
     * Get alerts
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAlerts()
    {
        return $this->alerts;
    }

    /**
     * Add events
     *
     * @param \Ws\EventsBundle\Entity\Event $events
     * @return User
     */
    public function addEvent(\Ws\EventsBundle\Entity\Event $events)
    {
        $this->events[] = $events;

        return $this;
    }

    /**
     * Remove events
     *
     * @param \Ws\EventsBundle\Entity\Event $events
     */
    public function removeEvent(\Ws\EventsBundle\Entity\Event $events)
    {
        $this->events->removeElement($events);
    }

    /**
     * Get events
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * Add series
     *
     * @param \Ws\EventsBundle\Entity\Serie $series
     * @return User
     */
    public function addSeries(\Ws\EventsBundle\Entity\Serie $series)
    {
        $this->series[] = $series;

        return $this;
    }

    /**
     * Remove series
     *
     * @param \Ws\EventsBundle\Entity\Serie $series
     */
    public function removeSeries(\Ws\EventsBundle\Entity\Serie $series)
    {
        $this->series->removeElement($series);
    }

    /**
     * Get series
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSeries()
    {
        return $this->series;
    }

    /**
     * Add invitations
     *
     * @param \Ws\EventsBundle\Entity\Invitation $invitations
     * @return User
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
     * Add participations
     *
     * @param \Ws\EventsBundle\Entity\Participation $participations
     * @return User
     */
    public function addParticipation(\Ws\EventsBundle\Entity\Participation $participations)
    {
        $this->participations[] = $participations;

        return $this;
    }

    /**
     * Remove participations
     *
     * @param \Ws\EventsBundle\Entity\Participation $participations
     */
    public function removeParticipation(\Ws\EventsBundle\Entity\Participation $participations)
    {
        $this->participations->removeElement($participations);
    }

    /**
     * Get participations
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getParticipations()
    {
        return $this->participations;
    }
}
