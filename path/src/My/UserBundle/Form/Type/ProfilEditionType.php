<?php

namespace My\UserBundle\Form\Type;

use Symfony\Component\Routing\Router;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\RequestStack;

use My\WorldBundle\Form\Type\LocationSelectType;

use FOS\UserBundle\Form\Type\ProfileFormType as BaseType;

class ProfilEditionType extends BaseType
{
    private $user;
    private $action;
    private $router;

    public function __construct(RequestStack $requestStack, SecurityContext $security, Router $router, $default_action = 'account')
    {
        $this->router = $router;
        $this->user = $security->getToken()->getUser();
        $request = $requestStack->getCurrentRequest();
        $this->action = ($request->query->get('action') != null)? $request->query->get('action') : $default_action;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->buildUserForm($builder, $options);       
        
        $builder
            ->add('action','hidden',array(
                'data'=>$this->action,
                'mapped' => false,
                ))
            ->add('id','hidden',array(
                'data'=>$this->user->getId()
                ));

        $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onPreSetData'));
        $builder->addEventListener(FormEvents::PRE_SUBMIT, array($this, 'onPreSubmit'));
        $builder->addEventListener(FormEvents::POST_SUBMIT, array($this, 'onPostSubmit'));
    }

    public function onPreSetData(FormEvent $event)
    {               
        $form = $event->getForm();        
        $form = $this->addGroupFields($form,$this->action); 

    }
        
    public function onPreSubmit(FormEvent $event)
    {
        $form = $event->getForm();

        $this->action = $form->get('action')->getData();

        $form = $this->addGroupFields($form,$this->action);
        
        if($this->action=='password')
            $this->updateUserPassword($event->getData());
    }

    public function onPostSubmit(FormEvent $event)
    {
        $user = $event->getData();

        if($this->action=='avatar')
            $this->setAvatarFilename($user);
    }

    /**
     * Set the avatar filename to the canonical username
     */
    public function setAvatarFilename($user)
    {
        $avatar = $user->getAvatar();
        //set the avatar filename to the login of the user
        $avatar->setFilename($user->getUsernameCanonical());   
    }

    /**
     * Update current user plainPassword by the form data 
     */
    public function updateUserPassword($data)
    {        
        $this->user->setPlainPassword($data['plainPassword']['first']);
    }    

    /**
     * Add fields in function of the group action
     */
    public function addGroupFields($form,$action)
    {
        if($action=='account'){

            $form->add('username','text',array(
                'required'=> true,
                'label'=> "Login",
                'data'=> $this->user->getUsername(),
                'attr'=> array(
                    'data-icon'=> 'icon-user',
                    'data-url-checker'=> $this->router->generate('my_user_login_checker'))
                ))
                ->add('email','text',array(
                    'label'=> "Email",
                    'data'=> $this->user->getEmail(),
                    'attr'=> array(
                        'data-icon'=> 'icon-envelope',
                        'data-url-checker'=> $this->router->generate('my_user_email_checker'))
                    ))                    
                ;
        }
        else {
            //if not account action
            //hide USERNAME and EMAIL fields
            $form
                ->add('username','hidden')
                ->add('email','hidden')
            ;
        }

        if($action=='info'){

            $form->add('firstname','text',array(
                'required'=>false,
                'label'=>"Prénom",
                'data'=> $this->user->getFirstname(),
                'attr'=>array(
                    'data-icon'=>'icon-user',
                    'placeholder'=>'Votre prénom',
                    )
                ))
                ->add('lastname','text',array(
                    'required' => false,
                    'label' => 'Nom',
                    'data' => $this->user->getLastname(),
                    'attr'=> array(
                        'data-icon' => 'icon-user',
                        'placeholder'=>"Votre nom de famille",
                    )
                ))
                ->add('description','textarea',array(
                    'required' => false,
                    'label' => "Description",
                    'data' => $this->user->getDescription(),
                    'attr' => array(
                        'placeholder'=>"Décrivez vous en quelques mots !",
                        'rows'=>3,
                    )
                ))
                ->add('gender','choice',array(
                    'required'=> false,
                    'label' => "Sexe",
                    'multiple'=> false,
                    'expanded'=> false,
                    'data' => $this->user->getGender(),
                    'choices'=>array(1=>' Homme',0=>' Femme'),
                    'empty_value' => "Je suis...",
                    ))

                ->add('birthday','birthday',array(
                    'label' => "Anniversaire", 
                    'required'=> false,
                    'data' => ($this->user->getBirthday())? $this->user->getBirthday() : new \DateTime('1996/06/18'),
                    'empty_value' => ''  ,              
                    ))

                ->add('location','location_selector',array(
                    'data' => $this->user->getLocation()
                    ))
                
                ;                    
        }

        if($action=='avatar'){

            $form->add('avatar','avatar_type',array(
                'data' => $this->user->getAvatar()
                ));
        }

        if($action=='mailing'){

            $form->add('settings','ws_mailer_settings_type',array(
                'data' => $this->user->getSettings()
                ));
        }

        if($action=='password'){

            $form->add('oldpassword','password',array(
                'label' => 'Ancien mot de passe',
                'mapped' => false,
                'constraints' => new UserPassword(),
                'attr' => array(
                    'placeholder' => 'Ancien',
                    'data-icon' => 'lock',
                    )
                ))
                ->add('plainPassword', 'repeated', array(
                    'type' => 'password',                
                    'options' => array('translation_domain' => 'FOSUserBundle'),
                    'first_options' => array(
                        'label' => 'Nouveau mot de passe',
                        'attr'=> array(
                            'placeholder' => 'Nouveau',
                            'data-icon' => 'lock'
                            ),                    
                        ),
                    'second_options' => array(
                        'label' => 'Confirmer mot de passe',
                        'attr'=> array(
                            'placeholder' => 'Confirmer',
                            'data-icon' => 'lock'
                            ),                    
                        ),
                    'invalid_message' => 'fos_user.password.mismatch',

                ));
        }

        return $form;
    }   


    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'My\UserBundle\Entity\User',
        ));
    }


    public function getName()
    {
        return 'my_user_profile';
    }
}