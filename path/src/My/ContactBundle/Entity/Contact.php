<?php

namespace My\ContactBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Contact
 *
 * @ORM\Table(name="contact_message")
 * @ORM\Entity(repositoryClass="My\ContactBundle\Entity\ContactRepository")
 */
class Contact {

	/**
     * @ORM\Id @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

	/**
     * @ORM\Column(name="user_id", type="integer")
     * @ORM\ManyToOne(targetEntity="My\UserBundle\Entity\User", fetch="EAGER")
     */
	private $user;

    /**
    * @ORM\Column(name="email", type="string", length=255)
    * @Assert\Email()
    */
    private $email;

	/**
    * @ORM\Column(name="date", type="date")
	* @Assert\Date()
	*/
	private $date;

	/**
    * @ORM\Column(name="title", type="string", length=255, nullable=true)
	* @Assert\NotBlank()
	*/
	private $title;

	/**
    * @ORM\Column(name="message", type="text")
	* @Assert\NotBlank()
	*/
	private $message;

	/**
    * @ORM\Column(name="lang", type="string", length=4, nullable=true)
	*/
	private $lang;



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
     * Set user
     *
     * @param \My\UserBundle\Entity\User $user
     * @return Contact
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
     * Set email
     *
     * @param string $email
     * @return Contact
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
     * @return Contact
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
     * Set title
     *
     * @param string $title
     * @return Contact
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set message
     *
     * @param string $message
     * @return Contact
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string 
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set lang
     *
     * @param string $lang
     * @return Contact
     */
    public function setLang($lang)
    {
        $this->lang = $lang;

        return $this;
    }

    /**
     * Get lang
     *
     * @return string 
     */
    public function getLang()
    {
        return $this->lang;
    }
}
