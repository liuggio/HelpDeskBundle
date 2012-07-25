<?php
namespace Liuggio\HelpDeskBundle\Tests\Unit;

use Doctrine\ORM\Query\ResultSetMapping;

class TicketManagerTest extends \PHPUnit_Framework_TestCase
{
    private $em;

    private $ticketManager;

    private $aclManager;

    public function setUp()
    {
        //this is for the unit test
        $this->em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods(array('getRepository', 'persist', 'flush'))
            ->getMock();

        $this->aclManager = $this->getMockBuilder('\Liuggio\HelpDeskBundle\Service\AclManager')
            ->disableOriginalConstructor()
            ->setMethods(array('insertAce'))
            ->getMock();

        $this->ticketManager = new \Liuggio\HelpDeskBundle\Manager\TicketManager($this->em, 'Liuggio\HelpDeskBundle\Entity\Ticket', $this->aclManager, 'Liuggio\HelpDeskBundle\Entity\Comment', '');

    }


    public function test_createTicketWithUserAndCategory()
    {
        $that = $this;

        $repo = $this->getMockBuilder('\Tvision\Common\Repository')
            ->disableOriginalConstructor()
            ->setMethods(array('findOneByCode'))
            ->getMock();

        $repo->expects($this->once())
            ->method('findOneByCode')
            ->with(\Liuggio\HelpDeskBundle\Entity\TicketState::STATE_NEW)
            ->will($this->returnvalue(new \Liuggio\HelpDeskBundle\Entity\TicketState()));

        $this->em->expects($this->once())
            ->method('getRepository')
            ->with('LiuggioHelpDeskBundle:TicketState')
            ->will($this->returnValue($repo));

        $user = $this->getMock('User');
        $subject = 'TestSubject';
        $body = 'TestBody';
        $language = 'en';

        $this->aclManager->expects($this->once())
            ->method('insertAce')
            ->will($this->returnCallback(function ($entityTest, $userTest) use ($that, $user){
            $that->assertNotNull($entityTest);
            $that->assertNotNull($userTest);
            $that->assertEquals($user, $userTest);
        }));

        $newTicket = $this->ticketManager->createTicketWithUserAndCategory(new \Liuggio\HelpDeskBundle\Entity\Category(),$language, $user, $subject, $body);

        $this->assertNotNull($newTicket);
        $this->assertEquals($subject, $newTicket->getSubject());
        $this->assertEquals($body, $newTicket->getBody());
        $this->assertEquals($language, $newTicket->getLanguage());
        $this->assertEquals($user, $newTicket->getCreatedBy());
    }

    public function test_createCommentForTicket()
    {
        $that = $this;

        $user = $this->getMock('User');
        $ticket = new \Liuggio\HelpDeskBundle\Entity\Ticket();
        $commentBody = 'TestBody';

        $this->em->expects($this->once())
            ->method('persist')
            ->will($this->returnCallback(function ($comment) use ($that, $user, $ticket, $commentBody){
            $that->assertNotNull($ticket);
            $that->assertNotNull($commentBody);
            $that->assertEquals($user, $comment->getCreatedBy());
            $that->assertEquals($ticket, $comment->getTicket());
            $that->assertEquals($commentBody, $comment->getBody());
        }));

        $comment = $this->ticketManager->createCommentForTicket($ticket, $user, $commentBody);

    }


    public function test_closeTicket()
    {
        $that = $this;

        $repo = $this->getMockBuilder('\Tvision\Common\Repository')
            ->disableOriginalConstructor()
            ->setMethods(array('findOneByCode'))
            ->getMock();

        $stateClosed = new \Liuggio\HelpDeskBundle\Entity\TicketState();
        $stateClosed->setCode(\Liuggio\HelpDeskBundle\Entity\TicketState::STATE_CLOSED);

        $repo->expects($this->once())
            ->method('findOneByCode')
            ->with(\Liuggio\HelpDeskBundle\Entity\TicketState::STATE_CLOSED)
            ->will($this->returnValue($stateClosed));

        $this->em->expects($this->once())
            ->method('getRepository')
            ->with('LiuggioHelpDeskBundle:TicketState')
            ->will($this->returnValue($repo));

        $state = new \Liuggio\HelpDeskBundle\Entity\TicketState();
        $state->setCode(\Liuggio\HelpDeskBundle\Entity\TicketState::STATE_NEW);

        $ticket = new \Liuggio\HelpDeskBundle\Entity\Ticket();
        $ticket->setState(new \Liuggio\HelpDeskBundle\Entity\TicketState());

        $this->em->expects($this->once())
            ->method('persist')
            ->will($this->returnCallback(function ($ticket) use ($that){
                $that->assertNotNull($ticket);
                $that->assertNotNull($ticket->getState());
                $that->assertEquals(\Liuggio\HelpDeskBundle\Entity\TicketState::STATE_CLOSED, $ticket->getState()->getCode());
        }));

        $this->ticketManager->closeTicket($ticket);

    }

}