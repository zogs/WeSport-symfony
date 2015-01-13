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
 * @ORM\Entity(repositoryClass="Ws\EventsBundle\Repository\InvitationRepository")
 */
class Invitation
{
/**
     * @ORM\Id @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

     /**
    * @ORM\ManyToOne(targetEntity="Ws\EventsBundle\Entity\Event", inversedBy="invitations")
    */
    private $event;

     /**
    * @ORM\ManyToOne(targetEntity="My\UserBundle\Entity\User", inversedBy="invitations")
    */
    private $inviter;

     /**
    * @ORM\Column(name="name", type="string", nullable=true)
    */
    private $name;

     /**
    * @ORM\OneToMany(targetEntity="Ws\EventsBundle\Entity\Invited", mappedBy="invitation", cascade={"persist","remove"})
    */
    private $invited;

    /**
    * @ORM\Column(name="date", type="datetime")
    */
    private $date;

    /**
    * @ORM\Column(name="content", type="string", nullable=true)
    */
    private $content = null;
    
    private $emails = null;
    



    function __construct(){
        $this->date = new \DateTime();
    }


    public function isEmpty()
    {
        if(empty($this->invited)) return true;
        return false;
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
     * Set name
     *
     * @param string $name
     * @return Invitation
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
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

    /**
     * Set inviter
     *
     * @param \My\UserBundle\Entity\User $inviter
     * @return Invitation
     */
    public function setInviter(\My\UserBundle\Entity\User $inviter = null)
    {
        $this->inviter = $inviter;

        return $this;
    }

    /**
     * Get inviter
     *
     * @return \My\UserBundle\Entity\User 
     */
    public function getInviter()
    {
        return $this->inviter;
    }

    /**
     * Add invited
     *
     * @param \Ws\EventsBundle\Entity\Invited $invited
     * @return Invitation
     */
    public function addInvited(\Ws\EventsBundle\Entity\Invited $invited)
    {
        $this->invited[] = $invited;

        return $this;
    }

    /**
     * Remove invited
     *
     * @param \Ws\EventsBundle\Entity\Invited $invited
     */
    public function removeInvited(\Ws\EventsBundle\Entity\Invited $invited)
    {
        $this->invited->removeElement($invited);
    }

    /**
     * Get invited
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getInvited()
    {
        return $this->invited;
    }

    /**
     * Has Invited
     *
     * @return boolean
     */
    public function hasInvited()
    {
        return !$this->isEmpty();
    }



    /**
     * Set invited
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function setInvited(\Doctrine\Common\Collections\Collection $invited)
    {
        return $this->invited = $invited;
    }

    /**
     * Set emails
     *
     * @param \String $emails
     * @return Invitation
     */
    public function setEmails($emails)
    {
        $this->emails = $emails;

        return $this;
    }

    /**
     * Get emails
     *
     * @return \String
     */
    public function getEmails()
    {
        return $this->emails;
    }

    /**
     * Set content
     *
     * @param \String $content
     * @return Invitation
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return \String 
     */
    public function getContent()
    {
        return $this->content;
    }
}
