<?php
namespace Ws\EventsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Ws\SportsBundle\Form\Type\SelectSportType;
use Ws\EventsBundle\Manager\CalendarManager;
use Ws\EventsBundle\Form\Type\SerieType;
use My\WorldBundle\Form\Type\AutoCompleteCityType;
use Ws\EventsBundle\Form\Type\CalendarSearchType;

class AlertType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('frequency','choice',array(
                'choices'=>array('daily'=>'Tous les jours','weekly'=>'Une fois par semaine'),
                'expanded'=> true,
                'multiple'=> false,
                'required'=> true,
                'data' => 'daily',                
                ))
            ->add('duration','choice',array(
                'label'=>"Pour combien de temps",
                'choices'=>array(1=>"1 mois",2=>"2 mois",3=>"3 mois",6=>"6 mois",12 =>"1 an"),
                'expanded'=> false,
                'multiple'=> false,
                'required'=> true,
                'mapped'=> false,
                'data'=> 3,
                ))
            ->add('search','calendar_search_type')

            ->add('submit','submit')
            ;
    		
    }

    public function getName()
    {
        return 'alert_type';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
	    $resolver->setDefaults(array(
            'invalid_message' => 'humm les alertes cest pas encore ça...',
	        'data_class' => 'Ws\EventsBundle\Entity\Alert',
            'cascade_validation' => true,
	    ));
	}
}

?>