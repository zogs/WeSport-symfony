<?php

namespace Ws\EventsBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

use Ws\EventsBundle\Entity\Event;

class AlertAdmin extends Admin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('email')
            ->add('frequency','choice',array('choices'=>array('daily'=>'Tous les jours','weekly'=>'Une fois par semaine'),'expanded'=> true,'multiple'=> false,'required'=> true ))
            ->add('duration')
            ->add('search',null,array('property'=>'id'))
        ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('email')
            ->add('frequency')
            ->add('duration')            
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('user.username',null,array('label'=>'User'))
            ->addIdentifier('email')
            ->add('frequency')
            ->add('duration')     
            ->addIdentifier('search',null,array('associated_property' => 'id') ) 
        ;
    }

}