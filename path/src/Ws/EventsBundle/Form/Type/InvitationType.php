<?php
namespace Ws\EventsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Security\Core\SecurityContext;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

use My\UserBundle\Entity\User;
use Ws\EventsBundle\Entity\Invitation;
use Ws\EventsBundle\Entity\Event;
use Ws\EventsBundle\Entity\Invited;

class InvitationType extends AbstractType
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
        $user = $this->user;

        $builder
        ->add('emails','textarea',array(  
	        'mapped'=>false,                                  
	        'label'=>'Créer une liste',
            'required'=> false,
	        'attr'=>array(
	        	'placeholder'=>'Entrer les adresses email de vos amis')
        ))

        
        ->add('name','text',array(
	        'required' => false,
	        'attr'=>array(
	        	'placeholder'=>'Donner un nom pour enregistrez votre liste')
        ))
        ;


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
                'required' => false,
        		));
    	}

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

    	//create the invitation object
        $invit = new Invitation();    	
        //set the inviter (user) object
        if($this->user instanceof User){
            $invit->setInviter($this->user);            
        } 
        else throw new \Exception('User is not an instance of User class');
        
        //set the event object from hidden field of the form
        if(!empty($data['event'])){
           $event = $this->em->getRepository('WsEventsBundle:Event')->find($data['event']);
           $invit->setEvent($event);        
        } 
        //else try to set the event from the parent form
        elseif($form->getParent()->getData() instanceof Event){
    		$invit->setEvent($form->getParent()->getData());
    	} 
        
    	//thow error if event is null
    	if($invit->getEvent() == NULL) throw new \Exception('Event paramater is not present in the form');

        //set the name
    	if(!empty($data['name']))
    		$invit->setName($data['name']);
    	//set the emails
    	if(!empty($data['emails']))
    		$invit->setEmails($data['emails']);    	
        //set the text content
    	if(!empty($data['content'])){
    		$invit->setContent($data['content']);
    	}

    	//create and set all the invited object
    	if(!empty($data['emails'])){

    		$emails = \My\UtilsBundle\Utils\String::findEmailsInString($data['emails']);
    		foreach ($emails as $key => $email) {	
                
    			if($key>=10) break;
				$o = new Invited();
				$o->setEmail($email);
                $o->setUser($this->em->getRepository('MyUserBundle:User')->findOneByEmail($email));
				$o->setInvitation($invit);
				$invit->addInvited($o);
			}			
    	}

        //get previously saved listing and set as Invitation 
        if(!empty($data['saved_list']) && is_numeric($data['saved_list'])){

            $invit = $this->em->getRepository('WsEventsBundle:Invitation')->find($data['saved_list']);           
        }

    	$form->setData($invit);
    	
    }

    public function onPostSubmit(FormEvent $event)
    {
    	
    }

    public function getName()
    {
        return 'invitation_type';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
	    $resolver->setDefaults(array(
	        'data_class' => 'Ws\EventsBundle\Entity\Invitation',
	    ));
	}
}

?>