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
    public function editProfilAction($action)
    {

        $user = $this->getUser();
        $userManager = $this->container->get('fos_user.user_manager');

        if($user === null){
            $this->get('flashbag')->add("Veuillez vous reconnecter",'info');           
            return $this->redirect($this->generateUrl("fos_user_security_login")); 
        }

        $form = $this->createForm(new ProfilEditionType($action,$user,$userManager));   

        if($this->getRequest()->isMethod('POST')){

            $form->handleRequest($this->getRequest());
            
            if($form->isValid()){
                
                $userManager->updateUser($user);

                $this->get('flashbag')->add("Vos informations ont été sauvegardé !");

            } else {
               
                $this->get('flashbag')->add("Veuillez revoir vos informations...",'error');
            }
            
        }


        return $this->render('MyUserBundle:Profil:edit.html.twig',array(
            'user'=>$user,
            'action'=>$action,
            'form'=>$form->createView(),
            )
        );
    }

}
