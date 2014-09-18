<?php

namespace Ws\StatisticBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="statistic_general")
  * @ORM\Entity(repositoryClass="Ws\StatisticBundle\Entity\GeneralStatRepository")
 */
class GeneralStat
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
    * @ORM\Column(name="name", type="string")
    */
    private $name = 'main';

    /**
    * @ORM\Column(name="total_user_registered", type="integer")
    */
    public $total_user_registered = 0;

    /**
    * @ORM\Column(name="total_event_created", type="integer")
    */
    public $total_event_created = 0;

    /**
    * @ORM\Column(name="total_event_created_confirmed", type="integer")
    */
    public $total_event_created_confirmed = 0;

    /**
    * @ORM\Column(name="total_event_deleted", type="integer")
    */
    public $total_event_deleted = 0;

    /**
    * @ORM\Column(name="total_event_participation", type="integer")
    */
    public $total_event_participation = 0;

    /**
    * @ORM\Column(name="total_event_participation_canceled", type="integer")
    */
    public $total_event_participation_canceled = 0;




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
     * @return GeneralStat
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
     * Set total_user_registered
     *
     * @param integer $totalUserRegistred
     * @return GeneralStat
     */
    public function setTotalUserRegistered($totalUserRegistered)
    {
        $this->total_user_registered = $totalUserRegistered;

        return $this;
    }

    /**
     * Get total_user_registered
     *
     * @return integer 
     */
    public function getTotalUserRegistered()
    {
        return $this->total_user_registered;
    }

    /**
     * Set total_event_created
     *
     * @param integer $totalEventCreated
     * @return GeneralStat
     */
    public function setTotalEventCreated($totalEventCreated)
    {
        $this->total_event_created = $totalEventCreated;

        return $this;
    }

    /**
     * Get total_event_created
     *
     * @return integer 
     */
    public function getTotalEventCreated()
    {
        return $this->total_event_created;
    }

    /**
     * Set total_event_created_confirmed
     *
     * @param integer $totalEventCreatedConfirmed
     * @return GeneralStat
     */
    public function setTotalEventCreatedConfirmed($totalEventCreatedConfirmed)
    {
        $this->total_event_created_confirmed = $totalEventCreatedConfirmed;

        return $this;
    }

    /**
     * Get total_event_created_confirmed
     *
     * @return integer 
     */
    public function getTotalEventCreatedConfirmed()
    {
        return $this->total_event_created_confirmed;
    }

    /**
     * Set total_event_deleted
     *
     * @param integer $totalEventDeleted
     * @return GeneralStat
     */
    public function setTotalEventDeleted($totalEventDeleted)
    {
        $this->total_event_deleted = $totalEventDeleted;

        return $this;
    }

    /**
     * Get total_event_deleted
     *
     * @return integer 
     */
    public function getTotalEventDeleted()
    {
        return $this->total_event_deleted;
    }

    /**
     * Set total_event_participation
     *
     * @param integer $totalEventParticipation
     * @return GeneralStat
     */
    public function setTotalEventParticipation($totalEventParticipation)
    {
        $this->total_event_participation = $totalEventParticipation;

        return $this;
    }

    /**
     * Get total_event_participation
     *
     * @return integer 
     */
    public function getTotalEventParticipation()
    {
        return $this->total_event_participation;
    }

    /**
     * Set total_event_participation_canceled
     *
     * @param integer $totalEventParticipationCanceled
     * @return GeneralStat
     */
    public function setTotalEventParticipationCanceled($totalEventParticipationCanceled)
    {
        $this->total_event_participation_canceled = $totalEventParticipationCanceled;

        return $this;
    }

    /**
     * Get total_event_participation_canceled
     *
     * @return integer 
     */
    public function getTotalEventParticipationCanceled()
    {
        return $this->total_event_participation_canceled;
    }
}
