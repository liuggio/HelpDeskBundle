<?php

namespace Liuggio\HelpDeskBundle\Model;

use Doctrine\Common\Persistence\ObjectManager;


abstract class TicketManager implements TicketManagerInterface
{
    protected $objectManager;
    protected $ticketClass;
    protected $ticketRepository;
    protected $aclManager;
    protected $ticketCommentClass;
    protected $categoryClass;

    function __construct($objectManager, $ticketClass, $aclManager, $ticketCommentClass, $categoryClass)
    {
        $this->objectManager = $objectManager;
        $this->ticketClass = $ticketClass;
        $this->aclManager = $aclManager;
        $this->ticketCommentClass = $ticketCommentClass;
        $this->categoryClass = $categoryClass;
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

    public function getCategoryRepository()
    {
        return $this->objectManager->getRepository($this->getCategoryClass());
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

    public function setCategoryClass($categoryClass)
    {
        $this->categoryClass = $categoryClass;
    }

    public function getCategoryClass()
    {
        return $this->categoryClass;
    }

    public function setTicketCommentClass($ticketCommentClass)
    {
        $this->ticketCommentClass = $ticketCommentClass;
    }

    public function getTicketCommentClass()
    {
        return $this->ticketCommentClass;
    }


}