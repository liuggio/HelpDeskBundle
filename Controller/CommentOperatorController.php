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
    public function createAction($state)
    {
        //Retrive the User from the Session
        $user = $this->get('security.context')->getToken()->getUser();

        $entity = new Comment();
        $request = $this->getRequest();


        $form = $this->createForm(new CommentType(), $entity);
        $form->bindRequest($request);

        if ($form->isValid()) {
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


            $em->flush();

            return $this->redirect($this->generateUrl('ticket_show_admin', array('id' => $ticket_id)));

        }

        return $this->render('LiuggioHelpDeskBundle:CommentAdmin:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView()
        ));
    }

}
