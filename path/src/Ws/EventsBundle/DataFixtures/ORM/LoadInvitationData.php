<?php

namespace Ws\EventsBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use Ws\EventsBundle\Entity\Invitation;
use Ws\EventsBundle\Entity\Invited;

class LoadInvitationData extends AbstractFixture implements OrderedFixtureInterface
{

	public function load(ObjectManager $manager)
	{
		$today = new \DateTime();

		$invit = new Invitation();
		$invit->setEvent($this->getReference('event_football'));
		$invit->setInviter($this->getReference('user1'));
		$invit->setName('Liste d\'invitation de test 1');
		$invit->setDate($today);
		$invit->setContent('Salut, viens participer à notre activité c\'est trop cool');

		$invited = new Invited();
		$invited->setDate($today);
		$invited->setEmail('guichardsim+user1@gmail.com');
		$invited->setUser($this->getReference('user1'));
		$invited->setInvitation($invit);
		$invit->addInvited($invited);


		$invited = new Invited();
		$invited->setDate($today);
		$invited->setEmail('guichardsim@hotmail.com');
		$invited->setUser(null);
		$invited->setInvitation($invit);
		$invit->addInvited($invited);


		$invited = new Invited();
		$invited->setDate($today);
		$invited->setEmail('guichardsim+user2@gmail.com');
		$invited->setUser($this->getReference('asso1'));
		$invited->setInvitation($invit);
		$invit->addInvited($invited);

		$manager->persist($invit);
		$manager->flush();

		$this->addReference('invitation1', $invit);
	}

	public function getOrder(){

		return 6; // the order in which fixtures will be loaded
	}
}

?>