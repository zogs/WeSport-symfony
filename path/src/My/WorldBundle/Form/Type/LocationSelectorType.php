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




class LocationSelectorType extends AbstractType
{
    public $em;
    private $router;
    private $options;

    public function __construct(EntityManager $em,Router $router)
    {
        $this->em = $em;
        $this->router = $router;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $em = $this->em;

        $builder   
            ->add('country','choice',array(
                'choices'=>$this->em->getRepository('MyWorldBundle:Country')->findCountryList('code'),
                'required'=>false,
                'mapped'=>false,
                'label' => 'world.city.label',
                'empty_value'=> 'world.country.placeholder',
                'attr'=>array('class'=>'geo-select geo-select-country geo-select-ajax','data-geo-level'=>'country','data-icon'=>'globe','data-ajax-url'=>$options['ajax_url'],'style'=>"width:100%"),

                ))
            ->add('region','choice',array(
                'choices'=>array(),
                'required'=>false,
                'mapped'=>false,
                'label' => 'world.city.label',
                'empty_value'=> 'world.region.placeholder',
                'attr'=>array('class'=>'geo-select geo-select-region geo-select-ajax hide','data-geo-level'=>'region','data-icon'=>'globe','data-ajax-url'=>$options['ajax_url'],'style'=>"width:100%"),
                
                ))
            ->add('departement','choice',array(
                'choices'=>array(),
                'required'=>false,
                'mapped'=>false,
                'label' => 'world.city.label',
                'empty_value'=> 'world.departement.placeholder',
                'attr'=>array('class'=>'geo-select geo-select-departement geo-select-ajax hide','data-geo-level'=>'departement','data-icon'=>'globe','data-ajax-url'=>$options['ajax_url'],'style'=>"width:100%"),
                
                ))
            ->add('district','choice',array(
                'choices'=>array(),
                'required'=>false,
                'mapped'=>false,
                'label' => 'world.city.label',
                'empty_value'=> 'world.district.placeholder',
                'attr'=>array('class'=>'geo-select geo-select-district geo-select-ajax hide','data-geo-level'=>'district','data-icon'=>'globe','data-ajax-url'=>$options['ajax_url'],'style'=>"width:100%"),
                
                ))
            ->add('division','choice',array(
                'choices'=>array(),
                'required'=>false,
                'mapped'=>false,
                'label' => 'world.city.label',
                'empty_value'=> 'world.division.placeholder',
                'attr'=>array('class'=>'geo-select geo-select-division geo-select-ajax hide','data-geo-level'=>'division','data-icon'=>'globe','data-ajax-url'=>$options['ajax_url'],'style'=>"width:100%"),
                
                ))
            ->add('city','choice',array(
                'choices'=>array(),
                'required'=>false,
                'mapped'=>false,
                'label' => 'world.city.label',
                'empty_value'=> 'world.city.placeholder',
                'attr'=>array('class'=>'geo-select geo-select-city geo-select-ajax hide','data-geo-level'=>'city','data-icon'=>'globe','data-ajax-url'=>$options['ajax_url'],'style'=>"width:100%"),
                
                ))             
        ;

        $this->options = $options;
        $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onPreSetData'));
        $builder->addEventListener(FormEvents::PRE_SUBMIT, array($this, 'onPreSubmit'));
        $builder->addEventListener(FormEvents::SUBMIT, array($this, 'onSubmit'));
        $builder->addEventListener(FormEvents::POST_SUBMIT, array($this, 'onPostSubmit'));
      
    }

    public function onPreSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $location = $event->getData();         

        //dynamilcaly fill fields     
        $this->addGeoFields($form, $location);

    }    
    

    public function onPreSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData(); 

        //replace country code by country id
        if(isset($data['country']) && !is_numeric($data['country'])) $data['country'] = $this->em->getRepository('MyWorldBundle:Country')->findCountryIdFromCode($data['country']);

        //get or create the Location object
        $this->location = $this->em->getRepository('MyWorldBundle:Location')->findLocationFromStates($data);

        //dynamilcaly fill fields   
        $this->addGeoFields($form, $this->location);

    }

    public function onSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        //set the Location to the form Data
        $event->setData($this->location);
        
    }

    public function onPostSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

    }

    //add fields that fits the Location
    public function addGeoFields(FormInterface $form, $location)
    {        
        if(is_object($location) && $location->isNull()) return;        
        if(NULL == $location) return;     
           
        if($location->getCountry()!=NULL) $this->addGeoField($form, $location, 'country', $location->getCountry()->getCode()); 
        if($location->getRegion()!=NULL) $this->addGeoField($form, $location, 'region', $location->getRegion()->getId());            
        if($location->getDepartement()!=NULL) $this->addGeoField($form, $location, 'departement', $location->getDepartement()->getId());
        if($location->getDistrict()!==NULL) $this->addGeoField($form, $location, 'district', $location->getDistrict()->getId());            
        if($location->getDivision()!==NULL) $this->addGeoField($form, $location, 'division', $location->getDivision()->getId());            
        if($location->getCity()!==NULL) $this->addGeoField($form, $location, 'city', $location->getCity()->getId());
    }

    //add one Location field
    public function addGeoField(FormInterface $form, $location, $level, $value = '')
    {        
        $list = $this->em->getRepository('MyWorldBundle:Location')->findStatesListFromLocationByLevel($location,$level);
        if(empty($list)) return;

        $level = $list['level']; //level can be different from the one passed to the function, so we prefer to get the level that is returned
        $choices = $list['list']; //choices of the select input

        $form->add($level,'choice',array(
                'choices'=>$choices,
                'required'=>false,
                'mapped'=>false,
                'empty_value'=>'Votre '.$level,
                'attr'=>array('class'=>'geo-select geo-select-'.$level.' geo-select-ajax','data-geo-level'=>$level,'data-icon'=>'globe','data-ajax-url'=>$this->options['ajax_url'],'style'=>"width:100%"),
                'data'=>$value,
                'label' => 'world.'.$level.'.label',
                'empty_value'=> 'world.'.$level.'.placeholder',
                ));
    }
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'My\WorldBundle\Entity\Location',
            'ajax_url' => $this->router->generate('my_world_location_select_nextlevel'), 
            'translation_domain' => 'MyWorldBundle'           
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'location_selector';
    }
}
