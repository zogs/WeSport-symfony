<?php

namespace Ws\SportsBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class SportAdmin extends Admin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name', 'text', array('label' => 'Nom du sport'))
            ->add('slug', 'text', array('label' => 'Slug'))
            ->add('icon')
            ->add('action')
            ->add('category', 'entity', array('class' => 'Ws\SportsBundle\Entity\Category'))
        ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
            ->add('category')
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name','string',array('template'=>'MyUtilsBundle:Administration:list_sport_icon.html.twig'))
            ->add('slug')
            ->add('category',null,array('associatied_property'=>'name'))
        ;
    }

    public function getParentAssociationMapping()
    {
        return 'category';
    }
}