<?php

namespace Liuggio\HelpDeskBundle\Model;

use Liuggio\HelpDeskBundle\Exception;

abstract class Ticket implements TicketInterface
{
    CONST RATE_STAR_ONE = 1;
    CONST RATE_STAR_TWO = 2;
    CONST RATE_STAR_THREE = 3;
    CONST RATE_STAR_FOUR = 4;
    CONST RATE_STAR_FIVE = 5;

    CONST STATE_OPEN = 'open'; //means pending, new, replied, pending
    CONST STATE_CLOSE = 'closed'; //means closed
    CONST STATE_ALL = 'all';
    CONST STATE_OPERATOR_OPEN = 'operator_open'; //means pending, new
    CONST STATE_OPERATOR_CLOSE = 'operator_closed'; //means replied, close
    CONST STATE_OPERATOR_ALL = 'operator_all';

    static $STATE = array(
        self::STATE_OPEN => array(TicketState::STATE_NEW, TicketState::STATE_PENDING, TicketState::STATE_REPLIED),
        self::STATE_CLOSE => array(TicketState::STATE_CLOSED),
        self::STATE_ALL => array(TicketState::STATE_NEW, TicketState::STATE_PENDING, TicketState::STATE_REPLIED, TicketState::STATE_CLOSED),
        self::STATE_OPERATOR_OPEN => array(TicketState::STATE_NEW, TicketState::STATE_PENDING),
        self::STATE_OPERATOR_CLOSE => array(TicketState::STATE_CLOSED, TicketState::STATE_REPLIED),
        self::STATE_OPERATOR_ALL => array(TicketState::STATE_NEW, TicketState::STATE_PENDING, TicketState::STATE_REPLIED, TicketState::STATE_CLOSED),
    );
    /**
     * @var integer $id
     */
    protected $id;

    /**
     * @var string $subject
     */
    protected $subject;

    /**
     * @var text $body
     */
    protected $body;

    /**
     * @var string $language
     */
    protected $language;

    /**
     * @var datetime $createdAt
     */
    protected $createdAt;

    /**
     * @var Liuggio\HelpDeskBundle\Model\TicketState $state
     */
    protected $state;

    /**
     * @var \Liuggio\HelpDeskBundle\Model\Category $category
     */
    protected $category;

    /**
     * @var Liuggio\HelpDeskBundle\Model\Comment
     */
    protected $comments;

    /**
     * @var Application\Sonata\UserBundle\Entity\User
     */
    protected $createdBy;

    /**
     * @var datetime $updatedAt
     */
    protected $updatedAt;


    /**
     * @var int or null if the ticket is not rated
     */
    protected $rate;
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
     * Get createdBy
     *
     * @return Application\Sonata\UserBundle\Entity\User
     */
    public function getRate()
    {
        return $this->rate;
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
     * @param \Liuggio\HelpDeskBundle\Entity\Category $category
     */
    public function setCategory(\Liuggio\HelpDeskBundle\Model\Category $category)
    {
        $this->category = $category;
    }

    /**
     * @return \Liuggio\HelpDeskBundle\Entity\Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Add comments
     *
     * @param Liuggio\HelpDeskBundle\Entity\Comment $comments
     */
    public function addComment(\Liuggio\HelpDeskBundle\Model\Comment $comments)
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
     * Check for comments
     *
     * @return boolean
     */
    public function hasComments()
    {
        if (null != $this->getComments() && count($this->getComments()) > 0) {
            return true;
        }
    }

    /**
     * Set state
     *
     * @param Liuggio\HelpDeskBundle\Entity\TicketState $state
     */
    public function setState(\Liuggio\HelpDeskBundle\Model\TicketState $state)
    {
        $this->state = $state;
    }

    /**
     * Get state
     *
     * @return Liuggio\HelpDeskBundle\Entity\TicketState
     */
    public function getState()
    {
        return $this->state;
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
     * @return Application\Sonata\UserBundle\Entity\User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }
    /**
     * Set createdBy
     *
     * @param Application\Sonata\UserBundle\Entity\User $createdBy
     */
    public function setRate($rate)
    {
        switch ($rate) {
            case NULL:
            case self::RATE_STAR_ONE:
            case self::RATE_STAR_TWO:
            case self::RATE_STAR_THREE:
            case self::RATE_STAR_FOUR:
            case self::RATE_STAR_FIVE:
                $this->rate = $rate;
                break;
            default:
                throw new Exception('Invalid rating value.');

        }
    }

}