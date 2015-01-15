<?php

namespace Ws\StatisticBundle\Manager;

use My\ManagerBundle\Manager\AbstractManager;
use Symfony\Component\Yaml\Parser;

use My\UserBundle\Entity\User;
use My\UtilsBundle\Utils\String;

class StatisticManager extends AbstractManager
{
	protected $stat;
	protected $logics = array();	
		
	public function sportParticiped($sport,$user)
	{
		$this->em->getRepository('WsStatisticBundle:UserSportStat')->setSportParticiped($sport,$user);
	}

	public function sportCreated($sport,$user)
	{
		$this->em->getRepository('WsStatisticBundle:UserSportStat')->setSportCreated($sport,$user);
	}

	

	/**
	 * Set statistic logics from an Event
	 *
	 * @param $event Event
	 *
	 * Event must have a method getStatLogic() that return an array of instruction, ex: array('user','ws.event.create',1|-1)
	 */
	public function fromEvent($event)
	{
		if(!method_exists($event, 'getStatLogics')) throw new \Exception("Method getStatLogics must be defined", 1);		

		$logics = $event->getStatLogics();
		$this->logics = array_merge($this->logics,$logics);

		return $this;		
	}

	/**
	 * Set statistics logics
	 *
	 * @param $logics array of Ws\StatisticBundle\Manager\StatLogic
	 */
	public function setLogics($logics = array())
	{
		$this->logics = array_merge($this->logics,$logics);

		return $this;
	}

	/**
	 * Set statistics logic
	 *
	 * @param $logic Ws\StatisticBundle\Manager\StatLogic
	 */
	public function setLogic(StatLogic $logic)
	{
		$this->logics[] = $logic;

		return $this;
	}


	/**
	 * Update the statistics according to the logics
	 */
	public function update()
	{
		if(empty($this->logics)) return;

		foreach ($this->logics as $logic) {

			if(!$logic instanceof StatLogic) throw new \Exception("Logic must be a StatLogic class", 1);
		
			$stat = $this->getStat($logic->getContext(),$logic->getEvent());

			$conf = $this->importConf($logic->getContext());			
			$field = $conf[$logic->getName()];
			$method = String::camelize($field,true);
			$setMethod = 'set'.$method;
			$getMethod = 'get'.$method;

			if(method_exists($stat,$setMethod) && method_exists($stat,$getMethod)){
			
				$before = $stat->$getMethod();		
				$after = $before + $logic->getIncrement();		
				$stat->$setMethod($after);

				$this->save($stat,true);

			} else {
				throw new \Exception("The Event named \"".$field."\" is not matching any field property of the context ".ucfirst($logic->getContext()."... Also Setter and Getter must be defined"), 1);
			}
			
		}
	}

	/**
	 * Return or initialize the global statistics
	 */
	public function getGlobalStat()
	{
		if($stat = $this->em->getRepository('WsStatisticBundle:GlobalStat')->findOneByName('main')){
			return $stat;
		}
		else {
			return $this->em->getRepository('WsStatisticBundle:GlobalStat')->initStat('main');
		}	
	}

	/**
	 * Return the user statistic
	 */
	public function getUserStat(User $user)
	{
		return $this->em->getRepository('WsStatisticBundle:UserStat')->find($user);
	}

	/**
	 * Return Stat object
	 *
	 * @param $context string 
	 * @param $event Event
	 *
	 */
	private function getStat($context,$event = null)
	{
		$context = strtolower($context);

		if($context == 'global'){

			return $this->getGlobalStat();			
		}

		if($context == 'user'){

			if(!method_exists($event, 'getUser')) throw new \Exception("Method getUser must be defined", 1);
			if($event->getUser() == null) throw new \Exception("User can not be null at his point",1);

			return $this->getUserStat($event->getUser());

			
			
		}
	}

	/**
	 * get the parameters events=>fields of the context
	 */
	private function importConf($context)
	{
		$yaml = new Parser();
		return $yaml->parse(file_get_contents(__DIR__.'/../Resources/config/fields/'.$context.'.yml'));
	}

	/**
	 * update global stats with the real count of database entitites
	 */
	public function updateGlobalStat()
	{
		$stat = $this->getGlobalStat();

		$stat->setTotalEventCreated($this->em->getRepository('WsEventsBundle:Event')->countAll());
		$stat->setTotalUserRegistered($this->em->getRepository('MyUserBundle:User')->countAll());
		$stat->setTotalEventParticipation($this->em->getRepository('WsEventsBundle:Participation')->countAll());
				
		$this->save($stat,true);

		return $stat;
	}


}
?>