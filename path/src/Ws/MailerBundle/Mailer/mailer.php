<?php

namespace Ws\MailerBundle\Mailer;

use Symfony\Component\Templating\EngineInterface;

use Ws\EventsBundle\Entity\Invitation;

class Mailer
{
    protected $mailer;

    protected $templating;

    private $expediteur = 'contact@we-sport.fr';

    public function __construct(\Swift_Mailer $mailer, EngineInterface $templating)
    {
        $this->mailer = $mailer;

        $this->templating = $templating;
    }

    public function sendTestMessage()
    {

        $this->sendMessage('sfwesport@we-sport.fr', 'guichardsim@gmail.com', 'test mailer', '<html><body><strong>Hello world</strong></body></html>');;
    }

    public function sendInvitationMessage(Invitation $invit)
    {
        $from = $this->expediteur;

        $subject = 'Invitation de '.$invit->getInviter()->getUsername();

        $body = $this->templating->render('WsMailerBundle:Events:invitation.html.twig',array(
            'invit' => $invit));

        //send mailing
        foreach ($invit->getInvited() as $key => $invited) {

            $this->sendMessage($from,$invited->getEmail(),$subject,$body);

        }

        return $key + 1;
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