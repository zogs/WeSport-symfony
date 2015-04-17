<?php

namespace Ws\StatisticBundle\Mailer;

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

    public function __construct(\Swift_Mailer $mailer, EngineInterface $templating, $sender)
    {
        $this->mailer = $mailer;
        $this->expediteur = $sender;
        $this->templating = $templating;
    }


    public function sendDailyStats($stats,$email)
    {
        $from = $this->expediteur;

        $subject = 'Daily Stat';

        $body = $this->templating->render('WsStatisticBundle:Email:daily_stats.html.twig', array('stats' => $stats));

        return $this->sendMessage($from, $email, $subject, $body);
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