<?php

namespace My\UserBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

use My\UserBundle\Entity\User;

class UserAdmin extends Admin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('username')
            ->add('email')
            ->add('enabled')
            ->add('salt')
            ->add('password')            
            ->add('type','choice',array('multiple'=>false,'expanded'=>true,'choices'=>array(
                    'person'=>"Particulier",
                    'asso'=>"Association",
                    'pro'=>"Professionel"
                    )))
            ->add('firstname')
            ->add('lastname')
            ->add('gender')
            ->add('birthday')
            ->add('description')
            ->add('avatar',null,array('property'=>'id'))
            ->add('settings',null,array('property'=>'id'))
            ->add('statistic',null,array('property'=>'id'))

        ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('username')
            ->add('email')     
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('username')
            ->add('email')
            ->add('firstname')
            ->add('lastname')
            ->add('age')
            ->add('birthday')
            ->add('gender')
            ->add('location',null,array('associated_property'=>'city.name'))
            ->add('register_since')
            ->add('lastLogin')
        ;
    }

}