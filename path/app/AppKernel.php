<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle(),

            //imported
            new FOS\UserBundle\FOSUserBundle(),
            new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
            new JMS\I18nRoutingBundle\JMSI18nRoutingBundle(),
            new JMS\TranslationBundle\JMSTranslationBundle(),
            new APY\DataGridBundle\APYDataGridBundle(),
            new Knp\Bundle\MenuBundle\KnpMenuBundle(),
            new FOS\RestBundle\FOSRestBundle(),
            new FOS\CommentBundle\FOSCommentBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle($this),
            new Sonata\CoreBundle\SonataCoreBundle(),
            new Sonata\BlockBundle\SonataBlockBundle(),
            new Sonata\jQueryBundle\SonatajQueryBundle(),
            new Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle(),
            new Sonata\EasyExtendsBundle\SonataEasyExtendsBundle(),
            new Sonata\AdminBundle\SonataAdminBundle(),
            new HWI\Bundle\OAuthBundle\HWIOAuthBundle(),

            //created
            new My\BlogBundle\MyBlogBundle(),
            new My\UserBundle\MyUserBundle(),
            new My\PageBundle\MyPageBundle(),
            new My\CommentBundle\MyCommentBundle(),
            new Ws\SportsBundle\WsSportsBundle(),
            new Ws\EventsBundle\WsEventsBundle(),
            new My\WorldBundle\MyWorldBundle(),
            new My\FlashBundle\MyFlashBundle(),
            new My\ManagerBundle\MyManagerBundle(),
            new Ws\StyleBundle\WsStyleBundle(),
            new Ws\MailerBundle\WsMailerBundle(),
            new My\ContactBundle\MyContactBundle(),
            new My\UtilsBundle\MyUtilsBundle(),
            new Ws\StatisticBundle\WsStatisticBundle(),
            new Ws\ConvertSQLBundle\WsConvertSQLBundle(),
            new My\CronBundle\MyCronBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
}
