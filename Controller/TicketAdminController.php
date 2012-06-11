<?php

namespace Liuggio\HelpDeskBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Tvision\Bundle\UserBundle\Entity\User;
use Liuggio\HelpDeskBundle\Entity\Ticket;
use Liuggio\HelpDeskBundle\Form\TicketType;
use Liuggio\HelpDeskBundle\Form\CloseTicketType;
use Liuggio\HelpDeskBundle\Form\RateType;
use Liuggio\HelpDeskBundle\Form\SearchType;
use Liuggio\HelpDeskBundle\Entity\TicketState;
use Liuggio\HelpDeskBundle\Form\CommentType;
use Liuggio\HelpDeskBundle\Entity\Comment;
use Liuggio\HelpDeskBundle\Exception;


class TicketAdminController extends Controller
{
    /**
     * Lists all Ticket entities.
     *
     */
    public function indexAction($state = Ticket::STATE_OPEN)
    {
        if ($state == Ticket::STATE_OPEN) {
            $states = Ticket::$STATE[Ticket::STATE_OPERATOR_OPEN];
        } elseif ($state == Ticket::STATE_CLOSE) {
            $states = Ticket::$STATE[Ticket::STATE_OPERATOR_CLOSE];
        } else {
            $states = Ticket::$STATE[Ticket::STATE_OPERATOR_ALL];
        }

        //Create the Search Form
        $form = $this->createForm(new SearchType());
        $request = $this->getRequest();
        $form->bindRequest($request);

        $request_pattern = null;

        if ($form->isValid()) {
            $formData = $form->getData();
            $request_pattern = $formData['request_pattern'];
        } else {
            $this->get('session')->setFlash('invalid_search_form_notice', 'invalid_search_form_notice');
        }

        $user = $this->get('security.context')->getToken()->getUser();
        $ticketRepository = $this->get('liuggio_help_desk.ticket.manager')
            ->getTicketRepository();

        $tickets = $ticketRepository->findTicketsByStatesAndOperator($user, $states, $request_pattern);


        // @TODO Pagination
        return $this->render('LiuggioHelpDeskBundle:TicketAdmin:index.html.twig', array(
            'entities' => $tickets,
            'form' => $form->createView(),
            'state' => $state
        ));

    }


    /**
     * Finds and displays a Ticket entity.
     *
     */
    public function showAction($id)
    {
        $operator = $this->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getEntityManager();
        $entity = $ticketRepository = $this->get('liuggio_help_desk.ticket.manager')
            ->getTicketRepository()
            ->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Ticket entity.');
        }

        // check CustomerCare permissions
        $isGranted = $this->get('liuggio_help_desk.ticket.manager')
            ->isOperatorGrantedForThisTicket($entity, $operator);
        if (!$isGranted) {
            throw new AccessDeniedException("Category Permission not granted!");
        }
        $comment = $ticketRepository = $this->get('liuggio_help_desk.ticket.manager')
            ->createComment();
        $comment->setCreatedBy($operator);
        $comment_form = $this->createForm(new CommentType($entity->getId()), $comment);
        //Closed is logic maybe into Manager
        if ($entity->getState()->getCode() == TicketState::STATE_CLOSED) {
            return $this->render('LiuggioHelpDeskBundle:TicketAdmin:show_closed.html.twig', array(
                'entity' => $entity,
            ));
        } else {
            return $this->render('LiuggioHelpDeskBundle:TicketAdmin:show_open.html.twig', array(
                'entity' => $entity,
                'comment_create_admin' => $comment_form->createView()
            ));
        }
    }

}
