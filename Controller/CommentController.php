<?php

namespace Liuggio\HelpDeskBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Liuggio\HelpDeskBundle\Entity\Comment;
use Liuggio\HelpDeskBundle\Form\CommentType;
use Liuggio\HelpDeskBundle\Exception;

/**
 * Comment controller.
 *
 */
class CommentController extends Controller
{
    /**
     * Creates a new Comment entity.
     *
     */
    public function createAction($redirectTo = 'ticket_show', $ticketState = \Liuggio\HelpDeskBundle\Entity\TicketState::STATE_PENDING)
    {
        //Retrive the User from the Session
        $user = $this->get('security.context')->getToken()->getUser();

        $entity = new Comment();
        $request = $this->getRequest();
        $form = $this->createForm(new CommentType(), $entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $comment = $form->getData();
            $form = $this->getRequest()->get('liuggio_HelpDeskBundle_commenttype');
            $ticket_id = $form['ticket'];
            $ticket = $em->getRepository('LiuggioHelpDeskBundle:Ticket')->find($ticket_id);
            if (!$ticket) {
                throw $this->createNotFoundException('Unable to find Ticket entity.');
            }
            $state = $em->getRepository('Liuggio\HelpDeskBundle\Entity\TicketState')
                ->findOneByCode($ticketState);

            if (!$state) {
                throw new Exception('Ticket State Not Found');
            }
            $ticket->setState($state);
            $em->persist($ticket);
            //Set the createdBy user
            $comment->setCreatedBy($user);
            $comment->setTicket($ticket);
            $em->persist($comment);

            $em->flush();
            return $this->redirect($this->generateUrl($redirectTo, array('id' => $ticket_id)));
        }

        $this->getRequest()->getSession()->getFlashBag()->add('alert',"Form error, please fill all fields.");
        return $this->redirect($this->generateUrl('ticket'));
    }

}
