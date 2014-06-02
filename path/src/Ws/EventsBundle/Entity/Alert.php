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
 * @ORM\Entity(repositoryClass="Ws\EventsBundle\Entity\AlertRepository")
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
    */
    private $user;

    /**
    * @ORM\JoinColumn(name="search_id", referencedColumnName="id")
    * @ORM\OneToOne(targetEntity="Ws\EventsBundle\Entity\Search", cascade={"persist", "merge", "remove"})
    */
    private $search;

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
    * @ORM\Column(name="email", type="boolean")
    */
    private $email = true;

    /**
    * @ORM\Column(name="active", type="boolean")
    */
    private $active = true;

    /**
    * @ORM\Column(name="date_created", type="datetime")
    */
    private $date_created;

    function __construct(){
        $this->date_created = new \DateTime();
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
        return $this->email;
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
    public function setDateStart($dateStart)
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
    public function setDateStop($dateStop)
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
}
