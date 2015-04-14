<?php

namespace My\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;


use My\UserBundle\Form\Type\ProfilEditionType;

class UserController extends Controller
{
    public function viewProfilAction($id)
    {
    	$user = $this->getDoctrine()->getRepository('MyUserBundle:User')->findOneById($id);

    	$organize = $this->getDoctrine()->getRepository('WsEventsBundle:Serie')->findByOrganizer($user);

    	$participate = $this->getDoctrine()->getRepository('WsEventsBundle:Participation')->findByUser($user);

    	return $this->render('MyUserBundle:Profile:view.html.twig',
            array(
                'user'=>$user,
                'organize'=> $organize,
                'participate'=> $participate,
                ));
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
    
    public function deleteAction($template = 'delete')
    {
        $user = $this->getUser();
        $form = $this->createFormBuilder($user)->add('confirm','choice',array(
            'label' => 'Vous êtes',
            'empty_value' => '...vraiment sûr ?',
            'choices' => array(
                'yes' => 'Oui, je veux supprimer mon compte définitivement et perdre tous mes supers pouvoirs...',
                'no' => "Non, je vais continuer à m'entrainer pour devenir plus fort!!"),
            'expanded' => false,
            'multiple' => false,
            'mapped' => false,
            ))
            ->setAction($this->generateUrl('my_user_delete_me'))
            ->getForm();

        $form->handleRequest($this->getRequest());

        if( ! $form->isValid()) return $this->render('MyUserBundle:Profile:'.$template.'.html.twig',array('form'=>$form->createView()));
        
        if($form->get('confirm')->getData() == 'no'){
            $this->get('flashbag')->add("C'est la bonne décision, le sport c'est la vie :)");
            return $this->redirect($this->generateUrl('fos_user_profile_edit'));
        }

        $msg = "Adieu ".ucfirst($user->getUsername()).", et garde la pêche !";
        $this->get('flashbag')->add($msg);

        $this->get('fos_user.user_manager')->deleteUser($user);

        return $this->redirect($this->generateUrl('ws_calendar'));

    }

    public function checkUsernameAction(Request $request)
    {        
        $errors = array();
        if(null != $user = $this->get('fos_user.user_manager')->findUserByUsername(strtolower($request->query->get('username')))){
            $errors = array('error'=>$this->get('translator')->trans('form.error.username.taken',array(),'MyUserBundle'));
        }

        if($this->getUser() != null && $user == $this->getUser()){
            $errors = array('error'=>$this->get('translator')->trans('form.error.username.itsyours',array(),'MyUserBundle'));
        }

        $response = new Response();
        $response->setContent(json_encode($errors));
        $response->headers->set('Content-Type','application/json'); 
        return $response;
    }

    public function checkEmailAction(Request $request)
    {       
        $errors = array();
        if(null != $user = $this->get('fos_user.user_manager')->findUserByEmail($request->query->get('email'))){
            $errors = array('error'=>$this->get('translator')->trans('form.error.email.taken',array(),'MyUserBundle'));
        }

        if($this->getUser() != null && $user == $this->getUser()){
            $errors = array('error'=>$this->get('translator')->trans('form.error.email.itsyours',array(),'MyUserBundle'));
        }
        
        $response = new JsonResponse();
        return $response->setData($errors);
        
    }

    

}
