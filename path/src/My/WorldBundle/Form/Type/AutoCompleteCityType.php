<?php

namespace My\WorldBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

use My\WorldBundle\Form\DataTransformer\CityIdToLocationTransformer;
use My\WorldBundle\Form\DataTransformer\CityNameToLocationTransformer;
use My\WorldBundle\Form\DataTransformer\CityToLocationTransformer;

class AutoCompleteCityType extends AbstractType
{
    public $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /*
        $builder
            ->add('city_id','hidden',array(
                'required'=>false,
                'mapped' => false
                ))->addModelTransformer(new CityToLocationTransformer($this->em))
            ->add('city_name','text',array(
                'required'=>false, 
                'label'=>'Ville',
                'mapped' => false
                ))                                                   
        ;
        */
        $builder->add('city_id','text',array(
                'required' => false,
                ))
                ->add('city_name','text',array(
                'required' => false,
                ))
            //->addModelTransformer(new CityToLocationTransformer($this->em))
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
            'invalid_message' => 'Form AutoCompleteCityType Error',
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
