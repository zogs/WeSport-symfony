<?php
 
namespace Ws\SportsBundle\Form\Type;
 
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

class SportSelectMultipleType extends AbstractType
{
    /**
     * @return  string
     */
    public function getName()
    {
        return 'sport_select_multiple';
    }
 
    /**
     * @return  string
     */
    public function getParent()
    {
        return 'entity';
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['attr'] = $options['attr'];
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
    	$resolver->setDefaults(array(   
            'class' => 'WsSportsBundle:Sport',
            'empty_value'=>"Sports",
            'label'=>'Sports',
            'property'=>'name',
            'expanded'=>false,
            'multiple' => true,
            'mapped' => true,
            'group_by' => 'category',
            'required' => false, 		
            'attr' => array(
                'class'=>'sportSelection',
                'placeholder' => 'Choisir un ou plusieurs sports',
                'multiple' => true,
                )
    	));
    }
}