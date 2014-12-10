<?php
namespace Ws\EventsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Doctrine\ORM\EntityManager;

use Ws\EventsBundle\Entity\Event;
use Ws\EventsBundle\Manager\CalendarManager;
use Ws\SportsBundle\Form\Type\SelectSportType;
use My\WorldBundle\Form\Type\AutoCompleteCityType;


class CalendarSearchType extends AbstractType
{
    private $manager;
    private $search;
    private $em;

    public function __construct(CalendarManager $manager,SecurityContext $secu, EntityManager $em)
    {
        $this->manager = $manager;
        $this->user = $secu->getToken()->getUser();
        $this->em = $em;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('location', 'city_to_location_type', array(
                'required'=>true,
                ))
            ->add('area','choice',array(
                'choices'=> array(10=>'10km',20=>'20km',50=>'50km',100=>'100km'),
                'multiple'=>false,
                'expanded'=>false,
                'required'=>false,
                ))
            ->add('type','choice',array(
                'choices'=>Event::$valuesAvailable['type'],
                'multiple'=>true,
                'expanded'=>true,
                'required'=>true,                
                ))
            ->add('price','text',array(
                'required'=>false,
                ))
            ->add('sports','entity',array(                                    
                    'class'=>'WsSportsBundle:Sport',
                    'label'=>'Sport',
                    'property'=>'name',
                    'expanded'=>false,
                    'multiple' => true,
                    'mapped' => true,
                    'group_by' => 'category',
                    'required' => false,
                    'attr'=>array('class'=>'sportSelection','multiple'=>true,'data-placeholder'=>'Choississez un ou plusieurs sports')
                    )
            )       
            ->add('level','choice',array(
                'multiple' => true,
                'expanded' => false,
                'required' => false,
                'choices' => Event::$valuesAvailable['level'],
                ))
            ->add('timestart','time',array(
                'widget'=>'choice',
                'input'=>'string',
                'with_seconds'=>false,
                'hours'=>array(5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,0),
                'minutes'=>array(0),
                'empty_value' => "A partir de:",
                'required' => false,
                ))
            ->add('timeend','time',array(
                'widget'=>'choice',
                'input'=>'string',
                'with_seconds'=>false,
                'hours'=>array(5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,0),
                'minutes'=>array(0),
                'empty_value' => "Avant :",
                'required' => false,
                ))
            ->add('dayofweek','choice',array(
                'choices'=>array('Monday'=>'Lundi','Tuesday'=>'Mardi','Wednesday'=>'Mercredi','Thursday'=>'Jeudi','Friday'=>'Vendredi','Saturday'=>'Samedi','Sunday'=>'Dimanche'),
                'multiple'=>true,
                'expanded'=>false,
                'required'=>false,
                ))
            ->add('organizer','entity',array(
                'class' => 'MyUserBundle:User',
                'label' => 'organizer',
                'property'=>'username',
                'expanded'=>false,                
                'required'=>false,
            
                ))        
    		;

            $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onPreSetData'));
            $builder->addEventListener(FormEvents::PRE_SUBMIT, array($this, 'onPreSubmit'));
            $builder->addEventListener(FormEvents::POST_SUBMIT, array($this, 'onPostSubmit'));

    }

    public function onPreSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $search = $event->getData(); 

        //for Type field, set key values as array
        if(is_array($search->getType()))  $search->setType(array_keys($search->getType()));
        
    }

    public function onPreSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        //reset search params ( dont reset cookies with false)
        $this->manager->resetParams(false);
        //add submitted  search params
        $this->manager->addParams($data);
        //compute params and prepare Search object
        $this->manager->prepareParams();
        //get Search object
        $search = $this->manager->getSearch();

        //set user to Search
        $search->setUser($this->user);
       
        //return
        $form->setData($search);

    }

    public function onPostSubmit(FormEvent $event)
    {    

    }

    public function getName()
    {
        return 'calendar_search_type';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
	    $resolver->setDefaults(array(
            //'invalid_message' => 'error in search type form',
	        'data_class' => 'Ws\EventsBundle\Entity\Search',
            'cascade_validation' => true,
	    ));
	}
}

?>