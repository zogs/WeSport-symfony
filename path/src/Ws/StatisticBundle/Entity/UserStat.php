<?php

namespace Ws\StatisticBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="statistic_user")
  * @ORM\Entity(repositoryClass="Ws\StatisticBundle\Entity\UserStatRepository")
 */
class UserStat
{

    const EVENT_CREATED = 'event_created';
    const EVENT_CONFIRMED = 'event_created_confirmed';
    const EVENT_CANCELED = 'event_created_canceled';
    const EVENT_TOTAL_PARTICIPANTS = 'event_created_total_participants';
    const EVENT_DELETED = 'event_deleted';
    const EVENT_PARTICIPATION_ADDED = 'event_participation';
    const EVENT_PARTICIPATION_CANCELED = 'event_participation_canceled';
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
    * @ORM\OneToOne(targetEntity="My\UserBundle\Entity\User", inversedBy="statistic")
    * @ORM\JoinColumn(name="user_id")
    */
    private $user;

    /**
    * @ORM\Column(name="event_created", type="integer")
    */
    public $event_created = 0;

    /**
    * @ORM\Column(name="event_created_confirmed", type="integer")
    */
    public $event_created_confirmed = 0;

    /**
    * @ORM\Column(name="event_created_canceled", type="integer")
    */
    public $event_created_canceled = 0;

    /**
    * @ORM\Column(name="event_created_total_participants", type="integer")
    */
    public $event_created_total_participants = 0;

    /**
    * @ORM\Column(name="event_deleted", type="integer")
    */
    public $event_deleted = 0;

    /**
    * @ORM\Column(name="event_participation", type="integer")
    */
    public $event_participation = 0;

    /**
    * @ORM\Column(name="event_participation_canceled", type="integer")
    */
    public $event_participation_canceled = 0;




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
     * Set event_created
     *
     * @param integer $eventCreated
     * @return UserStatistic
     */
    public function setEventCreated($eventCreated)
    {
        $this->event_created = $eventCreated;

        return $this;
    }

    /**
     * Get event_created
     *
     * @return integer 
     */
    public function getEventCreated()
    {
        return $this->event_created;
    }

    /**
     * Set event_created_confirmed
     *
     * @param integer $eventCreatedConfirmed
     * @return UserStatistic
     */
    public function setEventCreatedConfirmed($eventCreatedConfirmed)
    {
        $this->event_created_confirmed = $eventCreatedConfirmed;

        return $this;
    }

    /**
     * Get event_created_confirmed
     *
     * @return integer 
     */
    public function getEventCreatedConfirmed()
    {
        return $this->event_created_confirmed;
    }

    /**
     * Set event_created_canceled
     *
     * @param integer $eventCreatedCanceled
     * @return UserStatistic
     */
    public function setEventCreatedCanceled($eventCreatedCanceled)
    {
        $this->event_created_canceled = $eventCreatedCanceled;

        return $this;
    }

    /**
     * Get event_created_canceled
     *
     * @return integer 
     */
    public function getEventCreatedCanceled()
    {
        return $this->event_created_canceled;
    }

    /**
     * Set event_created_total_participants
     *
     * @param integer $eventCreatedTotalParticipants
     * @return UserStatistic
     */
    public function setEventCreatedTotalParticipants($eventCreatedTotalParticipants)
    {
        $this->event_created_total_participants = $eventCreatedTotalParticipants;

        return $this;
    }

    /**
     * Get event_created_total_participants
     *
     * @return integer 
     */
    public function getEventCreatedTotalParticipants()
    {
        return $this->event_created_total_participants;
    }

    /**
     * Set event_deleted
     *
     * @param integer $eventDeleted
     * @return UserStatistic
     */
    public function setEventDeleted($eventDeleted)
    {
        $this->event_deleted = $eventDeleted;

        return $this;
    }

    /**
     * Get event_deleted
     *
     * @return integer 
     */
    public function getEventDeleted()
    {
        return $this->event_deleted;
    }

    /**
     * Set event_total_participations
     *
     * @param integer $eventTotalParticipations
     * @return UserStatistic
     */
    public function setEventTotalParticipations($eventTotalParticipations)
    {
        $this->event_total_participations = $eventTotalParticipations;

        return $this;
    }

    /**
     * Get event_total_participations
     *
     * @return integer 
     */
    public function getEventTotalParticipations()
    {
        return $this->event_total_participations;
    }

    /**
     * Set event_total_participations_canceled
     *
     * @param integer $eventTotalParticipationsCanceled
     * @return UserStatistic
     */
    public function setEventTotalParticipationsCanceled($eventTotalParticipationsCanceled)
    {
        $this->event_total_participations_canceled = $eventTotalParticipationsCanceled;

        return $this;
    }

    /**
     * Get event_total_participations_canceled
     *
     * @return integer 
     */
    public function getEventTotalParticipationsCanceled()
    {
        return $this->event_total_participations_canceled;
    }

    /**
     * Set user
     *
     * @param \My\UserBundle\Entity\User $user
     * @return UserStatistic
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
}
