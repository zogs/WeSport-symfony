<?php
namespace Ws\EventsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormError;
use Symfony\Component\Routing\Router;

use Doctrine\ORM\EntityManager;

use Ws\EventsBundle\Entity\Spot;

class SpotType extends AbstractType
{

    public $em;
    public $router;
    public $errors;

    public function __construct(EntityManager $em,Router $router)
    {
        $this->em = $em;
        $this->router = $router;
        $this->errors = array();
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('spot_id','hidden',array(
                'mapped' => false,
                'required' => false,
                'attr' => array(
                    'class' => 'autocompleted-spot_id'
                    )
                ))
            ->add('spot_slug','text',array(
                'label' => "Lieu ?",
                'mapped' => false,
                'required' => false,
                'attr' => array(
                    'class' => 'autocomplete-spot',
                    'data-autocomplete-url' => $options['ajax_url'],
                    'data-template-empty' => $options['empty_html'],
                    'data-template-footer' => $options['footer_html'],
                    'data-template-header' => $options['header_html'],
                    'data-trigger-length' =>2,
                    'autocomplete' => "off",
                    )
                ))
            ->add('location', 'city_to_location_type', array(
                'label' => "Entrer la ville",
                'required' => false,
                'mapped' => false,              
                ))
            ->add('name','text',array(
                'label' => "Entrer le nom de l'endroit",
                'mapped' => false,
                'required' => false,
                ))
            ->add('address','text',array(
                'label' => "Entrer l'adresse exacte",
                'mapped' => false,
                'required' => false,
                ))
            ;
    		
            //set options array for listeners uses
            $this->options = $options;

            //listeners
            $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onPreSetData'));
            $builder->addEventListener(FormEvents::PRE_SUBMIT, array($this, 'onPreSubmit'));
            $builder->addEventListener(FormEvents::SUBMIT, array($this, 'onSubmit'));
            
    }

    public function onPreSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $spot = $event->getData();        

        if(NULL != $spot) {

            $form->add('spot_id','hidden',array(
                    'data' => $spot->getId(),
                    'mapped' => false,
                    'required' => false,
                    'attr' => array(
                        'class' => 'autocompleted-spot_id'
                        )
                    ))
                ->add('spot_slug','text',array(
                    'data' => $spot->getSlug(),
                    'mapped' => false,
                    'required' => false,
                    'attr' => array(
                        'class' => 'autocomplete-spot',
                        'data-autocomplete-url' => $this->options['ajax_url'],
                        'data-template-empty' => $this->options['empty_html'],
                        'data-template-footer' => $this->options['footer_html'],
                        'data-template-header' => $this->options['header_html'],
                        'data-trigger-length' =>2
                        )
                    ))
                ->add('location', 'city_to_location_type', array(
                    'data' => $spot->getLocation(),
                    'required' => false,
                    'mapped' => false,
                    'label' => "Ville",
                    ))
                ->add('name','text',array(
                    'data' => $spot->getName(),
                    'mapped' => false,
                    'required' => false,
                    'label' => "Nom de l'endroit",
                    ))
                ->add('address','text',array(
                    'data' => $spot->getAddress(),
                    'mapped' => false,
                    'required' => false,
                    'label' => "Adresse exacte",
                    ))
                ;
            
        }

    }

    /**
     * Set the Spot from the form date
     *    
     */
    public function onPreSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();
        
        $spot = NULL;

        dump($data);

        //if location is defined, create new spot
        if(!empty($data['location']) && (!empty($data['location']['city_id']) || !empty($data['location']['city_name']))){            
            //get location from city_id or city_name
            if(!empty($data['location']['city_id']))                
                $location = $this->em->getRepository('MyWorldBundle:location')->findLocationByCityId($data['location']['city_id']);            
            elseif(!empty($data['location']['city_name']))             
                $location = $this->em->getRepository('MyWorldBundle:location')->findLocationByCityName($data['location']['city_name']);
            //trigger an error if the city cant be find
            if(NULL == $location) $this->errors[] = array('location.city_name',"Cette ville n'existe pas...");
            //trigger an error if the name of the spot is not defined
            if(empty($data['name'])) $this->errors[] = array('name',"Le nom de l'endroit doit être rempli...");

            $spot = new Spot();
            $spot->setName($data['name']);
            $spot->setAddress($data['address']);
            $spot->setLocation($location);
            dump($spot);
        }
        //else get spot from spot_id
        elseif(!empty($data['spot_id'])){
            $spot = $this->em->getRepository('WsEventsBundle:Spot')->findOneById($data['spot_id']);   
            if(NULL == $spot) $this->errors[] = array('spot_slug',"Ce lieu n'existe pas..."); //show error on spot_slug field       
        }    
        //else from spot_slug
        elseif(!empty($data['spot_slug'])){
            $spot = $this->em->getRepository('WsEventsBundle:Spot')->findOneBySlug($data['spot_slug']);    
            if(NULL == $spot) $this->errors[] = array('spot_slug',"Ce lieu n'existe pas..."); //show error on spot_slug field
        }        
        
        else {
            $this->errors[] = array('spot_slug',"Un lieu est nécessaire...");
        }        
        
        return $form->setData($spot);   

    }

    /**
    * Triggers errors of the form
    *
    * (PRE_SUBMIT cant trigger error itself, because Symfony SUBMIT routine reset all errors at first )
    */
    public function onSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $spot = $form->getData();        
                
        //
        //set errors if exist
        if(!empty($this->errors)){
            //for each errors
            foreach ($this->errors as $k => $error) {                                
                $error_field = $error[0];                
                $error_txt = $error[1];
                //find children fields 
                $r = explode('.',$error_field);
                $field = $form;
                foreach ($r as $f) {
                    $field = $field->get($f);
                }
                //add error
                $field->addError(new FormError($error_txt));
            }
        }      
    }

    public function getName()
    {
        return 'spot_type';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
	    $resolver->setDefaults(array(
            'invalid_message' => 'Spot form error',
	        'data_class' => 'Ws\EventsBundle\Entity\Spot',
            'cascade_validation' => false,
            'ajax_url' => $this->router->generate('ws_spot_autocomplete'),
            'empty_html' => 'Pas de résultats',
            'footer_html' => '',
            'header_html' => '',
            'trigger-length' =>3
	    ));
	}
}

?>