<?php
namespace Ws\EventsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormError;
use Symfony\Component\Security\Core\SecurityContext;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

use My\UserBundle\Entity\User;
use Ws\EventsBundle\Entity\Invitation;
use Ws\EventsBundle\Entity\Event;
use Ws\EventsBundle\Entity\Invited;

class InvitationsType extends AbstractType
{
	private $em;
	private $secu;
	private $user;

	public function __construct(EntityManager $em, SecurityContext $secu, Event $event = null)
	{
		$this->em = $em;
		$this->secu = $secu;
		$this->user = $secu->getToken()->getUser();
		$this->event = $event;
	}



	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('emails','tags',array(                                    
				'label'=>'Créer une liste',
				'tags' => '',
				'required'=> false,
				'attr'=>array(
					'placeholder'=>'Adresses email d\'amis',
				    )
			))

			->add('content','textarea',array(
				'label' => "Un truc à leurs dire ?",
				'required' => false,
				'attr' => array(
					'placeholder' => "Salut les copains, ..."
				)
			))
			;

		$builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onPreSetData'));
		$builder->addEventListener(FormEvents::PRE_SUBMIT, array($this, 'onPreSubmit'));
		$builder->addEventListener(FormEvents::POST_SUBMIT, array($this, 'onPostSubmit'));
	}

	public function onPreSetData(FormEvent $event)
	{
		$form = $event->getForm();
		$data = $event->getData();

		//add event hidden field if needed
		if($this->event != null){
			$form->add('event','hidden',array(
				'mapped'=> false,
				'data'=> $this->event->getId(),		        
			));
		}
	}	


	public function onPreSubmit(FormEvent $event)
	{
		$form = $event->getForm();
		$data = $event->getData(); 

		$invit = new Invitation();

		if($this->user instanceof User)
			$invit->setInviter($this->user);            
		else 
			throw new \Exception('User is not an instance of User class');     

		if(!empty($data['event']))
			$event = $this->em->getRepository('WsEventsBundle:Event')->find($data['event']);             
		elseif(NULL != $form->getParent() && $form->getParent()->getData() instanceof Event)          
			$event = $form->getParent()->getData();
		elseif($this->event instanceof Event)
			$event = $this->event;

		$invit->setEvent($event);
		$invit->setContent($data['content']);
		$invit->setEmails($data['emails']);

			\My\UtilsBundle\Utils\Debug::debug($data);
		if(!empty($data['emails'])){

			$emails = \My\UtilsBundle\Utils\String::findEmailsInString($data['emails']);
			\My\UtilsBundle\Utils\Debug::debug($emails);
			foreach ($emails as $key => $email) {   
				if($key>=10) break;
				$o = new Invited();
				$o->setEmail($email);
				$o->setUser($this->em->getRepository('MyUserBundle:User')->findOneByEmail($email));
				$o->setInvitation($invit);
				$invit->addInvited($o);
			}           
		}
		else {
			return $form->setData(null);
		}

		if($invit->isEmpty()){
			$form->setData(null);
		} 
		else {
			$event->addInvitation($invit);
			$form->setData($invit);   	
		}

	}

	public function onPostSubmit(FormEvent $event)
	{
		$form = $event->getForm();
		$invit = $event->getData(); 
	}

	public function getName()
	{
		return 'invitations_type';
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'Ws\EventsBundle\Entity\Invitation',
		));
	}

}

?>