<?php
namespace Ws\EventsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Ws\SportsBundle\Form\Type\SelectSportType;
use My\WorldBundle\Form\Type\AutoCompleteCityType;

class CalendarSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('location', new AutoCompleteCityType(), array('mapped'=>false))
            ->add('area','choice',array(
                'choices'=> array(
                    0 => 'Okm',
                    10 => '10km',
                    30 => '30km',
                    50 => '50km',
                    100 => '100km',
                    ),
                'multiple'=> false,
                'expanded' => false,
                ))
            ->add('sport','entity',array(                                    
                                    'class'=>'WsSportsBundle:Sport',
                                    'label'=>'Sport',
                                    'property'=>'name',
                                    'empty_value'=>'Choississez un sport',
                                    'expanded'=>false,
                                    'attr'=>array('class'=>'iconSportSelect'))
            )            
    		;
    }

    public function getName()
    {
        return 'search_calendar';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
	    $resolver->setDefaults(array(
	        'data_class' => null,
	    ));
	}
}

?>