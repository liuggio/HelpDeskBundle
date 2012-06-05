<?php

namespace Liuggio\HelpDeskTicketSystemBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Liuggio\HelpDeskTicketSystemBundle\Entity\Ticket;
use Liuggio\HelpDeskTicketSystemBundle\Entity\TicketState;
use Tvision\Bundle\UserBundle\Entity\User;
use Tvision\Bundle\UserBundle\Entity\Group;

class LoadTicketAndUser extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(\Doctrine\Common\Persistence\ObjectManager $em)
    {

        $administrative_category = $em->merge($this->getReference('administrative_category'));
        $other_category = $em->merge($this->getReference('other_category'));

        // Load group
        $group_customercare = new Group("customercare_group", array("ROLE_CUSTOMERCARE"));
        $em->persist($group_customercare);
        $this->addReference('group_customercare', $group_customercare);


        //Load users
        $user1 = new User();
        $encoder = $this->container->get('security.encoder_factory')->getEncoder($user1);
        $user1->setPassword($encoder->encodePassword('operator1', $user1->getSalt()));
        $user1->setEmail('operator1@mail.com');
        $user1->setEnabled(true);
        $user1->addGroup($group_customercare);
        $em->persist($user1);
        $this->addReference('user_operator1', $user1);

        $em->flush();
        //add operator to a category
        $administrative_category->addUser($user1);
        $other_category->addUser($user1);

        $user2 = new User();
        $encoder = $this->container->get('security.encoder_factory')->getEncoder($user2);
        $user2->setPassword($encoder->encodePassword('operator2', $user2->getSalt()));
        $user2->setEmail('operator2@mail.com');
        $user2->setEnabled(true);
        $user2->addGroup($group_customercare);
        $em->persist($user2);
        $this->addReference('user_operator2', $user2);

        $em->flush();
        //add operator to a category
        $other_category->addUser($user2);

        $user3 = new User();
        $encoder = $this->container->get('security.encoder_factory')->getEncoder($user3);
        $user3->setPassword($encoder->encodePassword('customer1', $user3->getSalt()));
        $user3->setEmail('customer1@mail.com');
        $user3->setEnabled(true);
        $user3->addGroup($group_customercare);
        $em->persist($user3);
        $this->addReference('user_customer1', $user3);

        $em->flush();

        $user4 = new User();
        $encoder = $this->container->get('security.encoder_factory')->getEncoder($user4);
        $user4->setPassword($encoder->encodePassword('customer2', $user4->getSalt()));
        $user4->setEmail('customer2@mail.com');
        $user4->setEnabled(true);
        $user4->addGroup($group_customercare);
        $em->persist($user4);
        $this->addReference('user_customer2', $user4);

        $em->flush();


        $aclManager = $this->container->get('liuggio_help_desk_ticket_system.acl.manager');

        $ticket = new Ticket();
        $ticket->setSubject("ticket1");
        $ticket->setBody("administrative: new ticket : without comments");
        $ticket->setState($em->merge($this->getReference("new_ticket_state")));
        $ticket->setCategory($em->merge($this->getReference('administrative_category')));
        $ticket->setCreatedBy($user3);
        $ticket->setLanguage('en');
        $em->persist($ticket);
        $this->addReference('ticket1', $ticket);
        $em->flush();
        //Set the ACE
        $aclManager->insertAce($ticket, $user3);

        $ticket = new Ticket();
        $ticket->setSubject("ticket2");
        $ticket->setBody("other: closed ticket : without comments");
        $ticket->setRate("2");
        $ticket->setState($em->merge($this->getReference("closed_ticket_state")));
        $ticket->setCategory($em->merge($this->getReference('other_category')));
        $ticket->setCreatedBy($user3);
        $ticket->setLanguage('en');
        $em->persist($ticket);
        $this->addReference('ticket2', $ticket);
        $em->flush();

        //Set the ACE
        $aclManager->insertAce($ticket, $user3);

        $ticket = new Ticket();
        $ticket->setSubject("ticket3");
        $ticket->setBody("administrative: open ticket : with comments");
        $ticket->setState($em->merge($this->getReference("new_ticket_state")));
        $ticket->setCategory($em->merge($this->getReference('administrative_category')));
        $ticket->setCreatedBy($user4);
        $ticket->setLanguage('en');
        $em->persist($ticket);
        $this->addReference('ticket3', $ticket);
        $em->flush();
        //Set the ACE
        $aclManager->insertAce($ticket, $user4);

        $ticket = new Ticket();
        $ticket->setSubject("ticket4");
        $ticket->setBody("administrative: closed ticket : with comments");
        $ticket->setState($em->merge($this->getReference("closed_ticket_state")));
        $ticket->setCategory($em->merge($this->getReference('administrative_category')));
        $ticket->setCreatedBy($user4);
        $ticket->setLanguage('en');
        $em->persist($ticket);
        $this->addReference('ticket4', $ticket);
        $em->flush();
        //Set the ACE
        $aclManager->insertAce($ticket, $user4);
    }

    public function getOrder()
    {
        return 90; // the order in which fixtures will be loaded
    }
}