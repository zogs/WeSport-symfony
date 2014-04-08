<?php

namespace My\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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


    public function editProfilAction($action)
    {

        $user = $this->getUser();

        if($user === null){
            $this->get('flashbag')->add('info',"Veuillez vous reconnecter");           
            return $this->redirect($this->generateUrl("fos_user_security_login")); 
        }

        $form = $this->createForm(new ProfilEditionType($user,$action));   

        if($this->getRequest()->isMethod('POST')){

            $form->handleRequest($this->getRequest());
            if($form->isValid()){
                
                $userManager = $this->container->get('fos_user.user_manager');
                $userManager->updateUser($user);

                $this->get('flashbag')->add('success',"Vos informations ont été sauvegardé !");

            } else {
               
                $this->get('flashbag')->add('error',"Veuillez revoir vos informations...");
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
