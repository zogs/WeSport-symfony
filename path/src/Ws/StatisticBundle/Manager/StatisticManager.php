<?php

namespace Ws\StatisticBundle\Manager;

use My\ManagerBundle\Manager\AbstractManager;
use Symfony\Component\Yaml\Parser;

use Ws\StatisticBundle\Entity\GeneralStat;

class StatisticManager extends AbstractManager
{
	protected $em;
	protected $stat;
	protected $ctx = 'general';
	protected $ctx_id = null;
	
	public function setContext($ctx,$id = null)
	{
		$this->ctx = $ctx;
		$this->ctx_id = $id;

		return $this;
	}

	public function get()
	{
		if($this->ctx == 'general'){			
			$stat = $this->em->getRepository('WsStatisticBundle:GeneralStat')->findOneByName('main');

			if(NULL==$stat){
				$stat = $this->em->getRepository('WsStatisticBundle:GeneralStat')->initStat('main');
			}

			$this->data = $stat;
		}
		if($this->ctx == 'user'){		
			$this->data = $this->em->getRepository('WsStatisticBundle:UserStat')->findOneByUser($this->ctx_id);
		}
		return $this;
		
	}

	public function increment($name,$i=1)
	{
		$this->data->$name += $i;
		$this->save($this->data,true);
		$this->saveGeneralStatistic($name,$i);		
	}

	public function decrement($name,$i=1)
	{
		$this->data->$name -= $i;
		$this->save($this->data,true);
		$this->saveGeneralStatistic($name,-$i);
	}

	private function saveGeneralStatistic($name,$i=1)
	{
		$name = 'total_'.$name;
		if(property_exists(new GeneralStat,$name)){
			$this->setContext('general')->get();
			$this->data->$name += $i;
			$this->save($this->data,true);
		}
	}

	public function sportParticiped($sport,$user)
	{
		$this->em->getRepository('WsStatisticBundle:UserSportStat')->setSportParticiped($sport,$user);
	}

	public function sportCreated($sport,$user)
	{
		$this->em->getRepository('WsStatisticBundle:UserSportStat')->setSportCreated($sport,$user);
	}

	public function update($ctx)
	{
		if($ctx=='general') return $this->updateGeneral();

		if(isset($this->ctx)) $this->update($this->ctx);
	}

	private function updateGeneral()
	{
		$this->setContext('general')->get();

		$this->data->setTotalEventCreated($this->em->getRepository('WsEventsBundle:Event')->countAll());
		$this->data->setTotalUserRegistered($this->em->getRepository('MyUserBundle:User')->countAll());
		$this->data->setTotalEventParticipation($this->em->getRepository('WsEventsBundle:Participation')->countAll());
				
		$this->save($this->data,true);

		return $this->data;
	}

	public function setEvent($event)
	{
		if(!method_exists($event, 'getStatLogic')) throw new \Exception("Method getStatLogic must be defined", 1);		

		$logics = $event->getStatLogic();

		if(empty($logics)) return;

		foreach ($logics as $key => $logic) {

			$stat = $this->getContextStat($logic[0],$event);

			$conf = $this->importConf($logic[0]);
			
			$field = $conf[$logic[1]];

			if(!isset($stat->$field)) throw new \Exception("The Event name is not matching any field property of the context ".ucfirst($logic[0]), 1);
			
			$stat->$field += $logic[2];

			$this->save($stat,true);
		}
	}

	private function getContextStat($context,$event)
	{
		$context = strtolower($context);

		if($context == 'global'){

			if($stat = $this->em->getRepository('WsStatisticBundle:GeneralStat')->findOneByName('main')){
				return $stat;
			}
			else {
				return $this->em->getRepository('WsStatisticBundle:GeneralStat')->initStat('main');
			}			
		}

		if($context == 'user'){
			if(!method_exists($event, 'getUser')) throw new \Exception("Method getUser must be defined", 1);
			if($event->getUser() == null) throw new \Exception("User can not be null at his point",1);

			return $this->em->getRepository('WsStatisticBundle:UserStat')->findOneByUser($event->getuser()->getId());
			
		}
	}

	private function importConf($context)
	{
		$yaml = new Parser();
		return $yaml->parse(file_get_contents(__DIR__.'/../Resources/config/fields/'.$context.'.yml'));
	}



}
?>