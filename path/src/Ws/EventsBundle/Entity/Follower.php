<?php

namespace Ws\EventsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Follow Organizer
 *
 * @ORM\Table(name="users_followed")
 * @ORM\Entity
 */
class Follower 
{
    /**
     * @ORM\Id @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
    * @ORM\ManyToOne(targetEntity="My\UserBundle\Entity\User")
    * @ORM\JoinColumn(name="organizer_id", referencedColumnName="id", onDelete="CASCADE")
    */
    private $organizer;

    /**
    * @ORM\ManyToOne(targetEntity="My\UserBundle\Entity\User")
    * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
    */
    private $user;

     /**
    * @ORM\OneToOne(targetEntity="Ws\EventsBundle\Entity\Alert")    
    * @ORM\JoinColumn(name="alert_id")
    */
    private $alert;

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
     * @return Follower
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
     * Set organizer
     *
     * @param \My\UserBundle\Entity\User $organizer
     * @return Follower
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
     * Set user
     *
     * @param \My\UserBundle\Entity\User $user
     * @return Follower
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
     * Set alert
     *
     * @param \Ws\EventsBundle\Entity\Alert $alert
     * @return Follower
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
}
