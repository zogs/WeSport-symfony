<?php
namespace Ws\EventsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

use My\WorldBundle\Form\Type\AutoCompleteCityType;
use Ws\SportsBundle\Form\Type\SelectSportType;
use Ws\EventsBundle\Manager\CalendarManager;
use Ws\EventsBundle\Form\Type\SerieType;
use Ws\EventsBundle\Form\Type\CalendarSearchType;
use My\UserBundle\Entity\User;

class AlertType extends AbstractType
{

    private $user;

    public function __construct(SecurityContext $secu)
    {
        $this->user = $secu->getToken()->getUser();
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email','text',array(
                'required' => true,
                ))
            ->add('frequency','choice',array(
                'choices'=>array('daily'=>'daily','weekly'=>'weekly'),
                'expanded'=> false,
                'multiple'=> false,
                'required'=> true,
                'empty_data' => 'daily',  
                'translation_domain' => 'WsEventsBundle_alert',              
                ))
            ->add('duration','choice',array(
                'label'=>"Pour combien de temps",
                'choices'=>array(1=>"1 mois",2=>"2 mois",3=>"3 mois",6=>"6 mois",12 =>"1 an"),
                'expanded'=> false,
                'multiple'=> false,
                'required'=> true,
                'empty_data'=> 3,
                ))
            ->add('search','calendar_search')
            ;
    		
            $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onPreSetData'));
            $builder->addEventListener(FormEvents::POST_SUBMIT, array($this, 'onPostSubmit'));
    }

    public function onPreSetData(FormEvent $event)
    {
        $form = $event->getForm();

        if($this->user instanceof User){

            $form->add('email','text',array(
                'required' => true,
                'mapped' => true,
                'data' => $this->user->getEmail()
                ));
        }        
    }

     public function onPostSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $alert = $event->getData();

        if( $this->user instanceof User) {
            //Set the current user 
            $alert->setUser($this->user);
            
        }

    }
    public function getName()
    {
        return 'alert';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
	    $resolver->setDefaults(array(
            'invalid_message' => "Oups, ça s'était pas prévu, il y a une erreur dans le formulaire...",
	        'data_class' => 'Ws\EventsBundle\Entity\Alert',
            'cascade_validation' => true,
	    ));
	}
}

?>