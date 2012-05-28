<?php

namespace Liuggio\HelpDeskTicketSystemBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Liuggio\HelpDeskTicketSystemBundle\Entity\TicketState;

class TicketControllerTest extends WebTestCase
{
    private $categoryLabel;
    private $client;
    private $backLinkLabel;
    private $addCommentButton;
    private $closeTicketButton;
    private $rateButton;
    private $commentBody = "Hello comment";
    private $commentTextField;
    
    
   public function setUp()
   {
       parent::setUp();
       $this->client = static::createClient();
       $this->categoryLabel = "Category";
       $this->backLinkLabel = "Back to the list";
       $this->addCommentButton = "Add Comment";
       $this->closeTicketButton = "Close Ticket";
       $this->rateButton = "Rate our service";
       $this->commentTextField = "liuggio_helpdeskticketsystembundle_commenttype[body]";
   }
    
    /**
    * @dataProvider getTicketData
    */
    public function testNew($testSubject, $testBody)
    {
        $crawler = $this->client->request('GET', '/customer-care/ticket/new');
        // assert();
        $this->assertEquals(200,$this->client->getResponse()->getStatusCode());
        // assert();
        $this->assertTrue($crawler->filter('label:contains("'. $this->categoryLabel .'")')->count() > 0);
        // assert();
        $this->assertTrue($crawler->filter('a:contains("'. $this->backLinkLabel .'")')->count() > 0);
        
        $form = $crawler->selectButton('Create')->form();
         
        $crawler = $this->client->submit(
            $form, array(
                'liuggio_helpdeskticketsystembundle_tickettype[subject]' => $testSubject,
                'liuggio_helpdeskticketsystembundle_tickettype[body]' => $testBody
            )
        );

        $crawler = $this->client->followRedirect();
        // assert();
        $this->assertTrue($crawler->filter('td:contains("'. $testSubject .'")')->count() > 0);
        // assert();
        $this->assertTrue($crawler->filter('td:contains("'. $testBody .'")')->count() > 0);
        
        $em = $this->client->getContainer()->get('doctrine.orm.entity_manager');
        $ticketCreated =  $em->getRepository('\Liuggio\HelpDeskTicketSystemBundle\Entity\Ticket')
                ->findOneBy(array('subject'=>$testSubject, 'body' =>$testBody));
        
        //asserting that the ticket has the state to NEW
        $this->assertTrue($ticketCreated->getState()->getCode() == \Liuggio\HelpDeskTicketSystemBundle\Entity\TicketState::STATE_NEW);
    }
    
    
    /**
    * @dataProvider getTicketData
    */
    public function testShow($testSubject, $testBody)
    {
        $em = $this->client->getContainer()->get('doctrine.orm.entity_manager');
        $ticket =  $em->getRepository('\Liuggio\HelpDeskTicketSystemBundle\Entity\Ticket')
                ->findOneBy(array('subject'=>$testSubject, 'body' =>$testBody));
        $ticketId = $ticket->getId();
        
        $crawler = $this->client->request('GET', '/customer-care/ticket/'.$ticketId.'/show');
        // assert();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        
        // assert();
        $this->assertGreaterThan(0, $crawler->filter('td:contains("' . $testSubject . '")')->count());
        // assert();
        $this->assertGreaterThan(0, $crawler->filter('td:contains("' . $testSubject . '")')->count());
        
        if($ticket->getState()->getCode() != TicketState::STATE_CLOSED){
            
            // Let's add a comment to our ticket
            // assert();
            $this->assertGreaterThan(0, $crawler->filter('button:contains("' . $this->addCommentButton . '")')->count());
            $form = $crawler->selectButton($this->addCommentButton)->form();
            $crawler = $this->client->submit(
            $form, array($this->commentTextField => $this->commentBody ));
            $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
            $crawler = $this->client->followRedirect();
            // assert();
            $this->assertGreaterThan(0, $crawler->filter('td:contains("' . $this->commentBody . '")')->count());
            // Remove the comment just created
            $em = $this->client->getContainer()->get('doctrine.orm.entity_manager');
            $comment =  $em->getRepository('\Liuggio\HelpDeskTicketSystemBundle\Entity\Comment')
                ->findOneBy(array('body' =>$this->commentBody));
            
            if($comment){
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
        else{
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

        $crawler = $this->client->request('GET', '/customer-care/ticket/all');
        // assert();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        // assert();
        $this->assertGreaterThan(0, $crawler->filter('html:contains("' . $testSubject . '")')->count());
        // assert();
        $this->assertGreaterThan(0, $crawler->filter('html:contains("' . $testBody . '")')->count());
        
        $crawler = $this->client->request('GET', '/customer-care/ticket/open');
        // assert();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        // assert();
        $this->assertEquals(0, $crawler->filter('html:contains("' . $testSubject . '")')->count());
        // assert();
        $this->assertEquals(0, $crawler->filter('html:contains("' . $testBody . '")')->count());
        
        $crawler = $this->client->request('GET', '/customer-care/ticket/all');
        // assert();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        
        $em = $this->client->getContainer()->get('doctrine.orm.entity_manager');
        $ticket =  $em->getRepository('\Liuggio\HelpDeskTicketSystemBundle\Entity\Ticket')
                ->findOneBy(array('subject'=>$testSubject, 'body' =>$testBody));
        $ticketId = $ticket->getId();
        // assert();
        $this->assertGreaterThan(0, $crawler->filter('a:contains("' . $ticketId . '")')->count());
        $crawler = $this->client->request('GET', '/customer-care/ticket/'.$ticketId.'/show');
        // assert();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        
        //cleaning
        $em->remove($ticket);
        $em->flush();
         
    }
    
       public function getTicketData()
       {
        $subject = 'test-subject';
        $body = 'test-body';
        return array(
            array($subject . "_1",$body . "_1"),
            array($subject . "_2",$body . "_2"),
            array($subject . "_3",$body . "_3")
            );
       }   
   
}