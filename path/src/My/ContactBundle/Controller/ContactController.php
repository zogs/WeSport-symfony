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
            $flashbag = $this->container->get('flashbag');

            if($mailer->sendContactMessage($message)){
                $flashbag->add("Merci pour votre message, nous allons regarder ça avec attention !");                           
            }
            else {
                $flashbag->add("Arf, on dirait que ça ne marche pas pour l'instant :/ ... Mais vous pouvez directement nous écrire contact@cosporturage.fr si vous voulez !",'error');
            }            
        }

        return $this->render('MyContactBundle:Page:contact.html.twig',array(
            'user' => $this->getUser(),
            'form' => $form->createView(),
            ));
    }

    public function embeddedFormAction(Request $request){

        $contact = new Contact();
        $form = $this->createForm('contact_form',$contact);

        return $this->render('MyContactBundle:Form:form_embedded.html.twig',array(
            'form' => $form->createView()
            ));
    }
}
