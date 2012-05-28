<?php

namespace Liuggio\HelpDeskTicketSystemBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Liuggio\HelpDeskTicketSystemBundle\Entity\Comment;
use Liuggio\HelpDeskTicketSystemBundle\Form\CommentType;

/**
 * Comment controller.
 *
 */
class CommentController extends Controller
{



    /**
     * Displays a form to create a new Comment entity.
     *
     */
    public function newAction()
    {
        $entity = new Comment();
        
        $form   = $this->createForm(new CommentType(), $entity);

        return $this->render('LiuggioHelpDeskTicketSystemBundle:Comment:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
     * Creates a new Comment entity.
     *
     */
    public function createAction()
    {
        $entity  = new Comment();
        $request = $this->getRequest();
        $form    = $this->createForm(new CommentType(), $entity);
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
            
            $state_pending = $em->getRepository('\Liuggio\HelpDeskTicketSystemBundle\Entity\TicketState')
            ->findOneByCode(\Liuggio\HelpDeskTicketSystemBundle\Entity\TicketState::STATE_PENDING);

            if ($state_pending) {
                $ticket->setState($state_pending);
            }
        
            $em->persist($ticket);
            $comment->setTicket($ticket);  
            $em->persist($comment);
            $em->flush();
            
            return $this->redirect($this->generateUrl('ticket_show', array('id' => $ticket_id)));
            
        }

        return $this->render('LiuggioHelpDeskTicketSystemBundle:Comment:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

}
