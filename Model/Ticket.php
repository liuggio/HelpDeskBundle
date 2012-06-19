<?php

namespace Liuggio\HelpDeskBundle\Model;


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
    private $id;

    /**
     * @var text $body
     */
    private $body;

    /**
     * @return string
     */
    public function __toString()
    {
        $string = sprintf('#%d %s', $this->getId(), $this->getBody());
        return $string;
    }

    /**
     * @param \Liuggio\HelpDeskBundle\Model\text $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * @return \Liuggio\HelpDeskBundle\Model\text
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}