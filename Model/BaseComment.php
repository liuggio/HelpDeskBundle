<?php

namespace Liuggio\HelpDeskBundle\Model;

use Liuggio\HelpDeskBundle\Model\Comment as AbstractComment;

/**
 * Liuggio\HelpDeskBundle\Entity\Comment
 */
class BaseComment extends AbstractComment
{
    /**
     * @var integer $id
     */
    protected $id;

    /**
     * @var text $body
     */
    protected $body;

    /**
     * @var datetime $createdAt
     */
    protected $createdAt;

    /**
     * @var Application\Sonata\UserBundle\Entity\User $createdBy
     */
    protected $createdBy;

    /**
     * @var datetime $modifiedAt
     */
    protected $modifiedAt;

    /**
     * @var Liuggio\HelpDeskBundle\Entity\Ticket $ticket
     */
    protected $ticket;


    /**
     * @return string
     */
    public function __toString()
    {
        return '#' . $this->getId();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set body
     *
     * @param text $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * Get body
     *
     * @return text
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set createdAt
     *
     * @param datetime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Get createdAt
     *
     * @return datetime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set modifiedAt
     *
     * @param datetime $modifiedAt
     */
    public function setModifiedAt($modifiedAt)
    {
        $this->modifiedAt = $modifiedAt;
    }

    /**
     * Get modifiedAt
     *
     * @return datetime
     */
    public function getModifiedAt()
    {
        return $this->modifiedAt;
    }

    /**
     * @param \Liuggio\HelpDeskBundle\Model\Ticket $ticket
     */
    public function setTicket($ticket)
    {
        $this->ticket = $ticket;
    }

    /**
     * @return \Liuggio\HelpDeskBundle\Model\Ticket
     */
    public function getTicket()
    {
        return $this->ticket;
    }

    /**
     * Set createdBy
     *
     * @param Application\Sonata\UserBundle\Entity\User $createdBy
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
    }

    /**
     * Get createdBy
     *
     * @return Tvision\Bundle\UserBundle\Entity\User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    public function prePersist()
    {
        $now = new \DateTime('NOW');
        $this->setUpdatedAt($now);
        $this->setCreatedAt($now);
    }

    public function preUpdate()
    {
        $now = new \DateTime('NOW');
        $this->setUpdatedAt($now);
    }

    /**
     * @var datetime $updatedAt
     */
    protected $updatedAt;


    /**
     * Set updatedAt
     *
     * @param datetime $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * Get updatedAt
     *
     * @return datetime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}