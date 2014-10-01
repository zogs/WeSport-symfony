<?php
 
namespace My\UtilsBundle\Form\Type;
 
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\PropertyAccess\PropertyAccess;

class TagsType extends AbstractType
{
    /**
     * @return  string
     */
    public function getName()
    {
        return 'tags';
    }
 
    /**
     * @return  string
     */
    public function getParent()
    {
        return 'textarea';
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {

    }
}