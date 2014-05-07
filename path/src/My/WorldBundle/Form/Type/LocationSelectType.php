<?php

namespace My\WorldBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Doctrine\ORM\EntityManager;


class LocationSelectType extends AbstractType
{
    public $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        
    }




    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $em = $this->em;

        $countries = $em->getRepository('MyWorldBundle:Country')->findCountryList();

        $builder   
            ->add('id','hidden',array(
                'required'=>true,
                'mapped'=>true,
                'data'=>'0'
                ))         
            ->add('country','choice',array(
                'choices'=>$countries,
                'required'=>false,
                'mapped'=>false,
                'empty_value'=>'Votre pays',
                'attr'=>array('class'=>'geo-select geo-select-ajax','data-geo-level'=>'country','data-icon'=>'globe'),

                ))
            ->add('region','choice',array(
                'choices'=>array(),
                'required'=>false,
                'mapped'=>false,
                'empty_value'=>'Votre région',
                'attr'=>array('class'=>'geo-select geo-select-ajax hide','data-geo-level'=>'region','data-icon'=>'globe'),
                
                ))
            ->add('department','choice',array(
                'choices'=>array(),
                'required'=>false,
                'mapped'=>false,
                'empty_value'=>'Votre Département',
                'attr'=>array('class'=>'geo-select geo-select-ajax hide','data-geo-level'=>'department','data-icon'=>'globe'),
                
                ))
            ->add('district','choice',array(
                'choices'=>array(),
                'required'=>false,
                'mapped'=>false,
                'empty_value'=>'Votre district',
                'attr'=>array('class'=>'geo-select geo-select-ajax hide','data-geo-level'=>'district','data-icon'=>'globe'),
                
                ))
            ->add('division','choice',array(
                'choices'=>array(),
                'required'=>false,
                'mapped'=>false,
                'empty_value'=>'Votre division',
                'attr'=>array('class'=>'geo-select geo-select-ajax hide','data-geo-level'=>'division','data-icon'=>'globe'),
                
                ))
            ->add('city','choice',array(
                'choices'=>array(),
                'required'=>false,
                'mapped'=>false,
                'empty_value'=>'Votre ville',
                'attr'=>array('class'=>'geo-select geo-select-ajax hide','data-geo-level'=>'city','data-icon'=>'globe'),
                
                ))                                                                     
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onPreSetData'));

        $builder->addEventListener(FormEvents::PRE_SUBMIT, array($this, 'onPreSubmit'));
        

    }
    
    /*
        
        Before persist, find and replace with the adequate Location

     */
    public function onPreSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        //persist null if no country is submitted
        if(empty($data['country'])){
            $form->setData(null);
            return;
        }

        //find and replace the country field
        if(!empty($data['country']) && is_string($data['country'])){
            $country = $this->em->getRepository('MyWorldBundle:Country')->findByCodeOrId($data['country']);
            $data['country'] = $country->getId();                
        }

        //find Location that fit the form data
        $location = $this->em->getRepository('MyWorldBundle:Location')->findLocationFromStates($data);

        //replace with the  object location
        $form->setData($location);

        //add all relevant geo field to render view
        $this->addGeoFields($form, $location);
    }

    public function onPreSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $location = $event->getData();        
        $this->addGeoFields($form, $location);
    }    

    public function addGeoFields(FormInterface $form, $location)
    {
        if($location == NULL) return;
        if($location->getCountry() != NULL) $this->addGeoField($form, $location, 'country', $location->getCountry()->getCode());                        
        if($location->getRegion() != NULL) $this->addGeoField($form, $location, 'region', $location->getRegion()->getId());            
        if($location->getDepartement() != NULL) $this->addGeoField($form, $location, 'department', $location->getDepartement()->getId());            
        if($location->getDistrict() != NULL) $this->addGeoField($form, $location, 'district', $location->getDistrict()->getId());            
        if($location->getDivision() != NULL) $this->addGeoField($form, $location, 'division', $location->getDivision()->getId());            
        if($location->getCity() != NULL) $this->addGeoField($form, $location, 'city', $location->getCity()->getId());
    }

    public function addGeoField(FormInterface $form, $location, $level, $value = '')
    {        
        $list = $this->em->getRepository('MyWorldBundle:Location')->findStatesListByLocationLevel($location,$level);
        if(empty($list)) return;

        $form->add($list['level'],'choice',array(
                'choices'=>$list['list'],
                'required'=>false,
                'mapped'=>false,
                'empty_value'=>'Votre '.$list['level'],
                'attr'=>array('class'=>'geo-select geo-select-'.$list["level"].' geo-select-ajax','data-geo-level'=>'country','data-icon'=>'globe'),
                'data'=>$value
                ));
    }
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'invalid_message' => 'humm cest pas encore ça...',
            'data_class' => 'My\WorldBundle\Entity\Location',
            'cascade_validation' => false,
            'validation_groups' => false,
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'location_selectboxs';
    }
}
