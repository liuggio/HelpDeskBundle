<?php

namespace Liuggio\HelpDeskTicketSystemBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Liuggio\HelpDeskTicketSystemBundle\Entity\TicketState;

class TicketControllerTest extends WebTestCase
{
    private $categoryLabel= "Category";
    private $client;
    private $backLinkLabel = "Back to the list";
    private $addCommentButton = "Add Comment";
    private $closeTicketButton = "Close Ticket";
    private $rateButton = "Rate our service";
    private $commentBody = "Hello comment";
    private $commentTextField = "liuggio_helpdeskticketsystembundle_commenttype[body]";
    private $loginButton = "submit";

    private $user_operator1 = array( "username" => "operator1@mail.com", "password" => "operator1");
    private $user_operator2 = array( "username" => "operator2@mail.com", "password" => "operator2");
    private $user_customer1 = array( "username" => "customer1@mail.com", "password" => "customer1");
    private $user_customer2 = array( "username" => "customer2@mail.com", "password" => "customer2");


    public function setUp()
    {
        parent::setUp();

        $this->client = static::createClient();

    }

    /**
     * @dataProvider getTicketData
     */
    public function testNew($testSubject, $testBody)
    {
        //Client Login
        $this->setAuthClient($this->user_customer1);

        $crawler = $this->client->request('GET', '/customer-care/ticket/new');
        // assert();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        // assert();
        $this->assertTrue($crawler->filter('label:contains("' . $this->categoryLabel . '")')->count() > 0);
        // assert();
        $this->assertTrue($crawler->filter('a:contains("' . $this->backLinkLabel . '")')->count() > 0);

        $form = $crawler->selectButton('Create')->form();

        $crawler = $this->client->submit(
            $form, array(
                'liuggio_helpdeskticketsystembundle_tickettype[subject]' => $testSubject,
                'liuggio_helpdeskticketsystembundle_tickettype[body]' => $testBody
            )
        );

        $crawler = $this->client->followRedirect();
        // assert();
        $this->assertTrue($crawler->filter('td:contains("' . $testSubject . '")')->count() > 0);
        // assert();
        $this->assertTrue($crawler->filter('td:contains("' . $testBody . '")')->count() > 0);

        $em = $this->client->getContainer()->get('doctrine.orm.entity_manager');
        $ticketCreated = $em->getRepository('\Liuggio\HelpDeskTicketSystemBundle\Entity\Ticket')
            ->findOneBy(array('subject' => $testSubject, 'body' => $testBody));

        //asserting that the ticket has the state to NEW
        $this->assertTrue($ticketCreated->getState()->getCode() == \Liuggio\HelpDeskTicketSystemBundle\Entity\TicketState::STATE_NEW);
    }


    /**
     * @dataProvider getTicketData
     * @depends testNew
     *
     */
    public function testShow($testSubject, $testBody)
    {
        $this->setAuthClient($this->user_customer2);
        $em = $this->client->getContainer()->get('doctrine.orm.entity_manager');

        // assert() user can NOT access the resource
        $ticket = $em->getRepository('\Liuggio\HelpDeskTicketSystemBundle\Entity\Ticket')
            ->findOneBy(array('subject' => $testSubject, 'body' => $testBody));
        $ticketId = $ticket->getId();
        $crawler = $this->client->request('GET', '/customer-care/ticket/' . $ticketId . '/show');
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());

        // assert() user can access the resource
        $ticket = $em->getRepository('\Liuggio\HelpDeskTicketSystemBundle\Entity\Ticket')
            ->findOneBy(array('subject' => 'ticket3') );
        $ticketId = $ticket->getId();
        $crawler = $this->client->request('GET', '/customer-care/ticket/' . $ticketId . '/show');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        //echo $ticket->getId();die;
        // Test show view;
        $this->assertGreaterThan(0, $crawler->filter('td:contains("administrative: open ticket : with comments")')->count());
        // assert();
        $this->assertGreaterThan(0, $crawler->filter('td:contains("ticket3")')->count());
    }

    /**
     * @dataProvider getTicketData
     * @depends testNew
     *
     */
    public function testAddComment($testSubject, $testBody){

        $this->setAuthClient($this->user_customer2);
        $em = $this->client->getContainer()->get('doctrine.orm.entity_manager');

        $ticket = $em->getRepository('\Liuggio\HelpDeskTicketSystemBundle\Entity\Ticket')
            ->findOneBy(array('subject' => "ticket3") );
        $ticketId = $ticket->getId();
        $crawler = $this->client->request('GET', '/customer-care/ticket/' . $ticketId . '/show');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        if ($ticket->getState()->getCode() != TicketState::STATE_CLOSED) {

            // Test add comment
            $this->assertGreaterThan(0, $crawler->filter('button:contains("' . $this->addCommentButton . '")')->count());

            $form = $crawler->selectButton($this->addCommentButton)->form();
            $crawler = $this->client->submit(
                $form, array($this->commentTextField => $this->commentBody));

            $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
            $crawler = $this->client->followRedirect();
            // assert();
            $this->assertGreaterThan(0, $crawler->filter('td:contains("' . $this->commentBody . '")')->count());

            // Remove the comment just created
            $comment = $em->getRepository('\Liuggio\HelpDeskTicketSystemBundle\Entity\Comment')
                ->findOneBy(array('body' => $this->commentBody));
            if ($comment) {
                //cleaning
                $em->remove($comment);
                $em->flush();
            }

            // assert();
            $this->assertGreaterThan(0, $crawler->filter('button:contains("' . $this->closeTicketButton . '")')->count());

            // Test Close Ticket Form Action
            $form = $crawler->selectButton($this->closeTicketButton)->form();
            $crawler = $this->client->submit($form, array());

            // Here we should be inside rate.html.twig
            $this->assertGreaterThan(0, $crawler->filter('button:contains("' . $this->rateButton . '")')->count());

            // Test Close Ticket Form Action
            $form = $crawler->selectButton($this->rateButton)->form();
            $crawler = $this->client->submit($form, array());
            $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
            $crawler = $this->client->followRedirect();
            // select Here we should be inside /ticket/


        }
        else {
            // assert();
            $this->assertEquals(0, $crawler->filter('button:contains("' . $this->addCommentButton . '")')->count());
            // assert();
            $this->assertEquals(0, $crawler->filter('button:contains("' . $this->closeTicketButton . '")')->count());
        }

    }

    /**
     * @dataProvider getTicketData
     */
    public function test_Index_Search($testSubject, $testBody)
    {
        $em = $this->client->getContainer()->get('doctrine.orm.entity_manager');

        $this->setAuthClient($this->user_operator1);

        $crawler = $this->client->request('GET', '/customer-care/operator/ticket');
        // assert();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        // assert();
        $this->assertGreaterThan(0, $crawler->filter('html:contains("' . $testSubject . '")')->count());
        // assert();
        $this->assertGreaterThan(0, $crawler->filter('html:contains("' . $testBody . '")')->count());

        $crawler = $this->client->request('GET', '/customer-care/operator/ticket/closed');
        // assert();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        // assert();
        $this->assertEquals(0, $crawler->filter('html:contains("' . $testSubject . '")')->count());
        // assert();
        $this->assertEquals(0, $crawler->filter('html:contains("' . $testBody . '")')->count());


        $crawler = $this->client->request('GET', '/customer-care/operator/ticket/open');
        // assert();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        //Test search
        $form = $crawler->selectButton('Search Ticket')->form();
        $this->client->submit($form, array(
                                            's[request_pattern]' => $testSubject
                                            )
                            );

        $this->GreaterThan(0, $crawler->filter('html:contains("' . $testSubject . '")')->count());



        $ticket = $em->getRepository('\Liuggio\HelpDeskTicketSystemBundle\Entity\Ticket')
            ->findOneBy(array('subject' => $testSubject, 'body' => $testBody));
        $ticketId = $ticket->getId();

        //cleaning
        $em->remove($ticket);
        $em->flush();

    }



    public function getTicketData()
    {
        $subject = 'test-subject';
        $body = 'test-body';
        return array(
            array($subject . "_1", $body . "_1"),
            array($subject . "_2", $body . "_2"),
            array($subject . "_3", $body . "_3")
        );
    }


    protected function setAuthClient($credentials)
    {
        $crawler = $this->client->request('GET', '/login');

        $form = $crawler->selectButton('_submit')->form();

        $this->client->submit($form, array(
            '_username'              => $credentials['username'],
            '_password'  => $credentials['password'],
        ));

        // assert();
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

}