<?php

namespace My\ContactBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use My\ContactBundle\Entity\Contact;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('MyContactBundle:Default:index.html.twig', array('name' => $name));
    }

    public function contactAction(Request $request) {

        $contact = new Contact();
        $form = $this->createForm('contact_form',$contact);

        $form->handleRequest($request);

        if($form->isValid()) {
            
            $message = $form->getData();
            $mailer = $this->container->get('contact.mailer');
            
            $mailer->sendContactMessage($message);
            $this->container->get('flashbag')->add("Merci, nous vous répondrons dès que possible!");           
        }

        return $this->render('MyContactBundle:Default:contact.html.twig',array(
            'user' => $this->getUser(),
            'form' => $form->createView(),
            ));
    }
}
