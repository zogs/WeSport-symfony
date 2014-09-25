<?php

namespace Ws\ConvertSQLBundle\Caller;

use Symfony\Component\DependencyInjection\Container;

class AbstractCaller {

	protected $em;
	protected $container;
	protected $entry; //Array of fields of the database entry

	public function __construct(Container $container, $entry)
	{
		$this->container = $container;
		$this->entry = $entry;
		$this->em = $container->get('doctrine')->getEntityManager();
	}

	public function setContainer(Container $container)
	{
		$this->container = $container;
	}

	public function setEntry($entry)
	{
		$this->entry = $entry;
	}
}