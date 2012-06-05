<?php

namespace Liuggio\HelpDeskTicketSystemBundle\Model;

use Doctrine\Common\Persistence\ObjectManager;


abstract class TicketManager implements TicketManagerInterface
{
    protected $objectManager;
    protected $ticketClass;
    protected $commentClass;
    protected $securityContext;
    protected $ticketRepository;
    protected $commentRepository;

    function __construct(ObjectManager $objectManager, $ticketClass, $commentClass, $securityContext)
    {
        $this->objectManager = $objectManager;
        $this->ticketClass = $ticketClass;
        $this->commentClass = $commentClass;
        $this->securityContext = $securityContext;
        $this->ticketRepository = $this->objectManager->getRepository($ticketClass);
        $this->commentRepository = $this->objectManager->getRepository($commentClass);
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

    public function setSecurityContext($securityContext)
    {
        $this->securityContext = $securityContext;
    }

    public function getSecurityContext()
    {
        return $this->securityContext;
    }

    public function setTicketRepository($ticketRepository)
    {
        $this->ticketRepository = $ticketRepository;
    }

    public function getTicketRepository()
    {
        return $this->ticketRepository;
    }

    public function setCommentClass($commentClass)
    {
        $this->commentClass = $commentClass;
    }

    public function getCommentClass()
    {
        return $this->commentClass;
    }

    public function setCommentRepository($commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }

    public function getCommentRepository()
    {
        return $this->commentRepository;
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
    /**
    * Returns an empty ticket instance
    *
    * @return TicketInterface
    */
    public function createComment()
    {
        $class = $this->getCommentClass();
        $comment = new $class;

        return $comment;
    }

}