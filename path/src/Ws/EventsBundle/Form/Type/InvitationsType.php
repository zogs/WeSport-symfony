<?php
namespace Ws\EventsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormError;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Routing\Router;
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
	private $router;

	public function __construct(EntityManager $em, SecurityContext $secu, Router $router, Event $event = null)
	{
		$this->em = $em;
		$this->secu = $secu;
		$this->user = $secu->getToken()->getUser();
		$this->event = $event;
		$this->router = $router;
	}



	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('emails','tags',array(                                    
				'tags' => '',
				'required'=> false,
				'ajax_url' => $this->router->generate('ws_inviter_emails_ajax'),
				'trigger_length' => 2,
			))

			->add('content','textarea',array(
				'required' => false,
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

		if(empty($data['emails'])) return $form->setData(null);

		//get the event concerned from the submitted data
		if(!empty($data['event'])){ 
			$event = $this->em->getRepository('WsEventsBundle:Event')->findOneById($data['event']);             
		}
		//or from the embedded formular
		elseif(NULL != $form->getParent() && $form->getParent()->getData() instanceof Event){
			$event = $form->getParent()->getData();
		}
		//or from the __construct method
		elseif($this->event instanceof Event){
			$event = $this->event;
		}

		//create invitation
		if($this->user instanceof User){

			//if user have already invited somebody to this event, get the Invitation object
			if($invit = $this->em->getRepository('WsEventsBundle:Invitation')->findOneByUserAndEvent($this->user,$event)){
				$invit->setInviter($this->user);            
			}
			//else create a new Invitation
			else {
				$invit = new Invitation();
				$invit->setInviter($this->user);
			}
		}
		else 
			throw new \Exception('User must be an instance of My\UserBundle\Entity\User');     

		
		//set persisted data
		$invit->setEvent($event);
		$invit->setName('invitation à '.$event->getTitle().' le '.$event->getDate()->format('d/m/Y'));
		//set unpersisted data
		$invit->setContent($data['content']);
		$invit->setEmails($data['emails']);

		
		if(!empty($data['emails'])){

			//get array of all emails that have been already invited for that particular event
			$emails_already_invited = $this->em->getRepository('WsEventsBundle:Invitation')->findEmailsByEvent($event);

			//get submitted email tags
			$tags = explode(',', $data['emails']);

			foreach ($tags as $k => $tag) {
				
				//no more than 10 invited at once
				if($k>=10) break;

				//set invited
				$invited = null;

				//check if tag is a email
				if(\My\UtilsBundle\Utils\String::isEmail($tag)){

					//check if the email is registered					
					//if yes, set invited as registered user
					if($user = $this->em->getRepository('MyUserBundle:User')->findOneByEmail($tag)){
						$invited = new Invited();
						$invited->setEmail($user->getEmail());
						$invited->setUser($user);
					}
					//else, set invited as simple email
					else {
						$invited = new Invited();
						$invited->setEmail($tag);
						$invited->setUser(null);
					}
				}
				//if tag is not an email
				else {
					// check if it is a username
					if($user = $this->em->getRepository('MyUserBundle:User')->findOneByUsername($tag)){
						$invited = new Invited();
						$invited->setEmail($user->getEmail());
						$invited->setUser($user);					
					}

				}

				//if invited exist
				if($invited != null ){
					//check if not already invited
					if(!in_array($invited->getEmail(),$emails_already_invited)){
						
						$invited->setInvitation($invit);
						$invit->addInvited($invited);						
					}
				}
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