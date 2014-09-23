<?php

namespace My\CommentBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

use FOS\CommentBundle\Events;
use FOS\CommentBundle\Event\CommentEvent;
use Doctrine\ORM\EntityManager;
use Ws\MailerBundle\Mailer\Mailer;

class CommentListener implements EventSubscriberInterface
{

	private $em;
	private $router;
	private $mailer;

	public function __construct(EntityManager $em, UrlGeneratorInterface $router, Mailer $mailer)
	{
		$this->em = $em;
		$this->router = $router;
		$this->mailer = $mailer;
	}

	public static function getSubscribedEvents()
	{
		return array(
		  Events::COMMENT_POST_PERSIST => 'onCommentPosted',      
		  );
	}

	public function onCommentPosted( CommentEvent $event )
	{
		$comment = $event->getComment();

		//if its a event's comment
		if($comment->getThread()->getContext()=='event'){

			//get the related event
		    $event = $this->em->getRepository('WsEventsBundle:Event')->findOneById($comment->getThread()->getUid());	

		    //cancel if its a organizer comment
		    if($event->getOrganizer() == $comment->getAuthor()) return;

		    //send to organizer an email
		    $this->mailer->sendEventCommentedMessage($comment,$event);
		}


	}

}