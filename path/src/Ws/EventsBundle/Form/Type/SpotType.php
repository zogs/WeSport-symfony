<?php
namespace Ws\EventsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Routing\Router;

use Doctrine\ORM\EntityManager;

use Ws\EventsBundle\Entity\Spot;

class SpotType extends AbstractType
{

    public $em;
    public $router;

    public function __construct(EntityManager $em,Router $router)
    {
        $this->em = $em;
        $this->router = $router;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('spot_id','hidden',array(
                'mapped' => false,
                'required' => false,
                'attr' => array(
                    'class' => 'autocomplete-spot_id'
                    )
                ))
            ->add('spot_slug','text',array(
                'mapped' => false,
                'required' => false,
                'attr' => array(
                    'class' => 'autocomplete-spot',
                    'data-autocomplete-url' => $this->router->generate('ws_spot_autocomplete'),
                    )
                ))
            ->add('location', 'city_to_location_type', array(
                'required' => false,
                'mapped' => false,
                'label' => "Ville",
                ))
            ->add('name','text',array(
                'mapped' => false,
                'required' => false,
                'label' => "Nom de l'endroit",
                ))
            ->add('address','text',array(
                'mapped' => false,
                'required' => false,
                'label' => "Adresse exacte",
                ))
            ;
    		
            $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onPreSetData'));
            $builder->addEventListener(FormEvents::PRE_SUBMIT, array($this, 'onPreSubmit'));
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
                        'class' => 'autocomplete-spot_id'
                        )
                    ))
                ->add('spot_slug','text',array(
                    'data' => $spot->getSlug(),
                    'mapped' => false,
                    'required' => false,
                    'attr' => array(
                        'class' => 'autocomplete-spot',
                        'data-autocomplete-url' => $this->router->generate('ws_spot_autocomplete'),
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

    public function onPreSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();
        
        $spot = NULL;

        if(!empty($data['spot_id']))     
            $spot = $this->em->getRepository('WsEventsBundle:Spot')->findOneById($data['spot_id']);
        
        if(NULL!=$spot) return $form->setData($spot);
        
        if(!empty($data['spot_slug']))
            $spot = $this->em->getRepository('WsEventsBundle:Spot')->findOneBySlug($data['spot_slug']);
            
        if(NULL!=$spot) return $form->setData($spot);

        if(!empty($data['location']) && (!empty($data['name'] || !empty($data['address'])))){

            $spot = new Spot();
            $spot->setName($data['name']);
            $spot->setAddress($data['address']);

            if(!empty($data['location']['city_id'])) $location = $this->em->getRepository('MyWorldBundle:location')->findLocationByCityId($data['location']['city_id']);
            elseif(!empty($data['location']['city_name'])) $location = $this->em->getRepository('MyWorldBundle:location')->findLocationByCityName($data['location']['city_name']);
            
            $spot->setLocation($location);
            $form->setData($spot);
        }

        if(NULL==$spot) throw new Exception('Spot entity can not be instanciate in SpotType');

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
	    ));
	}
}

?>