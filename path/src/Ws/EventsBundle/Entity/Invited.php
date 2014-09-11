<?php

namespace Ws\EventsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Invitation
 *
 * @ORM\Table(name="events_invited")
 * @ORM\Entity(repositoryClass="Ws\EventsBundle\Repository\InvitedRepository")
 */
class Invited
{
/**
     * @ORM\Id @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

     /**
    * @ORM\ManyToOne(targetEntity="Ws\EventsBundle\Entity\Invitation", inversedBy="invited")
    */
    private $invitation;

      /**
    * @ORM\ManyToOne(targetEntity="My\UserBundle\Entity\User")
    */
    private $user = null;

    /**
     * @ORM\Column(name="email", type="string", length=255)     
     */
    private $email;

    /**
    * @ORM\Column(name="date", type="datetime")
    */
    private $date;

    /**
    * @ORM\Column(name="nb_sended", type="integer")
    */
    private $nb_sended = 1;

    /**
     * @ORM\Column(name="response", type="boolean", nullable=true)
     */
    private $response = null;

    /**
    * @ORM\Column(name="date_response", type="datetime", nullable=true)
    */
    private $date_response = null;


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
     * Set date_response
     *
     * @param \DateTime $dateResponse
     * @return Invited
     */
    public function setDateResponse($dateResponse)
    {
        if($dateResponse=='now') $this->date_response = new \DateTime();
        else $this->date_response = $dateResponse;

        return $this;
    }

    /**
     * Get date_response
     *
     * @return \DateTime 
     */
    public function getDateResponse()
    {
        return $this->date_response;
    }

    /**
     * Set invitation
     *
     * @param \Ws\EventsBundle\Entity\Invitation $invitation
     * @return Invited
     */
    public function setInvitation(\Ws\EventsBundle\Entity\Invitation $invitation = null)
    {
        $this->invitation = $invitation;

        return $this;
    }

    /**
     * Get invitation
     *
     * @return \Ws\EventsBundle\Entity\Invitation 
     */
    public function getInvitation()
    {
        return $this->invitation;
    }

    /**
     * Set user
     *
     * @param string $user
     * @return Invited
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return string 
     */
    public function getUser()
    {
        return (isset($this->user)? $this->user : null);
    }

    public function isRegisteredUser()
    {
        return (isset($this->user)? true : false);
    }

    /**
     * Set nb_sended
     *
     * @param integer $nbSended
     * @return Invited
     */
    public function setNbSended($nbSended)
    {
        $this->nb_sended = $nbSended;

        return $this;
    }

    /**
     * Get nb_sended
     *
     * @return integer 
     */
    public function getNbSended()
    {
        return $this->nb_sended;
    }
}
