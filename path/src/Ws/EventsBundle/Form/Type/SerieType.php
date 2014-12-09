<?php
namespace Ws\EventsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormError;

use My\UtilsBundle\Form\DataTransformer\DateToDatetimeTransformer;

class SerieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                    $builder->create('startdate','text',array(
                        'required'=>false))
                    ->addModelTransformer(new DateToDatetimeTransformer('d/m/Y'))
            )
            ->add(
                    $builder->create('enddate','text',array(                
                        'required'=>false))    
                    ->addModelTransformer(new DateToDatetimeTransformer('d/m/Y'))
            )		
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

            $builder->addEventListener(FormEvents::SUBMIT, array($this,'onSubmit'));
            $builder->addEventListener(FormEvents::PRE_SUBMIT, array($this,'onPreSubmit'));
    }

    public function onPreSubmit(FormEvent $event)
    {
        $data = $event->getData();

        dump($data);
    }

    public function onSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        if($date = $data->getStartDate()){
            if($date < new \DateTime('now')) $form->get('startdate')->addError(new FormError("Cette date doit être dans le futur..."));
        }

        if($date = $data->getEndDate()){
            if($date < new \DateTime('now')) $form->get('enddate')->addError(new FormError("Cette date doit être dans le futur..."));
            if($date < $data->getStartDate()) $form->get('enddate')->addError(new FormError("Cette date doit être après la date de début..."));
        }  

        if($data->getStartDate() && $data->getEndDate() && $data->getMonday()==false && $data->getTuesday()==false && $data->getWednesday()==false && $data->getThursday()==false && $data->getFriday()==false && $data->getSaturday()==false && $data->getSunday()==false){
            $form->get('enddate')->addError(new FormError("Choississez un ou plusieurs jours de la semaine..."));
        }        

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