<?php

namespace Ws\EventsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * Invitation Blacklist
 *
 * @ORM\Table(name="events_invitation_blacklist")
 * @ORM\Entity(repositoryClass="Ws\EventsBundle\Entity\InvitationBlacklistRepository")
 */
class InvitationBlacklist
{
/**
     * @ORM\Id @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
    * @ORM\Column(name="email", type="string")
    */
    private $email;

   

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
     * @return InvitationBlacklist
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
}
