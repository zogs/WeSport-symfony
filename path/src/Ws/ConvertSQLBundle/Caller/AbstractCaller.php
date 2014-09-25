<?php

namespace Ws\ConvertSQLBundle\Caller;

use Symfony\Component\DependencyInjection\Container;

class AbstractCaller {

	protected $em;
	protected $container;
	protected $entry; //Array of fields of the database entry
	protected $entity;

	public function __construct(Container $container, $entry, $entity)
	{
		$this->container = $container;
		$this->entry = $entry;
		$this->entity = $entity;
		$this->em = $container->get('doctrine')->getManager();
	}

	public function setContainer(Container $container)
	{
		$this->container = $container;
	}

	public function setEntry($entry)
	{
		$this->entry = $entry;
	}

	protected function formatDate($date,$format)
	{
		$r = new \DateTime();
		$r->createFromFormat($format,$date);
		return $r;
	}
}