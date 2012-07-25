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

        $this->ticketManager = new \Liuggio\HelpDeskBundle\Manager\TicketManager($this->em, 'Liuggio\HelpDeskBundle\Entity\Ticket', $this->aclManager);

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

}