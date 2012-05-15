<?php

namespace Liuggio\HelpDeskTicketSystemBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Liuggio\HelpDeskTicketSystemBundle\Entity\Category;

/**
 * Liuggio\HelpDeskTicketSystemBundle\Entity\Ticket
 */
class Ticket
{
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var string $subject
     */
    private $subject;

    /**
     * @var text $body
     */
    private $body;

    /**
     * @var string $language
     */
    private $language;

    /**
     * @var datetime $createdAt
     */
    private $createdAt;

    /**
     * @var datetime $updatedAt
     */
    private $updatedAt;

    /**
     * @var TicketState $state
     */
    private $state;

    /**
     * @var \Liuggio\HelpDeskTicketSystemBundle\Entity\Category $category
     */
    private $category;

    /**
     * @var Liuggio\HelpDeskTicketSystemBundle\Entity\Comment
     */
    private $comments;

    public function __construct()
    {
        $this->comments = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getSubject();
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
     * Set subject
     *
     * @param string $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * Get subject
     *
     * @return string 
     */
    public function getSubject()
    {
        return $this->subject;
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
     * Set language
     *
     * @param string $language
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * Get language
     *
     * @return string 
     */
    public function getLanguage()
    {
        return $this->language;
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

 

    /**
     * @param \Liuggio\HelpDeskTicketSystemBundle\Entity\Category $category
     */
    public function setCategory(\Liuggio\HelpDeskTicketSystemBundle\Entity\Category $category)
    {
        $this->category = $category;
    }

    /**
     * @return \Liuggio\HelpDeskTicketSystemBundle\Entity\Category
     */
    public function getCategory()
    {
        return $this->category;
    }
    
    /**
     * Add comments
     *
     * @param Liuggio\HelpDeskTicketSystemBundle\Entity\Comment $comments
     */
    public function addComment(\Liuggio\HelpDeskTicketSystemBundle\Entity\Comment $comments)
    {
        $this->comments[] = $comments;
    }

    /**
     * Get comments
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Set state
     *
     * @param Liuggio\HelpDeskTicketSystemBundle\Entity\TicketState $state
     */
    public function setState(\Liuggio\HelpDeskTicketSystemBundle\Entity\TicketState $state)
    {
        $this->state = $state;
    }

    /**
     * Get state
     *
     * @return Liuggio\HelpDeskTicketSystemBundle\Entity\TicketState 
     */
    public function getState()
    {
        return $this->state;
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
     * @var Application\Sonata\UserBundle\Entity\User
     */
    private $createdBy;


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
     * @return Application\Sonata\UserBundle\Entity\User 
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }
}