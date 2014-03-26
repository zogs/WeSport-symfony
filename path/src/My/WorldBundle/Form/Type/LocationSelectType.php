<?php

namespace My\WorldBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Doctrine\ORM\EntityManager;

use My\WorldBundle\Form\DataTransformer\StatesToLocationTransformer;

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
        $countries = $this->em->getRepository('MyWorldBundle:Country')->findCountryList();

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


        //add transformer
        //$transformer = new StatesToLocationTransformer($this->em);
        //$builder->addModelTransformer($transformer);

        $em = $this->em;

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function($event,$em) use($em) {

            $form = $event->getForm();
            $data = $event->getData();

            //reset to id of country if its a string
            if(!empty($data['country']) && is_string($data['country'])){
                $country = $em->getRepository('MyWorldBundle:Country')->findByCodeOrId($data['country']);
                $data['country'] = $country->getId();                
            }

            //find Location that fit the states data
            $location = $em->getRepository('MyWorldBundle:Location')->findLocationFromStates($data);

            //set data
            $data['id'] = $location->getId();
            $data['country'] = null;
            $data['region'] = null;
            $data['department'] = null;
            $data['district'] = null;
            $data['division'] = null;
            $data['city'] = null;
            $event->setData($data);

        

        }, 100);



        //stop further validation
        $builder->addEventListener(FormEvents::POST_SUBMIT, function($event) {
            $event->stopPropagation();
        }, 900); // Définissez toujours une priorité plus grande que le ValidationListener

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
