<?php

namespace My\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use My\UserBundle\Form\Type\ProfilEditionType;

class UserController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('MyUserBundle:Default:index.html.twig', array('name' => $name));
    }

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

            //get user
            $data = $form->getData();
            $email = $data['email'];
            $user =  $this->container->get('fos_user.user_manager')->findUserByEmail($email);
            //get mailer
            $mailer = $this->get('fos_user.mailer.twig_swift'); //this service is by default set to private, change to public in friendsofsymfony\user-bundle\FOS\UserBundle\Resources\config\mailer.xml
            //send mail
            $mailer->sendConfirmationEmailMessage($user);
            //flash message
            $this->get('flashbag')->add("Un email vous a été envoyé. Veuillez cliquez sur le lien contenu dans cet email",'success');
            
        }

        return $this->render('MyUserBundle:Activation:requestActivationMail.html.twig',array(
            'form'=>$form->createView(),
            ));

    }
    public function editProfilAction($action)
    {

        $user = $this->getUser();

        if($user === null){
            $this->get('flashbag')->add("Veuillez vous reconnecter",'info');           
            return $this->redirect($this->generateUrl("fos_user_security_login")); 
        }

        $form = $this->createForm(new ProfilEditionType($user,$action));   

        if($this->getRequest()->isMethod('POST')){

            $form->handleRequest($this->getRequest());
            
            if($form->isValid()){
                
                $userManager = $this->container->get('fos_user.user_manager');
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
