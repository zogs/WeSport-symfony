<?php

namespace Ws\ConvertSQLBundle\Caller;

use Ws\ConvertSQLBundle\Caller\AbstractCaller;

class SeriesCaller extends AbstractCaller
{
	private $events;

	public function getType()
	{
		$type = $this->entity->getType();

		if(null === $type) return 'person';

		return $type;
	}

	public function getOrganizer()
	{
		
		$events = $this->getSerieEvents();

		if(empty($events)) return '_skip_';

		$event = $events[0];
		$user_id = $event['user_id'];
		$user = $this->em->getRepository('MyUserBundle:User')->findOneById($user_id);

		if(NULL===$user) return '_skip_';

		return $user;
	}

	public function getSerieEvents()
	{
		$db = $this->container->get('doctrine.dbal.oldwesport_connection');
		$stmt = $db->prepare('SELECT * FROM events WHERE serie_id='.$this->entry['serie_id'].' ORDER BY date ASC, time ASC');		
		$stmt->execute();
		return $stmt->fetchAll();
	}
	
	public function setEvents()
	{

		$events = $this->getSerieEvents();		
		$entity = $this->em->merge($this->entity);

		if( !empty($events)) {
			foreach ($events as $key => $old) {
				
				$event = $this->em->getRepository('WsEventsBundle:Event')->findOneById($old['id']);				
				$event->setSerie($entity);
				$event->setOccurence($key+1);

				$this->em->persist($event);
			}

			$this->em->flush();
			$this->em->clear();
		}
	
		return '_noset_';
	}

	public function getStartDate()
	{
		return $this->createDatetimeFrom($this->entry['startdate'],'Y-m-d');
	}

	public function getEndDate()
	{
		return $this->createDatetimeFrom($this->entry['enddate'],'Y-m-d');
	}

	public function getDateDepot()
	{
		$events = $this->getSerieEvents();

		if(!empty($events)){
			$event = $events[0];

			return $this->createDatetimeFrom($event['date_depot'],'Y-m-d H:i:s');
		}

		return null;
	}
	
}