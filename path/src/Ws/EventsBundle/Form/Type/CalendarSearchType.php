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
use My\UtilsBundle\Form\DataTransformer\TimeToDatetimeTransformer;


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
                'choices'=> Event::$valuesAvailable['type'],
                'multiple'=>true,
                'expanded'=>false,
                'required'=>false,   
                'translation_domain' => 'WsEventsBundle_event'             
                ))
            ->add('price','choice',array(
                'required'=>false,
                'expanded'=>false,
                'multiple'=>false,
                'choices'=> array(
                    0 => 'gratuite',
                    10 => 'moins de 10€',
                    25 => 'moins de 25€',
                    50 => 'moins de 50€',
                    )
                ))
            ->add('sports','sport_select_multiple',array()
            )       
            ->add('level','choice',array(
                'multiple' => true,
                'expanded' => false,
                'required' => false,
                'choices' => Event::$valuesAvailable['level'],
                'translation_domain' => 'WsEventsBundle_event'
                ))
            ->add($builder->create('timestart','text',array(                
                'required' => false,
                ))->addModelTransformer(new TimeToDatetimeTransformer('H:i'))
            )
            ->add($builder->create('timeend','text',array(
                'required' => false,
                ))->addModelTransformer(new TimeToDatetimeTransformer('H:i'))
            )
            ->add('dayofweek','choice',array(
                'choices'=>array('Monday'=>'days.day.monday','Tuesday'=>'days.day.tuesday','Wednesday'=>'days.day.wednesday','Thursday'=>'days.day.thursday','Friday'=>'days.day.friday','Saturday'=>'days.day.saturday','Sunday'=>'days.day.sunday'),
                'multiple'=>true,
                'expanded'=>false,
                'required'=>false,   
                'translation_domain' => 'MyUtilsBundle'             
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

        if(null !== $search){
            //merge sports entity to em that were detach by the redirection
            if($search->hasSports()){
                $a = array();
                foreach ($search->getSports() as $key => $sport) {
                    $sport = $this->em->merge($sport);
                    $a[] = $sport;
                }
                $search->setSports($a);
            }
        } 
    }

    public function onPreSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        //if no data is submitted, return 
        if(empty($data)) return;        
        //reset search params ( dont reset cookies with false)
        $this->manager->resetParams(false);
        //add submitted  search params
        $this->manager->addParams($data);
        //compute params and prepare Search object
        $this->manager->prepareParams();
        //get Search object
        $search = $this->manager->getSearch();

        //set user to Search
        if(is_a($this->user,'My\UserBundle\Entity\User')){
            $search->setUser($this->user);            
        }        

        //return
        $form->setData($search);

    }

    public function onPostSubmit(FormEvent $event)
    {    

    }

    public function getName()
    {
        return 'calendar_search';
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