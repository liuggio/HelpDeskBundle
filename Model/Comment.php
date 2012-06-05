<?php

namespace Liuggio\HelpDeskTicketSystemBundle\Model;



abstract class Comment implements CommentInterface
{
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
     * @param \Liuggio\HelpDeskTicketSystemBundle\Model\text $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * @return \Liuggio\HelpDeskTicketSystemBundle\Model\text
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