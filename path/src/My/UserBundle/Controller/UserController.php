<?php

namespace My\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use My\UserBundle\Form\Type\ProfilEditionType;

class UserController extends Controller
{
    public function viewProfilAction($id)
    {
    	$user = $this->getDoctrine()->getRepository('MyUserBundle:User')->findOneById($id);

    	$user->organize = $this->getDoctrine()->getRepository('WsEventsBundle:Serie')->findByOrganizer($user);

    	$user->participate = $this->getDoctrine()->getRepository('WsEventsBundle:Participation')->findByUser($user);

    	return $this->render('MyUserBundle:Profil:view.html.twig',array('user'=>$user));
    }

    public function requestActivationMailAction(Request $request)
    {
        $user = $this->getUser();
        $form = $this->createFormBuilder($user)->add('email','email')->getForm();
        
        $form->handleRequest($request);
 
        if($form->isValid()) {
            
            $data = $form->getData();
            $email = $data['email'];
            //get user
            $user = $this->getDoctrine()->getRepository('MyUserBundle:User')->findOneByEmail($email);
            $user = $this->getDoctrine()->getRepository('MyUserBundle:User')->generateNewConfirmationToken($user);
            //send mail
            $this->get('my_user.mailer')->sendConfirmationEmailMessage($user);
            //flash message
            $this->get('flashbag')->add("Un email vous a été envoyé! Veuillez cliquez sur le lien contenu dans cet email.",'success');
            
        }

        return $this->render('MyUserBundle:Activation:requestActivationMail.html.twig',array(
            'form'=>$form->createView(),
            ));

    }
    
    public function deleteAction(Request $request)
    {
        $user = $this->getUser();
        $form = $this->createFormBuilder($user)->add('confirm','choice',array(
            'label' => 'Etes vous vraiment sûr ?',
            'choices' => array(
                'yes' => 'Oui, je veux supprimer mon compte et perdre tous mes supers pouvoirs',
                'no' => "Non, je vais continuer à m'entrainer pour devenir plus fort !"),
            'empty_value' => '',
            'expanded' => false,
            'multiple' => false,
            'mapped' => false,
            ))->getForm();

        $form->handleRequest($request);

        if( ! $form->isValid()) return $this->render('MyUserBundle:Profile:delete.html.twig',array('form'=>$form->createView()));

        if($form->get('confirm')->getData() == 'yes'){

            $msg = "Tchao' ".ucfirst($user->getUsername())." et garde la motivation !";
            if($user->getGender()=='f') $msg = "Bisous ".ucfirst($user->getUsername())." et continue le sport !";
            $this->get('flashbag')->add($msg);

            $this->get('fos_user.user_manager')->deleteUser($user);

            return $this->redirect($this->generateUrl('ws_calendar'));
        }

        return $this->render('MyUserBundle:Profile:delete.html.twig',array(
            'form'=>$form->createView()
            ));
    }

}
