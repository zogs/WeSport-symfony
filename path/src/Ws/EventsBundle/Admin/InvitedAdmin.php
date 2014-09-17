<?php

namespace Ws\EventsBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

use Ws\EventsBundle\Entity\Invited;

class InvitedAdmin extends Admin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('id')
            ->add('invitation',null,array('property'=>'event.title'))
            ->add('user',null,array('property'=>'username'))
            ->add('email')
            ->add('date')
            ->add('nb_sended')
            ->add('response')
            ->add('date_response')
        ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')

        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->addIdentifier('invitation',null,array('associated_property'=>'event.title','label'=>"Invité à"))
            ->addIdentifier('user',null,array('associated_property'=>'username','label'=>"Utilisateur enregistré"))         
            ->add('email')
            ->add('date')
            ->add('nb_sended')
            ->add('response')
            ->add('date_response')
        ;
    }

}