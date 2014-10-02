<?php
 
namespace My\UtilsBundle\Form\Type;
 
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormBuilderInterface;
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
    	if(is_string($options['tags']))
    		$view->vars['tags'] = $options['tags'];
    	if(is_array($options['tags']))
    		$view->vars['tags'] = implode(', ',$options['tags']);

    	if(is_string($options['prefetch']))
    		$view->vars['prefetch'] = $options['prefetch'];
    	if(is_array($options['prefetch']))
    		$view->vars['prefetch'] = implode(', ',$options['prefetch']);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
    	$resolver->setDefaults(array(
    		'tags' => '',
    		'prefetch'=>'',
    	));
    }
}