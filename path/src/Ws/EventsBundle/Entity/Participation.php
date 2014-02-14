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
 * @ORM\Entity(repositoryClass="Ws\EventsBundle\Entity\ParticipationRepository")
 */
class Participation 
{
	/**
	* @ORM\Id
	* @ORM\ManyToOne(targetEntity="Ws\EventsBundle\Entity\Event")
	*/
	private $event;

	/**
	* @ORM\Id
	* @ORM\ManyToOne(targetEntity="My\UserBundle\Entity\User")
	*/
	private $user;

	/**
     * @ORM\Column(name="date", type="datetime")
     */
    private $date_inscription;


	function __construct(){

        $this->date_inscription = new \DateTime();
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
    public function setUser(\My\UserBundle\Entity\User $user)
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
}
