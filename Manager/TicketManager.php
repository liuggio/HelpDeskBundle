<?php

namespace Liuggio\HelpDeskBundle\Manager;


use Doctrine\Common\Persistence\ObjectManager;
use Liuggio\HelpDeskBundle\Model\TicketManager as BaseTicketManager;
use Liuggio\HelpDeskBundle\Model\Category;
use Liuggio\HelpDeskBundle\Entity\TicketState;

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


    public function createTicketWithUserAndCategory(Category $category, $language, $user, $subject, $body)
    {
        $state = $this->objectManager->getRepository('LiuggioHelpDeskBundle:TicketState')->findOneByCode(TicketState::STATE_NEW);

        $ticket = $this->createTicket();
        $ticket->setBody($body);
        $ticket->setSubject($subject);
        $ticket->setCreatedBy($user);
        $ticket->setState($state);
        $ticket->setCategory($category);
        $ticket->setLanguage($language);

        $this->getObjectManager()->persist($ticket);
        $this->getObjectManager()->flush();

        $aclManager = $this->getAclManager();
        $aclManager->insertAce($ticket, $user);

        return $ticket;
    }

    public function createCommentForTicket($ticket, $user, $commentBody)
    {
        $commentClass = $this->ticketCommentClass;
        $comment = new $commentClass;

        $comment->setBody($commentBody);
        $comment->setCreatedBy($user);
        $comment->setTicket($ticket);

        $this->objectManager->persist($comment);
        $this->objectManager->flush();

        return $comment;
    }

    public function closeTicket($ticket)
    {
        $state = $this->objectManager->getRepository('LiuggioHelpDeskBundle:TicketState')->findOneByCode(TicketState::STATE_CLOSED);
        $ticket->setState($state);

        $this->objectManager->persist($ticket);
        $this->objectManager->flush();
    }

}