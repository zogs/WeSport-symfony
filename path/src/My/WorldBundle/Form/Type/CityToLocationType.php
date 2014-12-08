<?php

namespace My\WorldBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

use My\WorldBundle\Entity\Location;

use My\WorldBundle\Form\DataTransformer\CityIdToLocationTransformer;
use My\WorldBundle\Form\DataTransformer\CityNameToLocationTransformer;
use My\WorldBundle\Form\DataTransformer\CityToLocationTransformer;

class CityToLocationType extends AbstractType
{
    public $em;
    public $router;


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
                        'data-autocomplete-url' => $options['ajax_url'],
                        'data-template-empty' => '<div class="tt-city-noresult">'.$options['empty_html'].'</div>',
                        'data-template-footer' => '<div class="tt-city-footer">'.$options['footer_html'].'</div>',
                        'data-template-header' => '<div class="tt-city-header">'.$options['header_html'].'</div>',
                        'data-trigger-length' =>2,
                        'autocomplete' => "off"
                        )
                ))
            ;

       $builder->addEventListener(FormEvents::PRE_SUBMIT, array($this, 'onPreSubmit'));
    }


    /**
     * onPreSubmit
     * 
     * Find and fill the form with the Location object, with the city_id if defined, or the city_name is defined
     * 
     * @param FormEvent $event
     */
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
            'cascade_validation' => false,
            'ajax_url' => $this->router->generate('my_world_autocompletecity'),
            'empty_html' => 'Pas de résultats',
            'footer_html' => '',
            'header_html' => '',
            'trigger-length' =>3

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
