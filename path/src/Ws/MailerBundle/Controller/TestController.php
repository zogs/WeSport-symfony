<?php

namespace Ws\MailerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TestController extends Controller
{
    public function invitationTemplateAction()
    {

    	$invit = $this->getDoctrine()->getManager()->getRepository('WsEventsBundle:Invitation')->findOneRandomly();

        return $this->render('WsMailerBundle:Events:invitation.html.twig', array('invit' => $invit));
    }
}
