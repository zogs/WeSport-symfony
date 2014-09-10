<?php

namespace Ws\StatisticBundle\Manager;

use My\ManagerBundle\Manager\AbstractManager;

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
			$this->data = $this->em->getRepository('WsStatisticBundle:GeneralStat')->findOneByName('main');
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
			
		$this->data->setEventTotalCount($this->em->getRepository('WsEventsBundle:Event')->countAll());
		$this->data->setUserTotalCount($this->em->getRepository('MyUserBundle:User')->countAll());


		$this->save($this->data,true);

		return $this->data;
	}

}
?>