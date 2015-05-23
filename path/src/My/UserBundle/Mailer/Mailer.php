<?php

namespace My\UserBundle\Mailer;

use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Routing\RouterInterface;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Mailer\MailerInterface;

use My\UserBundle\Entity\User;

class Mailer implements MailerInterface
{
    protected $mailer;
    protected $router;
    protected $templating;

    public $expediteur = array('contact@jeveuxdusport.com' => 'Jeveuxdusport.com');

    public function __construct(\Swift_Mailer $mailer, EngineInterface $templating, RouterInterface $router)
    {
        $this->mailer = $mailer;
        $this->router = $router;
        $this->templating = $templating;
    }

    public function sendTestMessage()
    {
        $this->sendMessage('sfwesport@we-sport.fr', 'guichardsim@gmail.com', 'test mailer', '<html><body><strong>Hello world</strong></body></html>');;
    }

    public function sendConfirmationEmailMessage(UserInterface $user)
    {
        
        $subject = "Oui, c'est moi l'email de confirmation!";

        $url = $this->router->generate('fos_user_registration_confirm', array('token' => $user->getConfirmationToken()), true);

        $body = $this->templating->render('MyUserBundle:Registration:email.html.twig', array(
            'user' => $user,
            'confirmationUrl' => $url
            ));

        $this->sendMessage($this->expediteur,$user->getEmail(),$subject,$body);
    }

    /**
     * {@inheritdoc}
     */
    public function sendResettingEmailMessage(UserInterface $user)
    {
        $subject = "Mot de passe oublié, pas de problème !";

        $url = $this->router->generate('fos_user_resetting_reset', array('token' => $user->getConfirmationToken()), true);

        $body = $this->templating->render('MyUserBundle:Resetting:email.html.twig', array(
            'user' => $user,
            'confirmationUrl' => $url
        ));

        $this->sendMessage($this->expediteur,$user->getEmail(),$subject,$body);
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

        $this->mailer->send($mail);
    }
}