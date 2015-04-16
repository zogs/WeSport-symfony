<?php

namespace My\CronBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

use My\CronBundle\Form\DataTransformer\TextareaToArrayTransformer;

class CronAdmin extends Admin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name','text',array('attr'=> array('placeholder'=> "Name of the cron job")))         
            ->add($formMapper->create('commands', 'textarea')
                ->addModelTransformer(new TextareaToArrayTransformer())
            )
            ->add('interval','text',array('attr'=> array('placeholder'=> "Time in second")))         
            ;

    }

    public function prePersist($cron)
    {

    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
            ->add('interval')         
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('name')
            ->add('interval')
            ->add('lastrun')
        ;
    }

}