<?php

namespace Ws\EventsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Alerted
 *
 * @ORM\Table(name="events_alert_sended")
 * @ORM\Entity(repositoryClass="Ws\EventsBundle\Repository\AlertRepository")
 */
class Alerted
{
/**
     * @ORM\Id @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

     /**
    * @ORM\ManyToOne(targetEntity="Ws\EventsBundle\Entity\Alert")
    */
    private $alert;

      /**
    * @ORM\ManyToOne(targetEntity="My\UserBundle\Entity\User")
    */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Ws\EventsBundle\Entity\Event")
     */
    private $event;

    /**
    * @ORM\Column(name="date", type="datetime")
    */
    private $date;


    function __construct(){
        $this->date = new \DateTime();
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
     * Set date
     *
     * @param \DateTime $date
     * @return Alerted
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
     * Set alert
     *
     * @param \Ws\EventsBundle\Entity\Alert $alert
     * @return Alerted
     */
    public function setAlert(\Ws\EventsBundle\Entity\Alert $alert = null)
    {
        $this->alert = $alert;

        return $this;
    }

    /**
     * Get alert
     *
     * @return \Ws\EventsBundle\Entity\Alert 
     */
    public function getAlert()
    {
        return $this->alert;
    }

    /**
     * Set user
     *
     * @param \My\UserBundle\Entity\User $user
     * @return Alerted
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
     * Set event
     *
     * @param \Ws\EventsBundle\Entity\Event $event
     * @return Alerted
     */
    public function setEvent(\Ws\EventsBundle\Entity\Event $event = null)
    {
        $this->event = $event;

        return $this;
    }

    /**
     * Get event
     *
     * @return \Ws\EventsBundle\Entity\Event 
     */
    public function getEvent()
    {
        return $this->event;
    }
}
