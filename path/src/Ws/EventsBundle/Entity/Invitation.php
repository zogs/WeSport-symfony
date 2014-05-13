<?php

namespace Ws\EventsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Invitation
 *
 * @ORM\Table(name="events_invitation")
 * @ORM\Entity(repositoryClass="Ws\EventsBundle\Entity\InvitationRepository")
 */
class Invitation
{
/**
     * @ORM\Id @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

     /**
    * @ORM\ManyToOne(targetEntity="Ws\EventsBundle\Entity\Event")
    */
    private $event;

     /**
    * @ORM\ManyToOne(targetEntity="My\UserBundle\Entity\User")
    */
    private $inviter;

     /**
    * @ORM\Column(name="name", type="string")
    */
    private $name;

     /**
    * @ORM\OneToMany(targetEntity="Ws\EventsBundle\Entity\Invited", mappedBy="invitation")
    */
    private $invited;

    /**
    * @ORM\Column(name="date", type="datetime")
    */
    private $date;

    private $content;


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
     * Set email
     *
     * @param string $email
     * @return Invitation
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Invitation
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
     * Set response
     *
     * @param string $response
     * @return Invitation
     */
    public function setResponse($response)
    {
        $this->response = $response;

        return $this;
    }

    /**
     * Get response
     *
     * @return string 
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Set event
     *
     * @param \Ws\EventsBundle\Entity\Event $event
     * @return Invitation
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
