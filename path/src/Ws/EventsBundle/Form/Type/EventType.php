<?php
namespace Ws\EventsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormError;

use Ws\SportsBundle\Form\Type\SelectSportType;
use Ws\EventsBundle\Entity\Event;
use Ws\EventsBundle\Form\Type\SerieType;
use Ws\EventsBundle\Form\Type\InvitationType;
use My\WorldBundle\Form\Type\CityToLocationType;
use My\UtilsBundle\Form\DataTransformer\DateToDatetimeTransformer;
use My\UtilsBundle\Form\DataTransformer\TimeToDatetimeTransformer;

class EventType extends AbstractType
{
	private $pre_event; //event before modification
	private $post_event; //event after modification

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
	$builder
		->add('sport','entity',array(                                    
			'class'=>'WsSportsBundle:Sport',			
			'property'=>'name',
			'empty_value'=>'Activité ?',
			'expanded'=>false,
		))
		->add('title','text',array(	
			'required' => false,	
			))
		->add('spot', 'spot_type', array(
			'required' => true,
			))
		->add($builder->create('date', 'text', array(
				'required'=>false,
				))->addModelTransformer(new DateToDatetimeTransformer('d/m/Y'))
		)
		->add('serie', new SerieType(), array(			
			))
		->add($builder->create('time','text',array(
			))->addModelTransformer(new TimeToDatetimeTransformer('H:i'))
		)
		->add('description','textarea',array(
			'required'=>false,
			))
		->add('nbmin','integer',array(
			'required' => false,
			))
		->add('level','choice',array(
			'multiple'=> false,
			'expanded' => false,
			'required' => false,
			'choices' => Event::$valuesAvailable['level'],
			'translation_domain' => 'WsEventsBundle_event',
			))
		->add('price','integer',array(
			'required' => false,
		))
		//debug todo
		->add('invitations','invitation',array(
				'mapped' => false,			
			))
		->add('public','checkbox',array(
			'required' => false,
			))
	;

		$builder->addEventListener(FormEvents::PRE_SET_DATA, array($this,'onPreSetData'));
		$builder->addEventListener(FormEvents::SUBMIT, array($this,'onSubmit'));
		$builder->addEventListener(FormEvents::POST_SUBMIT, array($this, 'onPostSubmit'));
	}

	/**
	* Get the original Event
	*
	*/
	public function onPreSetData(FormEvent $event)
	{
		$form = $event->getForm();
		$data = $event->getData();

		$this->pre_event = clone $data;
	}

	/**
	* Validate the date field
	*
	*/
	public function onSubmit(FormEvent $event)
	{
		$form = $event->getForm();
		$data = $event->getData();

		if($date = $data->getDate()){
			if($date < new \DateTime('now')) $form->get('date')->addError(new FormError("Cette date doit être dans le futur..."));
		}
		
	}

	/**
	* Set an array of changed values 
	* 
	*/
	public function onPostSubmit(FormEvent $event)
	{
		//Detect eventualy modification if its is a modified event
		if(NULL != $this->pre_event){
			//get form data
			$this->post_event = $event->getData();			
			//get changes 
			$changes = \My\UtilsBundle\Utils\Object::getChanges($this->pre_event,$this->post_event);
			//set array of change to the event for future uses
			$this->post_event->setChanges($changes);
			//update form data
			$event->setData($this->post_event);
		}	
	}


    public function getName()
    {
        return 'event';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
	    $resolver->setDefaults(array(
	        'data_class' => 'Ws\EventsBundle\Entity\Event',
	        'error_mapping' => array(
	        	'InFutur' => 'date',
	        	)
	    ));
	}
}

?>