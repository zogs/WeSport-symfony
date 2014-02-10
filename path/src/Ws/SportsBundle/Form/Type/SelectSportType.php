<?php

namespace Ws\SportsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SelectSportType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name','entity',array(                                    
                                    'class'=>'WsSportsBundle:Sport',
                                    'label'=>'Sport',
                                    'property'=>'name',
                                    'mapped'=>'false',
                                    'empty_value'=>'Choississez un sport',
                                    'expanded'=>false)
            )
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ws\SportsBundle\Entity\Sport',
            'cascade_validation' => true
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'select_sport';
    }
}
