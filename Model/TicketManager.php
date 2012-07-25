<?php

namespace Liuggio\HelpDeskBundle\Model;

use Doctrine\Common\Persistence\ObjectManager;


abstract class TicketManager implements TicketManagerInterface
{
    protected $objectManager;
    protected $ticketClass;
    protected $ticketRepository;
    protected $aclManager;

    function __construct($objectManager, $ticketClass, $aclManager)
    {
        $this->objectManager = $objectManager;
        $this->ticketClass = $ticketClass;
        $this->aclManager = $aclManager;
    }

    public function setTicketClass($ticketClass)
    {
        $this->ticketClass = $ticketClass;
    }

    public function getTicketClass()
    {
        return $this->ticketClass;
    }

    public function setObjectManager(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function getObjectManager()
    {
        return $this->objectManager;
    }


    public function setTicketRepository($ticketRepository)
    {
        $this->ticketRepository = $ticketRepository;
    }

    public function getTicketRepository()
    {
        if (null == $this->ticketRepository) {
            $this->setTicketRepository($this->objectManager->getRepository($this->getTicketClass()));
        }
        return $this->ticketRepository;
    }

    /**
     * Returns an empty ticket instance
     *
     * @return TicketInterface
     */
    public function createTicket()
    {
        $class = $this->getTicketClass();
        $ticket = new $class;

        return $ticket;
    }

    public function setAclManager($aclManager)
    {
        $this->aclManager = $aclManager;
    }

    public function getAclManager()
    {
        return $this->aclManager;
    }


}