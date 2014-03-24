<?php

namespace My\UserBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;

use My\WorldBundle\Form\Type\LocationSelectForms;

class RegistrationFormType extends BaseType
{
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
                'data-icon' => 'user'          
                ),
            ))

        ->add('email','email',array(
            'label'=>'E-mail de contact',
            'attr'=> array(                 
                    'placeholder'=>"E-mail de contact", 
                    'data-icon' => 'envelope'                   
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

        ->add('location','location_form',array( ))


        ->add('submit','submit',array(
            'label' => 'S inscrire',
            'attr' => array(
                'class'=>'btn btn-info'
                )
            ));
    }

    public function getName()
    {
        return 'my_user_registration';
    }
}