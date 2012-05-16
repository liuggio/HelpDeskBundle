<?php

namespace Liuggio\HelpDeskTicketSystemBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TicketControllerTest extends WebTestCase
{
    public function testCompleteNewScenario()
    {

        $testSubject = 'test-subject' .  rand();
        $testBody = 'test-body'.  rand();

        // Create a new client to browse the application
        $client = static::createClient();

        $crawler = $client->request('GET', '/customer-care/ticket/new');

        $this->assertTrue(200 === $client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Create')->form();

        $crawler = $client->submit(
            $form, array(
                'liuggio_helpdeskticketsystembundle_tickettype[subject]' => $testSubject,
                'liuggio_helpdeskticketsystembundle_tickettype[body]' => $testBody
            )
        );

        $crawler = $client->followRedirect();
        $this->assertTrue($crawler->filter('td:contains("'. $testSubject .'")')->count() > 0);
        $this->assertTrue($crawler->filter('td:contains("en")')->count() > 0);
        //cleaning
        $em = $client->getContainer()->get('doctrine.orm.entity_manager');
        $ticketCreated =  $em->getRepository('\Liuggio\HelpDeskTicketSystemBundle\Entity\Ticket')->findOneBy(array('subject'=>$testSubject, 'body' =>$testBody));
        //asserting that the ticket has the state to NEW
        $this->assertTrue($ticketCreated->getState()->getCode() == \Liuggio\HelpDeskTicketSystemBundle\Entity\TicketState::STATE_NEW);
        $em->remove($ticketCreated);
        $em->flush();
    }
}