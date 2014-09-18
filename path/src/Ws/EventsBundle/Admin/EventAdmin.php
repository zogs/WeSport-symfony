<?php

namespace Ws\EventsBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

use Ws\EventsBundle\Entity\Event;

class EventAdmin extends Admin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper


        ->add('title',null,array(
            'label'=>'Titre'
            ))
        ->add('sport',null,array('property'=>'name','attr'=>array()))
        ->add('level','choice',array(
            'multiple'=> false,
            'expanded' => false,
            'required' => true,
            'choices' => Event::$valuesAvailable['level'],
            ))
        ->add('date','date')
        ->add('time','time')

        ->add('location',null,array('property'=>'city.name','label'=>"Ville"))
        ->add('address','text',array())

        ->add('description','text',array('required'=>false))
        ->add('nbmin','integer',array('required'=>false))
        ->add('phone','text',array('required'=>false))
        ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title',null,array('label'=>'Titre'))
            ->add('organizer',null)
            


                     
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('sport',null,array('associated_property'=>'name','template'=>'MyUtilsBundle:Administration:list_sport_icon.html.twig'))
            ->addIdentifier('title',null,array('label'=>'Titre'))            
            ->add('date_depot',null,array('label'=>'Déposé le'))
            ->add('organizer',null,array('label'=>'Par','template'=>'MyUtilsBundle:Administration:list_user_avatar.html.twig'))
            ->add('date',null,array('label'=>"A lieu le"))
            ->add('location',null,array('associated_property' => 'city.name','label'=>'Ville'))
        ;
    }

}