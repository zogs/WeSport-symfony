<?php

// src/Acme/UserBundle/Entity/User.php

namespace My\UserBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use My\UserBundle\Entity\Avatar;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="users")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

     /**
     * @ORM\Column(type="string", length=10)
     */
    private $type = 'person';

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $firstname = '';

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $lastname = '';

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $birthday = null;

    /**
     * @ORM\OneToOne(targetEntity="My\UserBundle\Entity\Avatar", fetch="EAGER", cascade={"all"})
     * @ORM\JoinColumn(nullable=true, name="avatar_id", referencedColumnName="id")
     */
    private $avatar = null;

    /**
     * @ORM\Column(type="string", length=1, nullable=true)
     */
    private $gender = '';

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description = '';

    /**
     * @ORM\ManyToOne(targetEntity="My\WorldBundle\Entity\Location", fetch="EAGER")
     * @ORM\JoinColumn(nullable=true, name="location_id", referencedColumnName="id")
     */
    private $location = null;

    /**
     * @ORM\Column(type="date")
     */
    private $register_since = '';

    /**
    * @ORM\Column(type="integer", nullable=true)
    */
    private $updatedCount = '';

    /**
     * @ORM\Column(type="text", length=6, nullable=true)
     */
    private $lang = '';
    


    /**
     * @ORM\PostLoad
     */
    public function init()
    {

        $this->type = 'person';        

    }

    public function isPerson()
    {
        if($this->type=='person' || empty($this->type)) return true;
        return false;
    }

    public function isAsso()
    {
        if($this->type=='asso') return true;
        return false;
    }

    public function isPro()
    {
        if($this->type=='pro') return true;
        return false;
    }

    public function getAge()
    {
        if(!empty($this->type)){
            if($this->type=='asso') return 'Association';
            if($this->type=='pro') return 'Professionnel';
        }
        if(!empty($this->birthday))
            return date('Y-m-d') - date($this->birthday). ' ans';

        return '';
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreatedAtValue()
    {
        $this->register_since = new \DateTime();
    }


    /**
     * @ORM\preUpdate
     */
    public function countProfileUpdated()
    {
        $this->updatedCount++;
    }

    /**
     * Set id
     *
     * @param integer $id
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set type
     *
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
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
     * Set firstname
     *
     * @param string $firstname
     * @return User
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string 
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     * @return User
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string 
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set birthday
     *
     * @param \DateTime $birthday
     * @return User
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * Get birthday
     *
     * @return \DateTime 
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * Set gender
     *
     * @param string $gender
     * @return User
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     *
     * @return string 
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return User
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set register_since
     *
     * @param \DateTime $registerSince
     * @return User
     */
    public function setRegisterSince($registerSince)
    {
        $this->register_since = $registerSince;

        return $this;
    }

    /**
     * Get register_since
     *
     * @return \DateTime 
     */
    public function getRegisterSince()
    {
        return $this->register_since;
    }

    /**
     * Set lang_prefered
     *
     * @param string $langPrefered
     * @return User
     */
    public function setLangPrefered($langPrefered)
    {
        $this->lang_prefered = $langPrefered;

        return $this;
    }

    /**
     * Get lang_prefered
     *
     * @return string 
     */
    public function getLangPrefered()
    {
        return $this->lang_prefered;
    }

    

    /**
     * Set updatedCount
     *
     * @param integer $updatedCount
     * @return User
     */
    public function setUpdatedCount($updatedCount)
    {
        $this->updatedCount = $updatedCount;

        return $this;
    }

    /**
     * Get updatedCount
     *
     * @return integer 
     */
    public function getUpdatedCount()
    {
        return $this->updatedCount;
    }

    /**
     * Set location
     *
     * @param \My\WorldBundle\Entity\Location $location
     * @return User
     */
    public function setLocation(\My\WorldBundle\Entity\Location $location = null)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return \My\WorldBundle\Entity\Location 
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set avatar
     *
     * @param object $avatar
     * @return User
     */
    public function setAvatar(Avatar $avatar)
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * Get avatar
     *
     * @return object avatar 
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * @ORM\PrePersist
     */
    public function setDefaultAvatar()
    {        
        if(!isset($this->avatar)) $this->avatar = new Avatar();
    }


}
