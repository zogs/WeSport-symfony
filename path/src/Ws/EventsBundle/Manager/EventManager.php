<?php

namespace Ws\EventsBundle\Manager;

use My\ManagerBundle\Manager\AbstractManager;

use Ws\EventsBundle\Entity\Participation;
use Ws\EventsBundle\Entity\Serie;

class EventManager extends AbstractManager
{
	protected $em;

	
	/**
     * Save event or the serie of event
     *
     * @param event(s)
     *
     * @return object event
     */
	public function saveAll($event)
	{
		if($event->getSerie()->getStartDate() && $event->getSerie()->getEndDate() ){
			return $this->saveSerie($event);
		} 
		elseif($event->getDate()){
			$this->saveEvent($event,true);
			$this->saveParticipation($event,$this->getUser(),true);
			return $event;
		}		
	}

	/**
     * Save an event
     *
     * @param object event
     * @param boolean flush
     *
     * @return object event
     */
	public function saveEvent($event, $flush = false)
	{
		//set the location from the spot
		$event->setLocation($event->getSpot()->getLocation());
		//set the organizer
		$event->setOrganizer($this->context->getToken()->getUser());
		//set default title if empty
		if($event->getTitle() == null) $event->setTitle($event->getSport()->getName());		
		//auto comfirm if nbmin <=1
		if($event->getNbmin()<=1) $event->setConfirmed(true);

		$this->save($event,$flush);	
		return $event;
	}

	/**
     * Delete an event
     *
     * @param object event
     * @param boolean flush
     */
	public function deleteEvent($event, $flush = false)
	{
		$this->delete($event);
	}

	/**
     * Save a serie and all events of the serie
     *
     * @param obejct event
     *
     * @return object first event of the serie
     */
	public function saveSerie($event)
	{
		$serie = $event->getSerie();
		$start = $serie->getStartDate();
		$end = $serie->getEndDate();
		$end->modify('+1 day');
		$interval = \DateInterval::createFromDateString('1 day');
		$period = new \DatePeriod($start,$interval,$end);
		
		//find occurences
		$occurences = array();
		foreach($period as $day){
			
			$weekday = $day->format('l');

			if( $serie->isWeekdayInSerie($weekday) ){

				$occurence = clone($event);
				$occurence->setDate($day);
				$occurences[] = $occurence;											
			}			
		}

		//save events
		foreach ($occurences as $occurence) {
			$this->saveEvent($occurence);
		}
		//save serie
		$serie->setNboccurence(count($occurences));
		$this->save($occurence,true);

		//save participations
		reset($occurences);
		foreach ($occurences as $occurence) {
			$this->saveParticipation($occurence,$this->getUser());
		}
		$this->flush();

		return $occurences[0];	
	}

	/**
     * Delete a serie and all events of the serie
     *
     * @param event
     *
     * @return boolean
     */
	public function deleteSerie(Serie $serie)
	{
		//delete serie
		$this->delete($serie,true);

		return true;
	}

	/**
     * Save a participation to an event
     *
     * @param object event
     * @param object user
     * @param boolean flush
     *
     * @return object participation
     */
	public function saveParticipation($event,$user,$flush = false)
	{
		$participation = new Participation();
		$participation->setEvent($event);
		$participation->setUser($user);

		$this->save($participation,$flush);	


		return $participation;
	}

	/**
	 * Check if a user participe
	 *
	 * @param object $event
	 * @param object $user	 
	 */
	public function isNotParticipating($event,$user)
	{
		if($this->em->getRepository('WsEventsBundle:Participation')->findParticipation($event,$user))
			return false;
		else
			return true;
	}

	/**
	 * Check if a user participe
	 *
	 * @param object $event
	 * @param object $user	 
	 */
	public function isParticipating($event,$user)
	{
		if($this->em->getRepository('WsEventsBundle:Participation')->findParticipation($event,$user))
			return true;
		else
			return false;
	}


	/**
     * Delete a participation to an event
     *
     * @param object event
     * @param object user
     */
	public function deleteParticipation($event,$user,$flush = false)
	{
		$participation = $this->em->getRepository('WsEventsBundle:Participation')->findParticipation($event,$user);

		if($participation){

			$this->delete($participation,$flush);

		}
	}

	/**
     * Confirm an event
     *
     * @param object event
     *
     * @return object event
     */	
	public function confirmEvent($event)
	{
		$event->setConfirmed(true);
		$this->save($event,true);
		return $event;
	}

	/**
     * Unconfirm an event
     *
     * @param object event
     *
     * @return object event
     */	
	public function unconfirmEvent($event)
	{
		$event->setConfirmed(false);
		$this->save($event,true);
		return $event;
	}

	/**
     * set online an event
     *
     * @param object event
     *
     * @return object event
     */	
	public function publishEvent($event)
	{
		$event->setOnline(true);
		$this->save($event,true);
		return $event;
	}

	/**
     * set offline an event
     *
     * @param object event
     *
     * @return object event
     */	
	public function unpublishEvent($event)
	{
		$event->setOnline(false);
		$this->save($event,true);
		return $event;
	}
}
?>