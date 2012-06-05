<?php
namespace Liuggio\HelpDeskTicketSystemBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Liuggio\HelpDeskTicketSystemBundle\Model\TicketInterface;

class TicketNotifier
{
    private $ticketManager;

    public function __construct($ticketManager) {
        $this->ticketManager = $ticketManager;
    }

    public function onTicketPersistOrUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $entityManager = $args->getEntityManager();
        //always notify the owner
        //notify the
        if ($entity instanceof $this->getTicketManager()->getTicketClass()) {
            //extract all the operators that belongs to this ticket
//            // notify the customercares of the corresponding category
//            $category = $entity->getCategory();
//            $operators = $category->getOperators();

//            $message = \Swift_Message::newInstance()
//                    ->setSubject('Hello Email')
//                    ->setFrom('send@example.com')
//                    ->setTo('recipient@example.com')
//                    ->setBody($this->renderView('HelloBundle:Hello:email.txt.twig', array('name' => $name)))
//            ;
//
//            $this->get('mailer')->send($message);
            //extract the customer that
        }
    }

    public function setTicketManager($ticketManager)
    {
        $this->ticketManager = $ticketManager;
    }

    public function getTicketManager()
    {
        return $this->ticketManager;
    }
}