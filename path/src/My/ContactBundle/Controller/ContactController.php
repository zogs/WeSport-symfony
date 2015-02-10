<?php

namespace My\ContactBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use My\ContactBundle\Entity\Contact;

class ContactController extends Controller
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
            $this->container->get('flashbag')->add("Bien reçu, nous vous répondrons dans les plus bref délais !");           
        }

        return $this->render('MyContactBundle:Default:contact.html.twig',array(
            'user' => $this->getUser(),
            'form' => $form->createView(),
            ));
    }

    public function renderFormAction(Request $request){

        $contact = new Contact();
        $form = $this->createForm('contact_form',$contact);

        return $this->render('MyContactBundle:Default:form.html.twig',array(
            'form' => $form->createView()
            ));
    }
}
