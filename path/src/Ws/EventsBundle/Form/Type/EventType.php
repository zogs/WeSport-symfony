<?php
namespace Ws\EventsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

use Ws\SportsBundle\Form\Type\SelectSportType;
use Ws\EventsBundle\Entity\Event;
use Ws\EventsBundle\Form\Type\SerieType;
use Ws\EventsBundle\Form\Type\InvitationType;
use My\WorldBundle\Form\Type\CityToLocationType;

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
			'attr'=>array('class'=>'__sportSelection'))
		)
		->add('level','choice',array(
			'multiple'=> false,
			'expanded' => false,
			'required' => true,
			'choices' => Event::$valuesAvailable['level'],
			))
		->add('title',null,array(		
			))
		->add('spot', 'spot_type', array(
			'required' => true,
			))
		->add('date', 'text', array(
			'required'=>false,
			))
		->add('serie', new SerieType(), array(			
			))
		->add('time','text',array(
			))
		->add('nbmin','integer',array(
			))
		->add('description','textarea',array(
			'required'=>false,
			))
		//debug todo
		->add('invitations','invitations_type',array(
				'mapped' => false,			
			))
	;


		$builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onPreSubmit'));
		$builder->addEventListener(FormEvents::POST_SUBMIT, array($this, 'onPostSubmit'));
	}

	public function onPreSubmit(FormEvent $event)
	{
		$form = $event->getForm();
		$data = $event->getData();
		$this->pre_event = clone($data);
	}

	public function onPostSubmit(FormEvent $event)
	{
		$form = $event->getForm();
		$this->post_event = $event->getData();


		//Detect eventualy modification
		//using php Reflector class
		$reflector = new \ReflectionClass($this->post_event);
		$properties = $reflector->getProperties();
		$changes = array();
		foreach ($properties as $property) {			
			$property->setAccessible(true);			
			if($property->getValue($this->pre_event) != $property->getValue($this->post_event)){				
				$changes[$property->getName()] = array(
					'pre' => $property->getValue($this->pre_event),
					'post' => $property->getValue($this->post_event)
					);
			}			
		}
		//set array of change to the event for future uses
		$this->post_event->setChanges($changes);

		$event->setData($this->post_event);
		
	}


    public function getName()
    {
        return 'event';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
	    $resolver->setDefaults(array(
	        'data_class' => 'Ws\EventsBundle\Entity\Event',
	    ));
	}
}

?>