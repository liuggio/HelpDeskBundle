<?php

namespace Liuggio\HelpDeskTicketSystemBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Liuggio\HelpDeskTicketSystemBundle\Entity\Comment;
use Liuggio\HelpDeskTicketSystemBundle\Form\CommentType;

/**
 * Comment controller.
 *
 */
class CommentAdminController extends Controller
{
    /**
     * Creates a new Comment entity.
     *
     */
    public function createAction()
    {
        //Retrive the User from the Session
        $user = $this->get('security.context')->getToken()->getUser();

        $entity = new Comment();
        $request = $this->getRequest();
        $form = $this->createForm(new CommentType(), $entity);
        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $comment = $form->getData();
            $form = $this->getRequest()->get('liuggio_helpdeskticketsystembundle_commenttype');
            $ticket_id = $form['ticket'];
            $ticket = $em->getRepository('LiuggioHelpDeskTicketSystemBundle:Ticket')->find($ticket_id);
            if (!$ticket) {
                throw $this->createNotFoundException('Unable to find Ticket entity.');
            }

            $state_replied = $em->getRepository('\Liuggio\HelpDeskTicketSystemBundle\Entity\TicketState')
                ->findOneByCode(\Liuggio\HelpDeskTicketSystemBundle\Entity\TicketState::STATE_REPLIED);

            if ($state_replied) {
                $ticket->setState($state_replied);
            }
            //Set the createdBy user
            $entity->setCreatedBy($user);
            $em->persist($ticket);
            $comment->setTicket($ticket);
            $em->persist($comment);
            $em->flush();

            return $this->redirect($this->generateUrl('ticket_show_admin', array('id' => $ticket_id)));

        }

        return $this->render('LiuggioHelpDeskTicketSystemBundle:CommentAdmin:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView()
        ));
    }

}
