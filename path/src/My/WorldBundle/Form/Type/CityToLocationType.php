<?php

namespace My\WorldBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormError;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

use My\WorldBundle\Entity\Location;

use My\WorldBundle\Form\DataTransformer\CityIdToLocationTransformer;
use My\WorldBundle\Form\DataTransformer\CityNameToLocationTransformer;
use My\WorldBundle\Form\DataTransformer\CityToLocationTransformer;

class CityToLocationType extends AbstractType
{
    private $em;
    private $router;
    private $form_filled = false;
    private $options;
    private $location = null;


    /**
     * @param EntityManager $em
     * @param Router $router
     */
    public function __construct(EntityManager $em, Router $router)
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

        $builder->add('city_id','hidden',array(
                'required' => false,
                'attr' => array(
                    'class' => 'city-id-autocompleted'
                    )
                ))
                ->add('city_name','text',array(
                    'attr'=>array(
                        'class' => 'city-autocomplete',
                        'size' => strlen($options['placeholder']),
                        'data-autocomplete-url' => $options['ajax_url'],
                        'data-template-empty' => '<div class="tt-city-noresult">'.$options['empty_html'].'</div>',
                        'data-template-footer' => '<div class="tt-city-footer">'.$options['footer_html'].'</div>',
                        'data-template-header' => '<div class="tt-city-header">'.$options['header_html'].'</div>',
                        'data-trigger-length' =>2,
                        'autocomplete' => "off"
                        )
                ))
                
            ;

        $this->options = $options;
        $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onPreSetData'));
        $builder->addEventListener(FormEvents::PRE_SUBMIT, array($this, 'onPreSubmit'));
        $builder->addEventListener(FormEvents::SUBMIT, array($this, 'onSubmit'));
    }

    /**
     * Pre-filled the form if Location is set
     */
    public function onPreSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $location = $event->getData();

        $this->addFormFields($location,$form);      
    }

    


    /**
     * Find the Location object from the submitted data
     * Add fields to form
     * 
     * @param FormEvent $event
     */
    public function onPreSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();   

        $loc = null;
        if(!empty($data['city_name'])){
            $loc = $this->em->getRepository('MyWorldBundle:location')->findLocationByCityName($data['city_name']);
        }  
        elseif(!empty($data['city_id'])){
            $loc = $this->em->getRepository('MyWorldBundle:location')->findLocationByCityId($data['city_id']);
        }          

        $this->location = $loc;

        $this->addFormFields($loc,$form);            
    }
    
    /**
     * Set the Location to the Form 
     * Set a FormError if the city can't be find
     * 
     * @param FormEvent $event
     */
    public function onSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        
        $event->setData($this->location);       

        if(!$form->isEmpty()){
            if(null == $this->location){
                $form->get('city_name')->addError(new FormError("Cette ville ne semble pas exister ..."));
            }
        }

    }

    /**
     * Add fields to the form depending to the Location
     */
    private function addFormFields($location,$form)
    {
        if(null == $location) return false;
                
        if($location instanceof Location && $location->exist()){
            //add size parameter to ajust the length of the input
            $form->add('city_name','text',array(
                    'attr'=>array(
                        'class' => 'city-autocomplete',
                        'data' => $location->getCity()->getName(),
                        'size' => strlen($location->getCity()->getName()),
                        'data-autocomplete-url' => $this->options['ajax_url'],
                        'data-template-empty' => '<div class="tt-city-noresult">'.$this->options['empty_html'].'</div>',
                        'data-template-footer' => '<div class="tt-city-footer">'.$this->options['footer_html'].'</div>',
                        'data-template-header' => '<div class="tt-city-header">'.$this->options['header_html'].'</div>',
                        'data-trigger-length' =>2,
                        'autocomplete' => "off"
                        )
            ));
        }
    }
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'My\WorldBundle\Entity\Location',
            'invalid_message' => 'Form AutoCompleteCityType Error',
            'cascade_validation' => false,
            'ajax_url' => $this->router->generate('my_world_autocompletecity'),
            'empty_html' => 'Pas de résultats',
            'footer_html' => '',
            'header_html' => '',
            'trigger-length' =>3,
            'placeholder' => "Votre ville?",


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
