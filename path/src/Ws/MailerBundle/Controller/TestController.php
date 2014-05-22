<?php

namespace Ws\MailerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TestController extends Controller
{
    public function invitationTemplateAction()
    {
    	
    	$invit = $this->getDoctrine()->getManager()->getRepository('WsEventsBundle:Invitation')->findOneRandomly();

    	$invit->setContent("Salut l'ami, on va faire un parti de sea sex and sun, tu viens ??");


        return $this->render('WsMailerBundle:Events:invitation.html.twig', array('invit' => $invit));
    }
}
