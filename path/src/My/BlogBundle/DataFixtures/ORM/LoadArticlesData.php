<?php

namespace My\BlogBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use My\BlogBundle\Entity\Article;

class LoadArticlesData implements FixtureInterface
{

	public function load(ObjectManager $manager)
	{
		$article1 = new Article();
		$article1->setTitre("Une belle brune");
		$article1->setDescription("Bonjour, ceci est le premier article dun blog !");
		$article1->setUrl("http://www.gq.com/images/women/2012/02/lust-list/jessica-pare/jessica-pare-01.jpg");

		$article2 = new Article();
		$article2->setTitre("Une belle chataîn");
		$article2->setDescription("Deuxieme article du blog ( mon préféré )");
		$article2->setUrl("http://series-parlotte.eu/ressources/images/LesActeurs/BaseDeDonneesDesActeurs/PeytonList.jpg");

		$article3 = new Article();
		$article3->setTitre("Une autre chose brunette");
		$article3->setDescription("Retouché sur photoshop bien sur !");
		$article3->setUrl("http://www.touchpuppet.com/wp-content/uploads/2013/08/Alison-Brie-by-Miko-Lim-for-Esquire-May-201301.jpg");

		$manager->persist($article1);
		$manager->persist($article2);
		$manager->persist($article3);
		$manager->flush();
	}
}

?>