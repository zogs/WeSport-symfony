<?php

namespace My\WorldBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

use My\WorldBundle\Entity\Location;

class StateAdmin extends Admin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper   
            ->add('name')
            ->add('lang')         
            ->add('cc1',null,array('label'=>'Country code'))
            ->add('adm_code',null,array('label'=>'State code'))
            ->add('adm_parent',null,array('label'=>'Parent code','required'=>false))
            ->add('dsg',null,array('label'=>'Administrative Level'))
                        
        ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
            ->add('lang')
            ->add('cc1',null,array('label'=>'Country code'))
            ->add('adm_code',null,array('label'=>'State code'))
            ->add('adm_parent',null,array('label'=>'Parent state code'))
            ->add('uni',null,array('label'=>'UNI code'))
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->addIdentifier('name')
            ->add('adm_code')
            ->add('lang')
        ;
    }

}