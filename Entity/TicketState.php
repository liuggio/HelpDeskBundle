<?php

namespace Liuggio\HelpDeskBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Liuggio\HelpDeskBundle\Entity\TicketState
 */
class TicketState
{
    CONST STATE_NEW = 'new';
    CONST STATE_PENDING = 'pending';
    CONST STATE_REPLIED = 'replied';
    CONST STATE_CLOSED = 'closed';

    /**
     * @var string $code
     */
    private $code;

    /**
     * @var string $description
     */
    private $description;

    /**
     * @var integer $weight
     */
    private $weight;

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getCode();
    }


    /**
     * Set code
     *
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set description
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set weight
     *
     * @param integer $weight
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
    }

    /**
     * Get weight
     *
     * @return integer
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @var integer $id
     */
    private $id;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
}