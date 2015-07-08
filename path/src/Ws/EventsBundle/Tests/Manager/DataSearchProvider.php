<?php

namespace Ws\EventsBundle\Tests\Manager;

use Doctrine\ORM\EntityManager;

use Ws\EventsBundle\Entity\Search;

class DataSearchProvider
{
	public $searchs;

	public function __construct(EntityManager $em)
	{
		$this->em = $em;
		$this->now = new \DateTime('now');

		$this->searchs['simple'] = new Search();
		$this->searchs['simple']->setNbDays(1);
		$this->searchs['simple']->setPrice(5);
		$this->searchs['simple']->setType(array('asso','pro'));	
		$this->searchs['simple']->setTimeStart($this->now);	
		$this->searchs['simple']->setDayOfWeek(array('monday','wednesday'));
		$this->searchs['simple']->setLevel(array('beginner'));

		$this->searchs['complex'] = new Search();
		$this->searchs['complex']->setCountry($this->em->getRepository('MyWorldBundle:Country')->findCountryByName('France'));
		$this->searchs['complex']->setLocation($this->em->getRepository('MyWorldBundle:Location')->findLocationByCityName('Dijon'));
		$this->searchs['complex']->setSports($this->em->getRepository('WsSportsBundle:Sport')->findAll());
		$this->searchs['complex']->setArea(100);
		$this->searchs['complex']->setType(array('person','pro'));
		$this->searchs['complex']->setNbDays(4);
		$this->searchs['complex']->setTimeEnd($this->now);

		$this->searchs['full'] = new Search();
		$this->searchs['full']->setCountry($this->em->getRepository('MyWorldBundle:Country')->findCountryByName('France'));
		$this->searchs['full']->setLocation($this->em->getRepository('MyWorldBundle:Location')->findLocationByCityName('Beaune'));
		$this->searchs['full']->setSports($this->em->getRepository('WsSportsBundle:Sport')->findAll());
		$this->searchs['full']->setArea(100);
		$this->searchs['full']->setType(array('person','pro','asso'));
		$this->searchs['full']->setNbDays(14);
		$this->searchs['full']->setTimeEnd($this->now);
		$this->searchs['full']->setPrice(50);
		$this->searchs['full']->setTimeStart($this->now);	
		$this->searchs['full']->setDayOfWeek(array('monday','wednesday','friday','sunday'));
		$this->searchs['full']->setLevel(array('beginner','confirmed','expert'));

		$this->searchs['organizer'] = new Search();
		$this->searchs['organizer']->setOrganizer($this->em->getRepository('MyUserBundle:User')->findOneByUsername('admin'));
	}

	public function get($key)
	{
		return (isset($this->searchs[$key]))? $this->searchs[$key] : null;
	}

	public function all()
	{
		return $this->searchs;
	}

	public function add(Search $search,$key)
	{
		$this->searchs[$key] = $search;
		return $search;
	}

}