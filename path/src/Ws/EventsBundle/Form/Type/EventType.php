<?php
namespace Ws\EventsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Ws\SportsBundle\Form\Type\SelectSportType;
use Ws\EventsBundle\Form\Type\SerieType;
use My\WorldBundle\Form\Type\AutoCompleteCityType;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
    		->add('sport','entity',array(                                    
                                    'class'=>'WsSportsBundle:Sport',
                                    'label'=>'Sport',
                                    'property'=>'name',
                                    'empty_value'=>'Choississez un sport',
                                    'expanded'=>false,
                                    'attr'=>array('class'=>'iconSportSelect'))
            )
    		->add('title',null,array('label'=>'Titre'))
            ->add('location', new AutoCompleteCityType(), array('mapped'=>false))
    		->add('address',null)

    		->add('date', 'date', array(
                'widget'=>'single_text',
                'input'=>'datetime',
                'required'=>false))

            ->add('serie', new SerieType(), array(
                'label'=>'Jour de la semaine'))
    		
    		->add('time','time',array(
    			'widget'=>'choice',
    			'input'=>'datetime',
    			'model_timezone'=>'Europe/Paris',
    			'view_timezone'=>'Europe/Paris',
    			'with_seconds'=>false,
    			'hours'=>array(5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,0),
    			'minutes'=>array(0,10,20,30,40,50)))
    		
    		->add('nbmin','integer',array('label'=>'Nombre minimum'))
    		->add('description','textarea',array('required'=>false))
    		->add('phone','text',array('label'=>'Téléphone','required'=>false))
    		->add('save','submit');
    		
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