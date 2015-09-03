<?php

namespace My\UserBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Routing\Router;

use My\WorldBundle\Form\Type\LocationSelectType;

class RegistrationFormType extends BaseType
{
    private $router;

    public function __construct($class, Router $router)
    {
        parent::__construct($class);
        $this->router = $router;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //parent::buildForm($builder, $options);

        // add your custom field
        $builder
        ->add('type','choice',array(
                'choices'=>array(
                    'person'=>"Particulier",
                    'asso'=>"Association",
                    'pro'=>"Professionel"
                    ),
                'required'=>true,
                'multiple'=>false,
                'expanded'=>true,
            ))
        ->add('username','text',array(
            'label'=>"Nom d'utilisateur",
            'attr'=>array(
                'placeholder'=>"Nom d'utilisateur",  
                'data-icon' => 'user', 
                'data-url-checker' =>$this->router->generate('my_user_login_checker'),       
                ),
            ))

        ->add('email','email',array(
            'label'=>'E-mail de contact',
            'attr'=> array(                 
                    'placeholder'=>"E-mail de contact", 
                    'data-icon' => 'envelope',
                    'data-url-checker' =>$this->router->generate('my_user_email_checker'),                          
                ),
                
            ))

        ->add('plainPassword', 'repeated', array(
                'type' => 'password',                
                'options' => array('translation_domain' => 'FOSUserBundle'),
                'first_options' => array(
                    'label' => 'Mot de passe',
                    'attr'=> array(
                        'placeholder' => 'Mot de passe',
                        'data-icon' => 'lock'
                        ),                    
                    ),
                'second_options' => array(
                    'label' => 'Confirmer',
                    'attr'=> array(
                        'placeholder' => 'Confirmer le mot de passe',
                        'data-icon' => 'lock'
                        ),                    
                    ),
                'invalid_message' => 'fos_user.password.mismatch',

            ))

        ->add('birthday','birthday',array(
            'label' => "Birthday", 
            'required'=>false,
            'empty_value'=>'Votre anniversaire'  ,
            'data' => new \DateTime('1996/06/18'),                
            ))

        ->add('gender','choice',array(
            'empty_value'=>'Gender',
            'choices'=> array('m'=>'Male', 'f'=>'Female'),
            'required'=>false,
            'attr'=>array(
                'data-icon'=> 'star',
                'placeholder'=>"Sexe"
                )
            ))

        ->add('location','location_selector',array( ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'      => 'My\UserBundle\Entity\User',
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            // a unique key to help generate the secret token
            'intention'       => 'user_registration_intention',
        ));
    }

    public function getName()
    {
        return 'my_user_registration';
    }
}