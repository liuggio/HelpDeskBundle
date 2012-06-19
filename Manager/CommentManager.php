<?php

namespace Liuggio\HelpDeskBundle\Manager;

use Doctrine\Common\Persistence\ObjectManager;

use Liuggio\HelpDeskBundle\Model\AbstractManager;
use Liuggio\HelpDeskBundle\Exception;

class CommentManager extends AbstractManager
{
    public function addCommentAndUpdateTicketState($form, $user, $state = \Liuggio\HelpDeskBundle\Entity\TicketState::STATE_REPLIED)
    {
        $comment = $form->getData();
        $form = $this->getRequest()->get('liuggio_HelpDeskBundle_commenttype');
        $ticket_id = $form['ticket'];
        $ticket = $this->getObjectManager()->getRepository('LiuggioHelpDeskBundle:Ticket')->find($ticket_id);
        if (!$ticket) {
            throw $this->createNotFoundException('Unable to find Ticket entity.');
        }
        $ticketState = $this->getObjectManager()->getRepository('\Liuggio\HelpDeskBundle\Entity\TicketState')
            ->findOneByCode($state);

        if (!$ticketState) {
            throw new Exception(sprintf('Ticket State Not Found looking for "%s"', $ticketState));
        }
        //Set the createdBy user
        $ticket->setState($ticketState);
        $this->getObjectManager()->persist($ticket);

        $comment->setTicket($ticket);
        $comment->setCreatedBy($user);
        $this->getObjectManager()->persist($comment);

    }


}