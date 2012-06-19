<?php

namespace Liuggio\HelpDeskBundle\Manager;


use Doctrine\Common\Persistence\ObjectManager;
use Liuggio\HelpDeskBundle\Model\TicketManager as BaseTicketManager;

class TicketManager extends BaseTicketManager
{

    /**
     * @param $ticket
     * @param $user
     * @return bool
     */
    public function isOperatorGrantedForThisTicket($ticket, $operator)
    {
        $qb = $this->getObjectManager()->createQueryBuilder();

        $qb->select('t')
            ->from('LiuggioHelpDeskBundle:Ticket', 't')
            ->leftjoin('t.category', 'ct')
            ->leftjoin('ct.operators', 'opr')
            ->where('t = :ticket')
            ->andWhere('opr = :user')
            ->setParameter('ticket', $ticket)
            ->setParameter('user', $operator);

        $result = $qb->getQuery()->getResult();

        if (empty($result)) {
            return false;
        }
        return true;
    }


}