<?php

namespace Ws\EventsBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

use Ws\EventsBundle\Entity\Event;

class SearchAdmin extends Admin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('user',null,array(),array('placeholder'=>"Utilisateur non enregistré"))
            ->add('location',null,array('property'=>'city.name'))
            ->add('area')
            ->add('type','choice',array('choices'=>Event::$valuesAvailable['type'],'multiple'=>true,'expanded'=>true,'required'=>true))
            ->add('price')
             ->add('level','choice',array('multiple' => true,'expanded' => true,'required' => false,'choices' => Event::$valuesAvailable['level'] ))
            ->add('organizer',null,array('property'=>'username'),array('placeholder'=>'Personne'))
        ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('location')

        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->addIdentifier('user')
            ->add('location',null,array('associated_property'=>'city.name','label'=>"Ville"))
            ->add('area')
            ->add('type','array')
            ->add('price')            
        ;
    }

}