<?php

namespace Ws\EventsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * EventParticipants
 *
 * @ORM\Table(name="events_participants")
 * @ORM\Entity(repositoryClass="Ws\EventsBundle\Repository\ParticipationRepository")
 */
class Participation 
{
    /**
     * @ORM\Id @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
    * @ORM\ManyToOne(targetEntity="Ws\EventsBundle\Entity\Event", inversedBy="participations")
    * @ORM\JoinColumn(name="event_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
    */
    private $event;

    /**
    * @ORM\ManyToOne(targetEntity="My\UserBundle\Entity\User")
    */
    private $user = null;

     /**
    * @ORM\OneToOne(targetEntity="Ws\EventsBundle\Entity\Invited")    
    */
    private $invited = null;

    /**
    * @ORM\Column(name="date", type="datetime")
    */
    private $date_inscription;


    function __construct(){
        $this->date_inscription = new \DateTime();
    }

    /**
     * Get id
     *
     * @return id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id
     *
     * @param id
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set date_inscription
     *
     * @param \DateTime $dateInscription
     * @return Participation
     */
    public function setDateInscription($dateInscription)
    {
        $this->date_inscription = $dateInscription;

        return $this;
    }

    /**
     * Get date_inscription
     *
     * @return \DateTime 
     */
    public function getDateInscription()
    {
        return $this->date_inscription;
    }

    /**
     * Set event
     *
     * @param \Ws\EventsBundle\Entity\Event $event
     * @return Participation
     */
    public function setEvent(\Ws\EventsBundle\Entity\Event $event)
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

    /**
     * Set user
     *
     * @param \My\UserBundle\Entity\User $user
     * @return Participation
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
     * Set invited
     *
     * @param \Ws\EventsBundle\Entity\Invited $invited
     * @return Participation
     */
    public function setInvited(\Ws\EventsBundle\Entity\Invited $invited = null)
    {
        $this->invited = $invited;

        return $this;
    }

    /**
     * Get invited
     *
     * @return \Ws\EventsBundle\Entity\Invited 
     */
    public function getInvited()
    {
        return $this->invited;
    }
}
