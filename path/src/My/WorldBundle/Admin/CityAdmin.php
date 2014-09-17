<?php

namespace My\WorldBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

use My\WorldBundle\Entity\Location;

class CityAdmin extends Admin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper   
            ->add('fullnamed',null,array('label'=>'Name'))
            ->add('lc',null,array('label'=>'Lang'))
            ->add('cc1',null,array('label'=>'Country code'))
            ->add('adm1')
            ->add('adm2')
            ->add('adm3')
            ->add('adm4')
            ->add('latitude')
            ->add('longitude')
            ->add('cp',null,array('label'=>'Code postal'))
            ->add('pop',null,array('label'=>'Population'))
            ->add('sfc',null,array('label'=>'Surface'))
                        
        ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('fullnamed',null,array('label'=>'Name'))
            ->add('lc',null,array('label'=>'Lang'))
            ->add('cc1',null,array('label'=>'Country code'))
            ->add('adm1')
            ->add('adm2')
            ->add('adm3')
            ->add('adm4')
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->addIdentifier('name')
            ->add('cc1',null,array('label'=>'Country code'))
            ->add('dsg')
            ->add('adm1')
            ->add('adm2')
            ->add('adm3')
            ->add('adm4')
            ->add('pop',null,array('label'=>'Population'))
            ->add('lc',null,array('label'=>'Lang'))
        ;
    }

}