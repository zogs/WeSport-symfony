<?php

namespace Ws\MailerBundle\Mailer;

use Symfony\Component\Templating\EngineInterface;

use Ws\EventsBundle\Entity\Invitation;
use Ws\EventsBundle\Entity\Alert;
use Ws\EventsBundle\Manager\CalendarUrlGenerator;

class Mailer
{
    protected $mailer;

    protected $templating;

    private $expediteur = array('contact@cosporturage.fr' => 'coSporturage.fr');

    public function __construct(\Swift_Mailer $mailer, EngineInterface $templating)
    {
        $this->mailer = $mailer;

        $this->templating = $templating;
    }

    public function sendTestMessage()
    {

        $this->sendMessage('sfwesport@we-sport.fr', 'guichardsim@gmail.com', 'test mailer', '<html><body><strong>Hello world</strong></body></html>');;
    }

    public function sendAlertMessage(Alert $alert, CalendarUrlGenerator $generator, $events)
    {
        $email = $alert->getUser()->getEmail();

        $subject = "Vos alertes coSporturage : ".count($events)." activités.";

        $body = $this->templating->render('WsMailerBundle:Alerts:alert.html.twig',array(
            'alert'=>$alert,
            'events'=>$events,
            'url_params' => $generator->setSearch($alert->getSearch())->getUrlParams(true),
            ));

        if($this->sendMessage($this->expediteur,$email,$subject,$body))
            return true;
        else
            return false;
    }

    public function sendInvitationMessages(Invitation $invit)
    {
        //set expeditor
        $from = $this->expediteur;
        //set subject
        $subject = 'Invitation de '.$invit->getInviter()->getUsername();

        $emails = array();
        foreach ($invit->getInvited() as $key => $invited) {

            //templating
            $body = $this->templating->render('WsMailerBundle:Events:invitation.html.twig',array(
                'invit' => $invit,
                'invited' => $invited
                ));
            //send message
            $this->sendMessage($from,$invited->getEmail(),$subject,$body);
            //stock sended email
            $emails[] = $invited->getEmail();
        }

        return $emails;
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