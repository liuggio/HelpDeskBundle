<?php

namespace Liuggio\HelpDeskBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use Liuggio\HelpDeskBundle\Entity\Category;

class LoadCategory extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(\Doctrine\Common\Persistence\ObjectManager $em)
    {
        $category = new Category();
        $category->setName('Administrative');
        $category->setDescription('Administrative problems');
        $category->setWeight(1);
        $category->setIsEnable(true);
        $em->persist($category);
        $this->addReference('administrative_category', $category);

        $category = new Category();
        $category->setName('Other');
        $category->setDescription('Other problems');
        $category->setWeight(2);
        $category->setIsEnable(true);
        $em->persist($category);
        $this->addReference('other_category', $category);

        $category = new Category();
        $category->setName('Member Card');
        $category->setDescription('Member Card');
        $category->setWeight(3);
        $category->setIsEnable(true);
        $em->persist($category);
        $this->addReference('member_card', $category);

        $em->flush();

    }

    public function getOrder()
    {
        return 40; // the order in which fixtures will be loaded
    }
}