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
            ->add('event_confirmed','checkbox',array(
                'label'=>"Recevoir un mail quand l'activité est confirmée",
                'required' => false,
                )) 
            ->add('event_canceled','checkbox',array(
                'label'=>"Recevoir un mail quand l'activité est annulée",
                'required' => false,
                )) 
            ->add('event_changed','checkbox',array(
                'label'=>"Recevoir un mail quand l'activité est modifiée",
                'required' => false,
                )) 
            ->add('event_opinion','checkbox',array(
                'label'=>"Recevoir un mail aprés l'événement pour donner son avis",
                'required' => false,
                )) 
            ->add('event_user_question','checkbox',array(
                'label'=>"Recevoir un mail quand quelqu'un pose une question",
                'required' => false,
                )) 
            ->add('event_organizer_answer','checkbox',array(
                'label'=>"Recevoir un mail quand l'organisateur répond à votre question",
                'required' => false,
                )) 
            ->add('event_add_participation','checkbox',array(
                'label'=>"Recevoir un mail quand il y a un nouvel inscrit à une activité",
                'required' => false,
                )) 
            ->add('event_cancel_participation','checkbox',array(
                'label'=>"Recevoir un mail quand quelqu'un annule sa participation",
                'required' => false,
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