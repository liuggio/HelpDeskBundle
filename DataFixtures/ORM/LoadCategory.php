<?php

namespace Tvision\Bundle\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use Liuggio\HelpDeskTicketSystemBundle\Entity\Category;

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

        $em->flush();
        
        $category = new Category();
        $category->setName('Other');
        $category->setDescription('Other problems');
        $category->setWeight(2);
        $category->setIsEnable(true);
        $em->persist($category);

        $em->flush();
        
    }
    
    public function getOrder()
    {
        return 100; // the order in which fixtures will be loaded
    }
}