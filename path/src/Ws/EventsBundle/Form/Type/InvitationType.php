<?php
namespace Ws\EventsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class InvitationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
	->add('emails','textarea',array(  
		'mapped'=>false,                                  
		'label'=>'Emails',
		'attr'=>array('placeholder'=>'Entrer les adresses email de vos amis'))
	)
	->add('event','entity',array(
		'label'=>'Event',
		'class'=> 'WsEventsBundle:Event',
		'property' => 'title'))
	->add('name','text',array(
		'attr'=>array(
			'placeholder'=>'Donner un nom pour enregistrez votre liste')
		))
	->add('save','submit');
    		
    }

    public function getName()
    {
        return 'invitation';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
	    $resolver->setDefaults(array(
	        'data_class' => 'Ws\EventsBundle\Entity\Invitation',
	    ));
	}
}

?>