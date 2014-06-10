<?php

namespace Ws\StatisticBundle\Manager;

use My\ManagerBundle\Manager\AbstractManager;

use Ws\StatisticBundle\Entity\General;

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

	public function increment($name,$i=1)
	{
		$this->stat->$name += $i;

		$this->save($this->stat,true);

		if(property_exists('General','total_'.$name)){
			$this->incrementGeneral('total_'.$name);
		}
	}

	public function incrementGeneral($name)
	{
		$stat = $this->setContext('general')->get();

		$stat->$name += 1;

		$this->save($stat,true);
	}

	public function decrement($name,$i=1)
	{
		$this->stat->$name -= $i;

		$this->save($this->stat,true);

		if(property_exists('General', 'total_'.$name)){
			$this->incrementGeneral('total_'.$name);
		}
	}

	public function decrementGeneral($name)
	{
		$stat = $this->setContext('General')->get();

		$stat->$name -= 1;

		$this->save($stat,true);
	}

	public function get($id = null)
	{
		if($this->scope == 'general'){			
			$this->stat = $this->em->getRepository('WsStatisticBundle:GeneralStat')->findOneByName('main');
		}

		if($this->scope == 'user'){		
			$this->stat = $this->em->getRepository('WsStatisticBundle:UserStat')->findOneByUser($id);
		}

		return $this;
		

	}

	public function update($scope)
	{
		if($scope=='general') return $this->updateGeneral();

		if(isset($this->scope)) $this->update($this->scope);
	}

	private function updateGeneral()
	{
		$this->setContext('general');

		$this->get();
			
		$this->stat->setEventTotalCount($this->em->getRepository('WsEventsBundle:Event')->countAll());
		$this->stat->setUserTotalCount($this->em->getRepository('MyUserBundle:User')->countAll());


		$this->save($this->stat,true);

		return $this->stat;
	}

}
?>