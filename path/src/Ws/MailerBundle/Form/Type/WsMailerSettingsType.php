<?php
namespace Ws\MailerBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

use Ws\MailerBundle\Form\DataTransformer\WsMailingSettingsToSettingsTransformer;

class WsMailerSettingsType extends AbstractType
{
    private $user;

    public function __construct($context)
    {
        $this->user = $context->getToken()->getUser();
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('event_confirmed','choice',array(
                'label'=>"Recevoir un mail quand l'activité est confirmée",
                'required' => false,
                'choices' => array('1'=>'Oui','0'=>'Non'),
                'empty_value'  => false,
                'multiple' => false,
                'expanded' => true,
                )) 
            ->add('event_canceled','choice',array(
                'label'=>"Recevoir un mail quand l'activité est annulée",
                'required' => false,
                'choices' => array('1'=>'Oui','0'=>'Non'),
                'empty_value'  => false,
                'multiple' => false,
                'expanded' => true,
                )) 
            ->add('event_changed','choice',array(
                'label'=>"Recevoir un mail quand l'activité est modifiée",
                'required' => false,
                'choices' => array('1'=>'Oui','0'=>'Non'),
                'empty_value'  => false,
                'multiple' => false,
                'expanded' => true,
                )) 
            ->add('event_opinion','choice',array(
                'label'=>"Recevoir un mail aprés l'événement pour donner son avis",
                'required' => false,
                'choices' => array('1'=>'Oui','0'=>'Non'),
                'empty_value'  => false,
                'multiple' => false,
                'expanded' => true,
                )) 
            ->add('event_user_question','choice',array(
                'label'=>"Recevoir un mail quand quelqu'un pose une question",
                'required' => false,
                'choices' => array('1'=>'Oui','0'=>'Non'),
                'empty_value'  => false,
                'multiple' => false,
                'expanded' => true,
                )) 
            ->add('event_organizer_answer','choice',array(
                'label'=>"Recevoir un mail quand l'organisateur répond à votre question",
                'required' => false,
                'choices' => array('1'=>'Oui','0'=>'Non'),
                'empty_value'  => false,
                'multiple' => false,
                'expanded' => true,
                )) 
            ->add('event_add_participation','choice',array(
                'label'=>"Recevoir un mail quand il y a un nouvel inscrit à une activité",
                'required' => false,
                'choices' => array('1'=>'Oui','0'=>'Non'),
                'empty_value'  => false,
                'multiple' => false,
                'expanded' => true,
                )) 
            ->add('event_cancel_participation','choice',array(
                'label'=>"Recevoir un mail quand quelqu'un annule sa participation",
                'required' => false,
                'choices' => array('1'=>'Oui','0'=>'Non'),
                'empty_value'  => false,
                'multiple' => false,
                'expanded' => true,
                ))
                ->addModelTransformer(new WsMailingSettingsToSettingsTransformer($this->user))                 
    		;

    }

    public function getName()
    {
        return 'ws_mailer_settings_type';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
	    $resolver->setDefaults(array(
            //'invalid_message' => 'error in search type form',
	        'data_class' => 'Ws\MailerBundle\Entity\Settings',
            'cascade_validation' => true,
	    ));
	}
}

?>