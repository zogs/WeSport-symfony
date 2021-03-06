<?php

namespace Ws\MailerBundle\Mailer;

use Symfony\Component\Templating\EngineInterface;

use Ws\EventsBundle\Entity\Invited;
use Ws\EventsBundle\Entity\Invitation;
use Ws\EventsBundle\Entity\Alert;
use Ws\EventsBundle\Manager\CalendarUrlGenerator;
use My\UserBundle\Entity\User;
use Ws\EventsBundle\Entity\Event;
use Ws\MailerBundle\Entity\Settings;
use My\CommentBundle\Entity\Comment;

class Mailer
{
    protected $mailer;
    protected $templating;
    protected $calendar_url_generator;

    public function __construct(\Swift_Mailer $mailer, EngineInterface $templating, CalendarUrlGenerator $generator, $sender)
    {
        $this->mailer = $mailer;
        $this->expediteur = $sender;
        $this->templating = $templating;
        $this->calendar_url_generator = $generator;
    }

    public function sendTestMessage()
    {

        $this->sendMessage('sfwesport@we-sport.fr', 'guichardsim@gmail.com', 'test mailer', '<html><body><strong>Hello world</strong></body></html>');;
    }

    public function sendPastEventOpinionReminder(Event $event)
    {
        $subject = "Vous avez passé un bon moment ?";

        foreach ($event->getParticipations() as $participation) {

            $user = $participation->getUser();
            
            if($user->getSettings()->isAuthorizedEmail(Settings::EVENT_OPINION) === false ) continue;

            $body = $this->templating->render('WsMailerBundle:Cron:opinion_reminder.html.twig',array(
                'event'=>$event,
                'user'=>$user
                ));

            $this->sendMessage($this->expediteur,$user->getEmail(),$subject,$body);
            
        }
    }

    public function sendPastEventEncouragement(Event $event)
    {
        $subject = "Personne ? La prochaine fois ça ira mieux!";

        $body = $this->templating->render('WsMailerBundle:Cron:encouragement.html.twig',array(
            'event'=>$event
            ));

        $this->sendMessage($this->expediteur,$event->getOrganizer()->getEmail(),$subject,$body);
    }

    public function sendEventCanceledToParticipants(Event $event)
    {
        $subject = "Cette activité manque de participants et ne peut avoir lieu pour l'instant";

        $body = $this->templating->render('WsMailerBundle:Events:canceled.html.twig',array(
            'event'=>$event
            ));

        foreach($event->getParticipations(false) as $participant){

            if($participant->getUser() === null || $participant->getUser()->getSettings()->isAuthorizedEmail(Settings::EVENT_CANCELED) === false ) continue;

            $this->sendMessage($this->expediteur,$participant->getUser()->getEmail(),$subject,$body);
        }
    }

    public function sendEventDeletedToParticipants(Event $event)
    {
        $subject = "Une activité à laquelle vous participez a été annulée...";

        $body = $this->templating->render('WsMailerBundle:Events:deleted.html.twig',array(
            'event'=>$event
            ));

        foreach($event->getParticipations(false) as $participant){

            if($participant->getUser() === null || $participant->getUser()->getSettings()->isAuthorizedEmail(Settings::EVENT_CANCELED) === false ) continue;

            $this->sendMessage($this->expediteur,$participant->getUser()->getEmail(),$subject,$body);
        }
    }

    public function sendEventConfirmedToParticipants(Event $event)
    {
        $subject = "L'activité ".$event->getTitle()." est confirmé !";

        $body = $this->templating->render('WsMailerBundle:Events:confirmed.html.twig',array(
            'event'=>$event
            ));

        foreach($event->getParticipations(false) as $participant){

            if($participant->getUser() === null || $participant->getUser()->getSettings()->isAuthorizedEmail(Settings::EVENT_CONFIRM) === false ) continue;

            $this->sendMessage($this->expediteur,$participant->getUser()->getEmail(),$subject,$body);
        }
    }

    public function sendEventModificationToParticipants(Event $event)
    {
        $subject = "L'activité suivante a été modifié: ".$event->getTitle();

         $body = $this->templating->render('WsMailerBundle:Events:changes.html.twig',array(
            'event' => $event,
            ));

         foreach ($event->getParticipations(false) as $participant) {            

                if($participant->getUser() === null || $participant->getUser()->getSettings()->isAuthorizedEmail(Settings::EVENT_CHANGED) === false) continue;

                $this->sendMessage($this->expediteur,$participant->getUser()->getEmail(),$subject,$body);
         }

    }

    public function sendParticipationAddedToAdmin(Event $event, User $participant)
    {
        if($event->getOrganizer()->getSettings()->isAuthorizedEmail(Settings::EVENT_ADD_PARTICIPATION) === false) return;

        $admin_email = $event->getOrganizer()->getEmail();

        $subject = ucfirst($participant->getUsername())." participe à votre activité ".ucfirst($event->getTitle());

        $body = $this->templating->render('WsMailerBundle:Participation:inform_organizer_participation_added.html.twig',array(
            'event' => $event,
            'participant' => $participant,
            ));

        if($this->sendMessage($this->expediteur,$admin_email,$subject,$body))
            return true;
        else
            return false;
    }

    public function sendParticipationCanceledToAdmin(Event $event, User $participant)
    {
        if($event->getOrganizer()->getSettings()->isAuthorizedEmail(Settings::EVENT_CANCEL_PARTICIPATION) === false) return;

        $admin_email = $event->getOrganizer()->getEmail();

        $subject = ucfirst($participant->getUsername())." annule sa participation à votre activité...";

        $body = $this->templating->render('WsMailerBundle:Participation:inform_organizer_participation_canceled.html.twig',array(
            'event' => $event,
            'participant' => $participant,
            ));

        if($this->sendMessage($this->expediteur,$admin_email,$subject,$body))
            return true;
        else
            return false;
    }

    public function sendAlertConfirmation(Alert $alert, User $user)
    {
        $email = $alert->getEmail();
        $subject = "Vos alertes sont prêtes !";
        $body = $this->templating->render('WsMailerBundle:Alerts:confirmation.html.twig',array(
            'alert'=>$alert,
            'user'=>$user,
            ));

        if($this->sendMessage($this->expediteur,$email,$subject,$body))
            return true;
        else
            return false;
    }

    public function sendAlertMessage(Alert $alert, $events)
    {
        $email = $alert->getEmail();

        $subject = "Vos alertes : ".count($events)." activités.";

        $body = $this->templating->render('WsMailerBundle:Alerts:alert.html.twig',array(
            'alert'=>$alert,
            'events'=>$events,
            'url_params' => $this->calendar_url_generator->setSearch($alert->getSearch())->getUrlParams(true),
            ));

        if($this->sendMessage($this->expediteur,$email,$subject,$body))
            return true;
        else
            return false;
    }

    
    public function sendExpiredAlertmessage(Alert $alert)
    {
        $email = $alert->getUser()->getEmail();

        $subject = "Votre alerte expire aujourd'hui !";

        $body = $this->templating->render('WsMailerBundle:Alerts:expired.html.twig',array(
            'alert'=>$alert,
            ));

        if($this->sendMessage($this->expediteur,$email,$subject,$body))
            return true;
        else
            return false;
    }


    public function sendInvitationMessages(Invitation $invitation)
    {

        $emails = array();
        foreach ($invitation->getInvited() as $key => $invited) {
   
            //send only not-sended message
            if($invited->getNbSended() == 0){

                $email = $this->sendInvitedMessage($invited);
                $emails[] = $email;
            }
        }

        return $emails;
    }

    public function sendInvitedMessage(Invited $invited)
    {
        $from = $this->expediteur;
        $subject = "Invitation d'un ami !";
        $body = $this->templating->render('WsMailerBundle:Events:invitation.html.twig',array(
            'invit' => $invited->getInvitation(),
            'invited' => $invited
            ));
        $this->sendMessage($from,$invited->getEmail(),$subject,$body);

        return $invited->getEmail();
    }

    public function sendEventCommentedMessage(Comment $comment, Event $event)
    {
        $from = $this->expediteur;
        $subject = ucfirst($comment->getAuthor()->getUsername())." à posé une question ";
        $body = $this->templating->render('WsMailerBundle:Events:event_commented.html.twig',array(
            'comment' => $comment,
            'event' => $event
            ));
        $this->sendMessage($from,$event->getOrganizer()->getEmail(),$subject,$body);

        return $comment->getAuthor()->getEmail();
    }

    

    protected function sendMessage($from, $to, $subject, $body)
    {
        $mail = \Swift_Message::newInstance();

        $mail
            ->setFrom($from)
            ->setTo($to)
            ->setSubject($subject)
            ->setBody($body)
            ->setContentType('text/html');

        if($this->mailer->send($mail))
            return true;
        else
            return false;
    }
}