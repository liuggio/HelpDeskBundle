<?php

namespace Liuggio\HelpDeskBundle\Model;

use Doctrine\Common\Persistence\ObjectManager;

interface TicketManagerInterface
{

    public function setTicketClass($ticketClass);

    public function getTicketClass();

    public function setObjectManager(ObjectManager $objectManager);

    public function getObjectManager();

    public function setTicketRepository($repository);

    public function getTicketRepository();

}