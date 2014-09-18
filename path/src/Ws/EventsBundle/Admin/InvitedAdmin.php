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
            ->add('invitation',null,array('associated_property'=>'event.title','label'=>"Invité à"))
            ->add('invitation.event.sport',null,array("label"=>"Sport",'template'=>'MyUtilsBundle:Administration:list_sport_icon.html.twig'))
            ->add('user',null,array('associated_property'=>'username','label'=>"Utilisateur enregistré",'template'=>'MyUtilsBundle:Administration:list_user_avatar.html.twig'))      
            ->add('email')
            ->add('date')
            ->add('nb_sended')
            ->add('response')
            ->add('date_response')
        ;
    }

}