<?php

namespace Ws\EventsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

use Ws\EventsBundle\Entity\Search;
/**
 * Alert
 *
 * @ORM\Table(name="events_alert")
 * @ORM\Entity(repositoryClass="Ws\EventsBundle\Repository\AlertRepository")
 */
class Alert
{
/**
     * @ORM\Id @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
    * @ORM\ManyToOne(targetEntity="My\UserBundle\Entity\User", inversedBy="alerts")
    * @ORM\JoinColumn(name="user_id", nullable=false)
    */
    private $user;

     /**
    * @ORM\Column(name="email", type="string")
    */
    private $email;

    /**
    * @ORM\OneToOne(targetEntity="Ws\EventsBundle\Entity\Search", mappedBy="alert", cascade={"persist", "remove"}, fetch="EAGER")
    * @ORM\JoinColumn(name="search_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
    */
    private $search;

    /**
    * @ORM\ManyToOne(targetEntity="Ws\EventsBundle\Entity\Alerted", inversedBy="alert")
    */
    private $sended;

    /**
    * @ORM\Column(name="frequency", type="string")
    */
    private $frequency;

    /**
    * @ORM\Column(name="duration", type="integer")
    */
    private $duration = 3;

    /**
    * @ORM\Column(name="date_start", type="datetime")
    */
    private $date_start;

    /**
    * @ORM\Column(name="date_stop", type="datetime")
    */
    private $date_stop;

    /**
    * @ORM\Column(name="nb_emails", type="integer")
    */
    private $nb_emails = 0;

     /**
    * @ORM\Column(name="nb_events", type="integer")
    */
    private $nb_events = 0;

    /**
    * @ORM\Column(name="active", type="boolean")
    */
    private $active = true;

    /**
    * @ORM\Column(name="date_created", type="datetime")
    */
    private $date_created;

    function __construct(){
        $this->date_created = new \DateTime('now');
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
     * Set search
     *
     * @param string $search
     * @return Alert
     */
    public function setSearch(Search $search)
    {
        $this->search = $search;

        return $this;
    }

    /**
     * Get search
     *
     * @return string 
     */
    public function getSearch()
    {
        return $this->search;
    }

    /**
     * Set frequency
     *
     * @param integer $frequency
     * @return Alert
     */
    public function setFrequency($frequency)
    {
        $this->frequency = $frequency;

        return $this;
    }

    /**
     * Get frequency
     *
     * @return integer 
     */
    public function getFrequency()
    {
        return $this->frequency;
    }

    /**
     * Set email
     *
     * @param boolean $email
     * @return Alert
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return boolean 
     */
    public function getEmail()
    {
        if(null!= $this->email) return $this->email;
        elseif(null!=$this->getUser()) return $this->getUser()->getEmail();
    }

    /**
     * Set active
     *
     * @param boolean $active
     * @return Alert
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean 
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Alert
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
     * Set user
     *
     * @param \My\UserBundle\Entity\User $user
     * @return Alert
     */
    public function setUser(\My\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \My\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set date_start
     *
     * @param \DateTime $dateStart
     * @return Alert
     */
    public function setDateStart(\DateTime $dateStart)
    {
        $this->date_start = $dateStart;

        return $this;
    }

    /**
     * Get date_start
     *
     * @return \DateTime 
     */
    public function getDateStart()
    {
        return $this->date_start;
    }

    /**
     * Set date_stop
     *
     * @param \DateTime $dateStop
     * @return Alert
     */
    public function setDateStop(\DateTime $dateStop)
    {
        $this->date_stop = $dateStop;

        return $this;
    }

    /**
     * Get date_stop
     *
     * @return \DateTime 
     */
    public function getDateStop()
    {
        return $this->date_stop;
    }

    /**
     * Set date_created
     *
     * @param \DateTime $dateCreated
     * @return Alert
     */
    public function setDateCreated($dateCreated)
    {
        $this->date_created = $dateCreated;

        return $this;
    }

    /**
     * Get date_created
     *
     * @return \DateTime 
     */
    public function getDateCreated()
    {
        return $this->date_created;
    }

    /**
     * Set duration
     *
     * @param integer $duration
     * @return Alert
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * Get duration
     *
     * @return integer 
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Set nb_emails
     *
     * @param integer $nbEmails
     * @return Alert
     */
    public function setNbEmails($nbEmails)
    {
        $this->nb_emails = $nbEmails;

        return $this;
    }

    /**
     * Get nb_emails
     *
     * @return integer 
     */
    public function getNbEmails()
    {
        return $this->nb_emails;
    }

    /**
     * Set nb_events
     *
     * @param integer $nbEvents
     * @return Alert
     */
    public function setNbEvents($nbEvents)
    {
        $this->nb_events = $nbEvents;

        return $this;
    }

    /**
     * Get nb_events
     *
     * @return integer 
     */
    public function getNbEvents()
    {
        return $this->nb_events;
    }

    /**
     * Set sended
     *
     * @param \Ws\EventsBundle\Entity\Alerted $sended
     * @return Alert
     */
    public function setSended(\Ws\EventsBundle\Entity\Alerted $sended = null)
    {
        $this->sended = $sended;

        return $this;
    }

    /**
     * Get sended
     *
     * @return \Ws\EventsBundle\Entity\Alerted 
     */
    public function getSended()
    {
        return $this->sended;
    }
}
