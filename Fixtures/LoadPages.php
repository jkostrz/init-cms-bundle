<?php

/*
 * This file is part of the Networking package.
 *
 * (c) net working AG <info@networking.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Networking\InitCmsBundle\Fixtures;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Networking\InitCmsBundle\Entity\Page;

/**
 *
 */
class LoadPages extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    private $container;

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $languages = $this->container->getParameter('networking_init_cms.page.languages');

        foreach ($languages as $lang) {
            $this->createHomePages($manager, $lang['locale']);
        }
    }

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     * @param $locale
     */
    public function createHomePages(ObjectManager $manager, $locale)
    {
        $homePage = new Page();

        $homePage->setLocale($locale);
        $homePage->setTitle('Homepage '.$locale);
        $homePage->setMetaKeyword('homepage');
        $homePage->setMetaDescription('This is the homepage');
        $homePage->setIsActive(true);
        $homePage->setIsHome(true);
        $homePage->setTemplate($this->getFirstTemplate());
        $homePage->setNavigationTitle('homepage');
        $homePage->setShowInNavigation(true);
        $homePage->setActiveFrom(new \DateTime('now'));
        $homePage->setActiveTill(new \DateTime('+ 5 years'));

        $manager->persist($homePage);
        $manager->flush();

        $this->addReference('homepage_'.$locale, $homePage);
    }

    /**
     * @return array
     */
    protected function getFirstTemplate()
    {
        $templates = $this->container->getParameter('networking_init_cms.page.templates');

        foreach ($templates as $key => $template) {
            return $key;
        }
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return 1;
    }
}