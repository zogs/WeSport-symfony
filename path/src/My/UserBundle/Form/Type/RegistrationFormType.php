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
        ->add('username','text',array(
            'label'=>"Username",
            'attr'=>array(
                'placeholder'=>'Username',  
                'data-icon' => 'user'          
                ),
            ))

        ->add('email','email',array(
            'label'=>'E-mail address',
            'attr'=> array(                 
                    'placeholder'=>'Contact e-mail', 
                    'data-icon' => 'envelope'                   
                ),
                
            ))

        ->add('plainPassword', 'repeated', array(
                'type' => 'password',                
                'options' => array('translation_domain' => 'FOSUserBundle'),
                'first_options' => array(
                    'label' => 'form.password',
                    'attr'=> array(
                        'placeholder' => 'Password',
                        'data-icon' => 'lock'
                        ),                    
                    ),
                'second_options' => array(
                    'label' => 'Confirmer',
                    'attr'=> array(
                        'placeholder' => 'Confirmer',
                        'data-icon' => 'lock'
                        ),                    
                    ),
                'invalid_message' => 'fos_user.password.mismatch',

            ))

        ->add('birthday','birthday',array(
            'label' => "Birthday",
            'attr'=>array(
                'class'=>'row-3-select-box',
                'data-icon' => 'gift'
                ),
            
            ))

        ->add('gender','choice',array(
            'empty_value'=>'Gender',
            'choices'=> array('m'=>'Male', 'f'=>'Female'),
            'required'=>false,
            'attr'=>array(
                'data-icon'=> 'star'
                )
            ))

        ->add('location','location_form',array('mapped'=>false))


        ->add('submit','submit',array(
            'label' => 'Se connecter',
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