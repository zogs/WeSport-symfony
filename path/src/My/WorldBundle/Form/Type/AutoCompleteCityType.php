<?php

namespace My\WorldBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class AutoCompleteCityType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('city_id','hidden',array(
                'required'=>false
                ))
            ->add('city_name','text',array(
                'required'=>false, 
                'label'=>'Ville'
                ))                                                   
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onPreSetData'));
    }

    public function onPreSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $location = $event->getData(); 

        if(isset($location) && $location->hasCity()){
            $data = array();
            $data['city_id'] = $location->getCity()->getId();
            $data['city_name'] = $location->getCity()->getName();
            $event->setData($data);
        }
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => null,
            'cascade_validation' => false
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'auto_complete_city';
    }
}
