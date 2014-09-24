<?php
namespace Ws\EventsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Routing\Router;

use Doctrine\ORM\EntityManager;

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
                'label' => "Ville",
                ))
            ->add('name','text',array(
                'mapped' => true,
                'required' => false,
                'label' => "Nom de l'endroit",
                ))
            ->add('address','text',array(
                'mapped' => true,
                'required' => false,
                'label' => "Adresse exacte",
                ))
            ;
    		
            
            $builder->addEventListener(FormEvents::PRE_SUBMIT, array($this, 'onPreSubmit'));
            $builder->addEventListener(FormEvents::POST_SUBMIT, array($this, 'onPostSubmit'));
    }

    public function onPreSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        if(!empty($data['spot_id'])) {
        
            $spot = $this->em->getRepository('WsEventsBundle:Spot')->findById($data['city_id']);
            $form->setData($spot);
        }
        else if(!empty($data['spot_slug'])) {

            $spot = $this->em->getRepository('WsEventsBundle:Spot')->findBySlug($data['spot_slug']);
            $form->setData($spot);
        }
        
    }

     public function onPostSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $spot = $event->getData();

        $spot->setSlug($spot->getLocation()->getCity()->getName().' '.$spot->getName().' '.$spot->getAddress());
        $spot->setCountryCode($spot->getLocation()->getCountry()->getCode());        

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
            'cascade_validation' => true,
	    ));
	}
}

?>