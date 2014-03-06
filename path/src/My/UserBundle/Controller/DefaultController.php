<?php

namespace My\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('MyUserBundle:Default:index.html.twig', array('name' => $name));
    }

    public function viewProfilAction($user)
    {
    	$user = $this->getDoctrine()->getRepository('MyUserBundle:User')->findOneById($user);

    	$user->organize = $this->getDoctrine()->getRepository('WsEventsBundle:Serie')->findByOrganizer($user);

    	$user->participate = $this->getDoctrine()->getRepository('WsEventsBundle:Participation')->findByUser($user);

    	return $this->render('MyUserBundle:Fiche:profil.html.twig',array('user'=>$user));
    }
}
