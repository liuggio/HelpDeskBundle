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
class CommentOperatorController extends Controller
{
    /**
     * Creates a new Comment entity.
     *
     */
    public function createAction($state = \Liuggio\HelpDeskBundle\Model\TicketState::STATE_REPLIED)
    {
        //Retrive the User from the Session
        $user = $this->get('security.context')->getToken()->getUser();

        $entity = new Comment();
        $request = $this->getRequest();


        $form = $this->createForm(new CommentType(), $entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $comment = $form->getData();
            $form = $this->getRequest()->get('liuggio_HelpDeskBundle_commenttype');
            $ticket_id = $form['ticket'];
            $ticket = $this->getDoctrine()->getManager()->getRepository('LiuggioHelpDeskBundle:Ticket')->find($ticket_id);
            if (!$ticket) {
                throw $this->createNotFoundException('Unable to find Ticket entity.');
            }
            $ticketState = $this->getDoctrine()->getManager()->getRepository('\Liuggio\HelpDeskBundle\Entity\TicketState')
                ->findOneByCode($state);

            if (!$ticketState) {
                throw new Exception(sprintf('Ticket State Not Found looking for "%s"', $ticketState));
            }
            //Set the createdBy user
            $ticket->setState($ticketState);
            $this->getDoctrine()->getManager()->persist($ticket);

            $comment->setTicket($ticket);
            $comment->setCreatedBy($user);
            $this->getDoctrine()->getManager()->persist($comment);

            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('ticket_show_admin', array('id' => $ticket_id)));

        }

        return $this->render('LiuggioHelpDeskBundle:CommentAdmin:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView()
        ));
    }

}
