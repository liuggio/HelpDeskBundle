<?php

namespace Liuggio\HelpDeskBundle\Document;

use Liuggio\HelpDeskBundle\Exception;
use Liuggio\HelpDeskBundle\Model\Ticket as BaseTicket;

/**
 * Liuggio\HelpDeskBundle\Entity\Ticket
 */
class Ticket extends BaseTicket
{

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

}
