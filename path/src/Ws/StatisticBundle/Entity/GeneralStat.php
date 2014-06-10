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
     * @return General
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
     * Set user_total_count
     *
     * @param integer $userTotalCount
     * @return General
     */
    public function setUserTotalCount($userTotalCount)
    {
        $this->user_total_count = $userTotalCount;

        return $this;
    }

    /**
     * Get user_total_count
     *
     * @return integer 
     */
    public function getUserTotalCount()
    {
        return $this->user_total_count;
    }

    /**
     * Set event_total_count
     *
     * @param integer $eventTotalCount
     * @return General
     */
    public function setEventTotalCount($eventTotalCount)
    {
        $this->event_total_count = $eventTotalCount;

        return $this;
    }

    /**
     * Get event_total_count
     *
     * @return integer 
     */
    public function getEventTotalCount()
    {
        return $this->event_total_count;
    }

    /**
     * Set event_total_participation_added
     *
     * @param integer $eventTotalParticipationAdded
     * @return General
     */
    public function setEventTotalParticipationAdded($eventTotalParticipationAdded)
    {
        $this->event_total_participation_added = $eventTotalParticipationAdded;

        return $this;
    }

    /**
     * Get event_total_participation_added
     *
     * @return integer 
     */
    public function getEventTotalParticipationAdded()
    {
        return $this->event_total_participation_added;
    }

    /**
     * Set event_total_participation_canceled
     *
     * @param integer $eventTotalParticipationCanceled
     * @return General
     */
    public function setEventTotalParticipationCanceled($eventTotalParticipationCanceled)
    {
        $this->event_total_participation_canceled = $eventTotalParticipationCanceled;

        return $this;
    }

    /**
     * Get event_total_participation_canceled
     *
     * @return integer 
     */
    public function getEventTotalParticipationCanceled()
    {
        return $this->event_total_participation_canceled;
    }
}
