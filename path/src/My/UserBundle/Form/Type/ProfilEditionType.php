<?php

namespace My\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

use My\WorldBundle\Form\Type\LocationSelectType;


class ProfilEditionType extends AbstractType
{
    private $user;
    private $action;
    private $userManager;

    public function __construct($action, $user, $userManager)
    {
        $this->user = $user;
        $this->action = $action;
        $this->userManager = $userManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
                
        $user = $this->user;        
        $action = $this->action;
        
        $builder
        ->add('action','hidden',array(
            'data'=>$action,
            'mapped' => false,
            ))
        ->add('id','hidden',array(
            'data'=>$user->getId()
            ));


        $builder->addEventListener(FormEvents::PRE_SET_DATA, function($event) use($user,$action) {

            $form = $event->getForm();

            if($action=='account'){

                $form->add('username','text',array(
                    'required'=> true,
                    'label'=> "Login",
                    'data'=> $user->getUsername(),
                    'attr'=> array(
                        'data-icon'=> 'icon-user',
                        'data-url'=> '/url/to/check/login')
                    ))
                    ->add('email','text',array(
                        'label'=> "Email",
                        'data'=> $user->getEmail(),
                        'attr'=> array(
                            'data-icon'=> 'icon-envelope',
                            'data-url'=> '/url/to/check/email')
                        ))
                    ->add('lang','choice', array(
                        'choices'=> array('fr'=>"Français",'en'=>"Anglais"),
                        'label'=> "Langue de l'interface",
                        'expanded'=> false,
                        'multiple'=> false,
                        'empty_value'=> "( Votre langue )",
                        'attr'=> array('data-icon'=>'icon-book')
                        ));

            }

            if($action=='profil'){

                $form->add('firstname','text',array(
                    'required'=>false,
                    'label'=>"Prénom",
                    'data'=> $user->getFirstname(),
                    'attr'=>array(
                        'data-icon'=>'icon-user',
                        'placeholder'=>'Votre prénom',
                        )
                    ))
                    ->add('lastname','text',array(
                        'required' => false,
                        'label' => 'Nom',
                        'data' => $user->getLastname(),
                        'attr'=> array(
                            'data-icon' => 'icon-user',
                            'placeholder'=>"Votre nom de famille",
                        )
                    ))
                    ->add('description','textarea',array(
                        'required' => false,
                        'label' => "Description",
                        'data' => $user->getDescription(),
                        'attr' => array(
                            'placeholder'=>"Décrivez vous en quelques mots ( 130 caractères max. )",
                            'rows'=>3,
                        )
                    ))
                    ->add('gender','choice',array(
                        'required'=> false,
                        'label' => "Vous êtes...",
                        'multiple'=> false,
                        'expanded'=> false,
                        'data' => $user->getGender(),
                        'choices'=>array(1=>' un homme',0=>' une femelle'),
                        'empty_value' => "Vous êtes...",
                        ))

                    ->add('birthday','birthday',array(
                        'label' => "Birthday", 
                        'required'=> false,
                        'data' => $user->getBirthday(),
                        'empty_value' => 'Votre anniversaire'  ,
                        'data' => new \DateTime('1996/06/18'),                
                        ))

                    ->add('location','location_select',array(
                        'data'=>$user->getLocation()
                        ))
                    ;                    
            }

            if($action=='avatar'){

                $form->add('avatar','avatar_type',array(
                    'data' => $user->getAvatar()
                    ));
            }

            if($action=='mailing'){

                $form->add('settings','ws_mailer_settings_type',array(
                    'data' => $user->getSettings()
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
        });

        
        $builder->addEventListener(FormEvents::POST_SUBMIT, array($this, 'onPostSubmit'));

        $builder->addEventListener(FormEvents::PRE_SUBMIT, array($this, 'onPreSubmit'));

    }

    public function onPostSubmit(FormEvent $event)
    {
        $user = $event->getForm()->getData();

        if($this->action=='avatar')
            $this->setAvatarFilename($user);

    }

    public function onPreSubmit(FormEvent $event)
    {
        if($this->action=='password')
            $this->updateUserPassword($event->getData());
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


    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'My\UserBundle\Entity\User',
        ));
    }


    public function getName()
    {
        return 'my_profil_edition';
    }
}