<?php

namespace Ws\MailerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use My\UserBundle\Entity\Settings as BaseSettings;
/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="users_settings_ws_mailing")
  * @ORM\Entity(repositoryClass="Ws\MailerBundle\Entity\SettingsRepository")
 */
class Settings
{
    const EVENT_CONFIRMED = 'event_confirmed';
    const EVENT_CANCELED = 'event_canceled';
    const EVENT_CHANGED = 'event_changed';
    const EVENT_OPINION = 'event_opinion';
    const EVENT_USER_QUESTION = 'event_user_question';
    const EVENT_ORGANIZER_ANSWER = 'event_organizer_anwser';
    const EVENT_ADD_PARTICIPATION = 'event_add_participation';
    const EVENT_CANCEL_PARTICIPATION = 'event_cancel_participation';

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
    * @ORM\Column(name="event_confirmed", type="boolean")
    */
    private $event_confirmed = true;

    /**
    * @ORM\Column(name="event_canceled", type="boolean")
    */
    private $event_canceled = true;

    /**
    * @ORM\Column(name="event_changed", type="boolean")
    */
    private $event_changed = true;

    /**
    * @ORM\Column(name="event_opinion", type="boolean")
    */
    private $event_opinion = true;

    /**
    * @ORM\Column(name="event_user_question", type="boolean")
    */
    private $event_user_question = true;

    /**
    * @ORM\Column(name="event_organizer_answer", type="boolean")
    */
    private $event_organizer_answer = true;

    /**
    * @ORM\Column(name="event_add_participation", type="boolean")
    */
    private $event_add_participation = true;

    /**
    * @ORM\Column(name="event_cancel_participation", type="boolean")
    */
    private $event_cancel_participation = true;

    /**
     * isAuthorized
     *
     * @param Ws\MailerBundle\Entity\Settings const
     * @return boolean
     */
    public function isAuthorized($setting)
    {
        if($this->$setting == true) return true;
        return false;
    }

    /**
     * Set event_confirmed
     *
     * @param boolean $eventConfirmed
     * @return Settings
     */
    public function setEventConfirmed($eventConfirmed)
    {
        $this->event_confirmed = $eventConfirmed;

        return $this;
    }

    /**
     * Get event_confirmed
     *
     * @return boolean 
     */
    public function getEventConfirmed()
    {
        return $this->event_confirmed;
    }

    /**
     * Set event_canceled
     *
     * @param boolean $eventCanceled
     * @return Settings
     */
    public function setEventCanceled($eventCanceled)
    {
        $this->event_canceled = $eventCanceled;

        return $this;
    }

    /**
     * Get event_canceled
     *
     * @return boolean 
     */
    public function getEventCanceled()
    {
        return $this->event_canceled;
    }

    /**
     * Set event_changed
     *
     * @param boolean $eventChanged
     * @return Settings
     */
    public function setEventChanged($eventChanged)
    {
        $this->event_changed = $eventChanged;

        return $this;
    }

    /**
     * Get event_changed
     *
     * @return boolean 
     */
    public function getEventChanged()
    {
        return $this->event_changed;
    }

    /**
     * Set event_opinion
     *
     * @param boolean $eventOpinion
     * @return Settings
     */
    public function setEventOpinion($eventOpinion)
    {
        $this->event_opinion = $eventOpinion;

        return $this;
    }

    /**
     * Get event_opinion
     *
     * @return boolean 
     */
    public function getEventOpinion()
    {
        return $this->event_opinion;
    }

    /**
     * Set event_user_question
     *
     * @param boolean $eventUserQuestion
     * @return Settings
     */
    public function setEventUserQuestion($eventUserQuestion)
    {
        $this->event_user_question = $eventUserQuestion;

        return $this;
    }

    /**
     * Get event_user_question
     *
     * @return boolean 
     */
    public function getEventUserQuestion()
    {
        return $this->event_user_question;
    }

    /**
     * Set event_organizer_answer
     *
     * @param boolean $eventOrganizerAnswer
     * @return Settings
     */
    public function setEventOrganizerAnswer($eventOrganizerAnswer)
    {
        $this->event_organizer_answer = $eventOrganizerAnswer;

        return $this;
    }

    /**
     * Get event_organizer_answer
     *
     * @return boolean 
     */
    public function getEventOrganizerAnswer()
    {
        return $this->event_organizer_answer;
    }

    /**
     * Set event_add_participation
     *
     * @param boolean $eventAddParticipation
     * @return Settings
     */
    public function setEventAddParticipation($eventAddParticipation)
    {
        $this->event_add_participation = $eventAddParticipation;

        return $this;
    }

    /**
     * Get event_add_participation
     *
     * @return boolean 
     */
    public function getEventAddParticipation()
    {
        return $this->event_add_participation;
    }

    /**
     * Set event_cancel_participation
     *
     * @param boolean $eventCancelParticipation
     * @return Settings
     */
    public function setEventCancelParticipation($eventCancelParticipation)
    {
        $this->event_cancel_participation = $eventCancelParticipation;

        return $this;
    }

    /**
     * Get event_cancel_participation
     *
     * @return boolean 
     */
    public function getEventCancelParticipation()
    {
        return $this->event_cancel_participation;
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
}
