<?php
namespace Ws\StatisticBundle\Manager;

class StatLogic {
	
	private $context;
	private $event;
	private $increment;
	private $name;

	public function __construct($context = 'global',$event, $increment = 1, $name = '')
	{
		
		$this->event = $event;
		$this->context = $context;
		$this->increment = $increment;
		//set name from the event class
		if(method_exists($event, 'getName')) $this->name = $event->getName();
		//or from the constructor
		if(!empty($name)) $this->name = $name;
	}

	public function getContext()
	{
		return $this->context;
	}

	public function getEvent()
	{
		return $this->event;
	}

	public function getIncrement()
	{
		return $this->increment;
	}

	public function getName()
	{
		return $this->name;
	}

}
