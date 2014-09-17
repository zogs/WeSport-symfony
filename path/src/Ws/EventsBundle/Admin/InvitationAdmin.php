<?php

namespace Ws\EventsBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

use Ws\EventsBundle\Entity\Invited;

class InvitationAdmin extends Admin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('id')
            ->add('event',null,array('property'=>'title'))
            ->add('inviter',null,array('property'=>'username'))
            ->add('name')
            ->add('invited',null,array('property'=>'email'))
            ->add('date')
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
            ->addIdentifier('event',null,array('associated_property'=>'title','label'=>"Invité à"))
            ->addIdentifier('inviter',null,array('associated_property'=>'username','label'=>"Inviteur"))         
            ->add('name',null,array('label'=>"Nom de la liste d'invité"))
            ->add('date')
        ;
    }

}