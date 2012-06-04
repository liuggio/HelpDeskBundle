<?php

namespace Liuggio\HelpDeskTicketSystemBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use Liuggio\HelpDeskTicketSystemBundle\Entity\Comment;

class LoadComment extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(\Doctrine\Common\Persistence\ObjectManager $em)
    {
        $comment = new Comment();
        $comment->setBody("I am the comment of the ticket_3");
        $comment->setTicket( $this->getReference('ticket3') );
        $comment->setCreatedBy( $this->getReference('user_customer2') );
        $em->persist($comment);
        $this->addReference('comment1', $comment);

        $em->flush();

        $comment = new Comment();
        $comment->setBody("operator1 comment of the ticket_3");
        $comment->setTicket( $this->getReference('ticket3') );
        $comment->setCreatedBy( $this->getReference('user_operator1') );
        $em->persist($comment);
        $this->addReference('comment2', $comment);

        $em->flush();

        $comment = new Comment();
        $comment->setBody("I am the comment of the ticket_4");
        $comment->setTicket( $this->getReference('ticket4') );
        $comment->setCreatedBy( $this->getReference('user_customer2') );
        $em->persist($comment);
        $this->addReference('comment3', $comment);

        $em->flush();

    }

    public function getOrder()
    {
        return 100; // the order in which fixtures will be loaded
    }
}