<?php

namespace Ws\StatisticBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="statistic_global")
  * @ORM\Entity(repositoryClass="Ws\StatisticBundle\Entity\GlobalStatRepository")
 */
class GlobalStat
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
    private $total_user_registered = 0;

    /**
    * @ORM\Column(name="total_event_created", type="integer")
    */
    private $total_event_created = 0;

    /**
    * @ORM\Column(name="total_event_created_confirmed", type="integer")
    */
    private $total_event_created_confirmed = 0;

    /**
    * @ORM\Column(name="total_event_deleted", type="integer")
    */
    private $total_event_deleted = 0;

    /**
    * @ORM\Column(name="total_event_participation", type="integer")
    */
    private $total_event_participation = 0;

    /**
    * @ORM\Column(name="total_event_participation_canceled", type="integer")
    */
    private $total_event_participation_canceled = 0;

    /**
    * @ORM\Column(name="total_alert_created", type="integer")
    */
    private $total_alert_created = 0;




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
     * @return GlobalStat
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
     * @return GlobalStat
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
     * @return GlobalStat
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
     * @return GlobalStat
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
     * @return GlobalStat
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
     * @return GlobalStat
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
     * @return GlobalStat
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

    /**
     * Set total_alert_create
     *
     * @param integer $totalAlertCreate
     * @return GlobalStat
     */
    public function setTotalAlertCreate($totalAlertCreate)
    {
        $this->total_alert_create = $totalAlertCreate;

        return $this;
    }

    /**
     * Get total_alert_create
     *
     * @return integer 
     */
    public function getTotalAlertCreate()
    {
        return $this->total_alert_create;
    }

    /**
     * Set total_alert_created
     *
     * @param integer $totalAlertCreated
     * @return GlobalStat
     */
    public function setTotalAlertCreated($totalAlertCreated)
    {
        $this->total_alert_created = $totalAlertCreated;

        return $this;
    }

    /**
     * Get total_alert_created
     *
     * @return integer 
     */
    public function getTotalAlertCreated()
    {
        return $this->total_alert_created;
    }
}
