<?php

namespace Ws\EventsBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

use Ws\EventsBundle\Entity\Event;

class ParticipationAdmin extends Admin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('id')
            ->add('event',null,array('property'=>'title'))
            ->add('user',null,array('property'=>'username'))
            ->add('invited',null,array('property'=>'email'))
        ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('user',null,array('property'=>'username'))

        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('user',null,array('template'=>'MyUtilsBundle:Administration:list_user_avatar.html.twig'))
            ->add('event',null,array('associated_property'=>'title'))    
            ->add('event.sport',null,array("label"=>"Sport",'template'=>'MyUtilsBundle:Administration:list_sport_icon.html.twig'))
        ;
    }

}