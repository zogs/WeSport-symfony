<?php
namespace Ws\EventsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class SerieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startdate','date',array(
                'widget'=>'single_text',
                'input'=>'datetime',
                'label'=>'Date de début',
                'required'=>false))
            ->add('enddate','date',array(                
                'widget'=>'single_text',
                'input'=>'datetime',
                'label'=>'Date de fin',
                'required'=>false))    		
            ->add('monday','checkbox',array(
                    'label'=>'Lun',
                    'required'=>false))
            ->add('tuesday','checkbox',array(
                    'label'=>'Mar',
                    'required'=>false)) 	
            ->add('wednesday','checkbox',array(
                    'label'=>'Mer',
                    'required'=>false))    
            ->add('thursday','checkbox',array(
                    'label'=>'Jeu',
                    'required'=>false))    
            ->add('friday','checkbox',array(
                    'label'=>'Ven',
                    'required'=>false))    
            ->add('saturday','checkbox',array(
                    'label'=>'Sam',
                    'required'=>false))
            ->add('sunday','checkbox',array(
                    'label'=>'Dim',
                    'required'=>false))        
    		;
    }

    public function getName()
    {
        return 'serie';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
	    $resolver->setDefaults(array(
	        'data_class' => 'Ws\EventsBundle\Entity\Serie',
	    ));
	}
}

?>