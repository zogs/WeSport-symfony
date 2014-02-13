<?php

namespace Ws\EventsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Serie
 *
 * @ORM\Table(name="events_serie")
 * @ORM\Entity(repositoryClass="Ws\EventsBundle\Entity\SerieRepository")
 */
class Serie 
{
	/**
     * @ORM\Id @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="occurences", type="smallint")
     */
    private $occurences = 1;

    /**
     * @ORM\Column(name="date_start", type="date", nullable=true)
     */
    private $startDate;

    /**
     * @ORM\Column(name="date_end", type="date", nullable=true)
     */
    private $endDate;

    /**
     * @ORM\Column(name="monday", type="boolean")
     */
    private $monday = 0;

    /**
     * @ORM\Column(name="tuesday", type="boolean")
     */
    private $tuesday = 0;

    /**
     * @ORM\Column(name="wednesday", type="boolean")
     */
    private $wednesday = 0;

    /**
     * @ORM\Column(name="thursday", type="boolean")
     */
    private $thursday = 0;

    /**
     * @ORM\Column(name="friday", type="boolean")
     */
    private $friday = 0;

    /**
     * @ORM\Column(name="saturday", type="boolean")
     */
    private $saturday = 0;

    /**
     * @ORM\Column(name="sunday", type="boolean")
     */
    private $sunday = 0;

    

    public function __construct()
    {
        $this->events = new ArrayCollection();

        $this->startDate = new \DateTime('0000-00-00 00:00:00');
        $this->endDate = new \DateTime('0000-00-00 00:00:00');
    }

    public function isWeekdayInSerie($weekday)
    {
        if(call_user_method('get'.ucfirst($weekday),$this)) return true;
        return false;
    }

    public function addEvent(\Ws\EventsBundle\Entity\Event $event)
    {
        $this->events[] = $event;
        $event->setSerie($this);
        return $this;
    }

    public function removeEvent(\Ws\EventsBundle\Entity\Event $event)
    {
        $this->events->removeElement($event);
        
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
     * Set nboccurence
     *
     * @param integer $nboccurence
     * @return Serie
     */
    public function setNboccurence($nboccurence)
    {
        $this->nboccurence = $nboccurence;

        return $this;
    }

    /**
     * Get nboccurence
     *
     * @return integer 
     */
    public function getNboccurence()
    {
        return $this->nboccurence;
    }

    /**
     * Set occurences
     *
     * @param integer $occurences
     * @return Serie
     */
    public function setOccurences($occurences)
    {
        $this->occurences = $occurences;

        return $this;
    }

    /**
     * Get occurences
     *
     * @return integer 
     */
    public function getOccurences()
    {
        return $this->occurences;
    }

    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     * @return Serie
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime 
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     * @return Serie
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime 
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set monday
     *
     * @param boolean $monday
     * @return Serie
     */
    public function setMonday($monday)
    {
        $this->monday = $monday;

        return $this;
    }

    /**
     * Get monday
     *
     * @return boolean 
     */
    public function getMonday()
    {
        return $this->monday;
    }

    /**
     * Set tuesday
     *
     * @param boolean $tuesday
     * @return Serie
     */
    public function setTuesday($tuesday)
    {
        $this->tuesday = $tuesday;

        return $this;
    }

    /**
     * Get tuesday
     *
     * @return boolean 
     */
    public function getTuesday()
    {
        return $this->tuesday;
    }

    /**
     * Set wednesday
     *
     * @param boolean $wednesday
     * @return Serie
     */
    public function setWednesday($wednesday)
    {
        $this->wednesday = $wednesday;

        return $this;
    }

    /**
     * Get wednesday
     *
     * @return boolean 
     */
    public function getWednesday()
    {
        return $this->wednesday;
    }

    /**
     * Set thursday
     *
     * @param boolean $thursday
     * @return Serie
     */
    public function setThursday($thursday)
    {
        $this->thursday = $thursday;

        return $this;
    }

    /**
     * Get thursday
     *
     * @return boolean 
     */
    public function getThursday()
    {
        return $this->thursday;
    }

    /**
     * Set friday
     *
     * @param boolean $friday
     * @return Serie
     */
    public function setFriday($friday)
    {
        $this->friday = $friday;

        return $this;
    }

    /**
     * Get friday
     *
     * @return boolean 
     */
    public function getFriday()
    {
        return $this->friday;
    }

    /**
     * Set saturday
     *
     * @param boolean $saturday
     * @return Serie
     */
    public function setSaturday($saturday)
    {
        $this->saturday = $saturday;

        return $this;
    }

    /**
     * Get saturday
     *
     * @return boolean 
     */
    public function getSaturday()
    {
        return $this->saturday;
    }

    /**
     * Set sunday
     *
     * @param boolean $sunday
     * @return Serie
     */
    public function setSunday($sunday)
    {
        $this->sunday = $sunday;

        return $this;
    }

    /**
     * Get sunday
     *
     * @return boolean 
     */
    public function getSunday()
    {
        return $this->sunday;
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
}
