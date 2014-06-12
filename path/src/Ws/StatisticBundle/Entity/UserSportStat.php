<?php

namespace Ws\StatisticBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="statistic_user_sports")
  * @ORM\Entity(repositoryClass="Ws\StatisticBundle\Entity\UserSportStatRepository")
 */
class UserSportStat
{

    const SPORT_CREATED = 'nb_created';
    const SPORT_PARTICIPATED = 'nb_participated';

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
    * @ORM\ManyToOne(targetEntity="My\UserBundle\Entity\User")
    * @ORM\JoinColumn(name="user_id")
    */
    private $user;

    /**
    * @ORM\ManyToOne(targetEntity="Ws\SportsBundle\Entity\Sport")
    * @ORM\JoinColumn(name="sport_id")
    */
    private $sport;

    /**
    * @ORM\Column(name="nb_created", type="integer")
    */
    public $created = 0;

    /**
    * @ORM\Column(name="nb_participated", type="integer")
    */
    public $participated = 0;


    public function __construct($sport,$user)
    {
        $this->setSport($sport);
        $this->setUser($user);
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
     * Set created
     *
     * @param integer $created
     * @return UserSportStat
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return integer 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set participated
     *
     * @param integer $participated
     * @return UserSportStat
     */
    public function setParticipated($participated)
    {
        $this->participated = $participated;

        return $this;
    }

    /**
     * Get participated
     *
     * @return integer 
     */
    public function getParticipated()
    {
        return $this->participated;
    }

    /**
     * Set user
     *
     * @param \My\UserBundle\Entity\User $user
     * @return UserSportStat
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
     * Set sport
     *
     * @param \Ws\SportsBundle\Entity\Sport $sport
     * @return UserSportStat
     */
    public function setSport(\Ws\SportsBundle\Entity\Sport $sport = null)
    {
        $this->sport = $sport;

        return $this;
    }

    /**
     * Get sport
     *
     * @return \Ws\SportsBundle\Entity\Sport 
     */
    public function getSport()
    {
        return $this->sport;
    }
}
