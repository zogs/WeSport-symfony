<?php
namespace Ws\EventsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

use My\UserBundle\Entity\User;
use Ws\EventsBundle\Entity\Invitation;

class InvitationType extends AbstractType
{
	private $em;
	private $user;

    public function __construct(EntityManager $em, User $user)
    {
        $this->em = $em;
        $this->user = $user;
    }



    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	$user = $this->user;

        $builder
	->add('emails','textarea',array(  
		'mapped'=>false,                                  
		'label'=>'Créer une liste',
		'attr'=>array('placeholder'=>'Entrer les adresses email de vos amis')
		))

	->add('event','entity',array(
		'label'=>'Event',
		'class'=> 'WsEventsBundle:Event',
		'property' => 'title'))

	->add('name','text',array(
		'required' => false,
		'attr'=>array(
			'placeholder'=>'Donner un nom pour enregistrez votre liste')
		))

	->add('save','submit');
    		

    		$builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onPreSetData'));
        	$builder->addEventListener(FormEvents::PRE_SUBMIT, array($this, 'onPreSubmit'));
        	$builder->addEventListener(FormEvents::POST_SUBMIT, array($this, 'onPostSubmit'));
    }

    public function onPreSetData(FormEvent $event)
    {
    	$form = $event->getForm();

    	//add previously saved invitation's list
    	$invits = $this->em->getRepository('WsEventsBundle:Invitation')->findSavedInvitation($this->user);
    	$list = array();
    	foreach($invits as $invit){
    		$list[$invit->getId()] = $invit->getName();
    	}


    	if(!empty($invits)){
    		$form->add('saved_list','choice',array(
        		'mapped' => false,
        		'label' => 'Utiliser une liste précédante',
        		'expanded' => false,
        		'multiple' => false,
        		'choices' => $list,
        		));
    	}
    	
    }	

    public function onPreSubmit(FormEvent $event)
    {
    	$form = $event->getForm();
    	$data = $event->getData();

    	$event = $this->em->getRepository('WsEventsBundle:Event')->find($data['event']);

    	$invit = new Invitation();
    	$invit->setEvent($event);
    	$invit->setInviter($this->user);

    	if(!empty($data['name']))
    		$invit->setName($data['name']);
    	
    	if(!empty($data['emails']))
    		$invit->setEmails($data['emails']);    	

    	if(!empty($data['content'])){
    		$invit->setContent($data['content']);
    	}

    	$form->setData($invit);
    	
    }

    public function onPostSubmit(FormEvent $event)
    {
    	
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