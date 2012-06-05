<?php

namespace Liuggio\HelpDeskTicketSystemBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use Liuggio\HelpDeskTicketSystemBundle\Entity\TicketState;
use Liuggio\HelpDeskTicketSystemBundle\Entity\Ticket;
use Liuggio\HelpDeskTicketSystemBundle\Entity\Category;

class LoadTicketState extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(\Doctrine\Common\Persistence\ObjectManager $em)
    {
        $state = new TicketState();
        $state->setCode('new');
        $state->setDescription('The ticket is created, a n operator has replied to this ticket');
        $state->setWeight(1);
        $em->persist($state);
        $this->addReference('new_ticket_state', $state);

        $state = new TicketState();
        $state->setCode('pending');
        $state->setDescription('This ticket must be processed');
        $state->setWeight(2);
        $em->persist($state);
        $this->addReference('pending_ticket_state', $state);

        $state = new TicketState();
        $state->setCode('replied');
        $state->setDescription('An operator has replied to this ticket');
        $state->setWeight(3);
        $em->persist($state);
        $this->addReference('replied_ticket_state', $state);

        $state = new TicketState();
        $state->setCode('closed');
        $state->setDescription('This ticket is closed');
        $state->setWeight(4);
        $em->persist($state);
        $this->addReference('closed_ticket_state', $state);

        $em->flush();
    }
    
    public function getOrder()
    {
        return 20; // the order in which fixtures will be loaded
    }
}