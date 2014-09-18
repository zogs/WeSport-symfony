<?php

namespace My\WorldBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

use My\WorldBundle\Entity\Location;

class LocationAdmin extends Admin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper            
                        
        ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('country.name')
            ->add('region.name')
            ->add('departement.name')
            ->add('district.name')
            ->add('division.name')
            ->add('city.fullnamed')
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('country',null,array('associated_property'=>'name'))
            ->add('region',null,array('associated_property'=>'name'))
            ->add('city',null,array('associated_property'=>'name'))
        ;
    }

}