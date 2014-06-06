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

class CityToLocationType extends AbstractType
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
        $builder->add('city_id','hidden',array(
                'required' => false,
                ))
                ->add('city_name','text',array(
                'required' => false,
                ))
            //->addModelTransformer(new CityToLocationTransformer($this->em))
            ;

       $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onPreSetData'));
       $builder->addEventListener(FormEvents::PRE_SUBMIT, array($this, 'onPreSubmit'));
    }

    public function onPreSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $location = $event->getData(); 

    }

    public function onPreSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        $location = null;
        if(!empty($data['city_id'])) $location = $this->em->getRepository('MyWorldBundle:location')->findLocationByCityId($data['city_id']);
        elseif(!empty($data['city_name'])) $location = $this->em->getRepository('MyWorldBundle:location')->findLocationByCityName($data['city_name']);
        
        $form->setData($location);

    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'invalid_message' => 'Form AutoCompleteCityType Error',
            'data_class' => 'My\WorldBundle\Entity\Location',
            'cascade_validation' => false
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'city_to_location_type';
    }
}
