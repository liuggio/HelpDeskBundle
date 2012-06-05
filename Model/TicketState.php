<?php

namespace Liuggio\HelpDeskTicketSystemBundle\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Liuggio\HelpDeskTicketSystemBundle\Entity\TicketState
 */
abstract class TicketState
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

}