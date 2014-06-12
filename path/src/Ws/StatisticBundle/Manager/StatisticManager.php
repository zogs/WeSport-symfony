<?php

namespace Ws\StatisticBundle\Manager;

use My\ManagerBundle\Manager\AbstractManager;

use Ws\StatisticBundle\Entity\GeneralStat;

class StatisticManager extends AbstractManager
{
	protected $em;
	protected $stat;
	protected $scope = 'general';

	public function setContext($scope)
	{
		$this->scope = $scope;
		return $this;
	}

	public function get($id = null)
	{
		if($this->scope == 'general'){			
			$this->data = $this->em->getRepository('WsStatisticBundle:GeneralStat')->findOneByName('main');
		}
		if($this->scope == 'user'){		
			$this->data = $this->em->getRepository('WsStatisticBundle:UserStat')->findOneByUser($id);
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
		if(property_exists(new GeneralStat,'total_'.$name)){
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

	public function update($scope)
	{
		if($scope=='general') return $this->updateGeneral();

		if(isset($this->scope)) $this->update($this->scope);
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