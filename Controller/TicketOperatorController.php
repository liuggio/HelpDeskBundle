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
use Doctrine\ORM\Tools\Pagination\Paginator;

class TicketOperatorController extends Controller
{
    const ELEMENTS_PER_PAGE = 20;

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

        $request_pattern = null;
        //Create the Search Form
        $form = $this->createForm(new SearchType());
        $request = $this->getRequest();

        if($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $formData = $form->getData();
                $request_pattern = $formData['request_pattern'];
            } else {
                $this->get('session')->getFlashBag()->add('invalid_search_form_notice', 'invalid_search_form_notice');
            }
        }

        $user = $this->get('security.context')->getToken()->getUser();
        $ticketRepository = $this->get('liuggio_help_desk.ticket.manager')
            ->getTicketRepository();

        $tickets = $ticketRepository->findTicketsByStatesAndOperator($user, $states, $request_pattern, true);
        $paginator = new Paginator($tickets, $fetchJoinCollection = true);

        $page = $this->getRequest()->get('page');
        $ticketCounter = count($paginator);
        $pages = intval($ticketCounter/self::ELEMENTS_PER_PAGE);
        if($pages < 0){
            $pages = 0;
        }
        if(is_null($page)){
            $firstResult = $pages;
        }else{
            $firstResult = intval(self::ELEMENTS_PER_PAGE * $page);
        }

        $paginator = $paginator->getQuery()->setFirstResult($firstResult)->setMaxResults(self::ELEMENTS_PER_PAGE);

        $categoryRepo = $this->get('liuggio_help_desk_category.manager')->getEntityRepository();

        $categories = $categoryRepo->findByOperator($user);

        // @TODO Pagination
        return $this->render('LiuggioHelpDeskBundle:TicketOperator:index.html.twig', array(
            'entities' => $paginator->getResult(),
            'form' => $form->createView(),
            'state' => $state,
            'categories' => $categories,
            'pages' => range(0,$pages),
        ));

    }


    /**
     * Finds and displays a Ticket entity.
     *
     */
    public function showAction($id)
    {
        $operator = $this->get('security.context')->getToken()->getUser();
        $em = $this->get('liuggio_help_desk.doctrine.manager');
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
        $comment = $ticketRepository = $this->get('liuggio_help_desk_comment.manager')
            ->createEntity();
        $comment->setCreatedBy($operator);
        $comment_form = $this->createForm(new CommentType($entity->getId()), $comment);

        //Closed is logic maybe into Manager
        if ($entity->getState()->getCode() == TicketState::STATE_CLOSED) {
            return $this->render('LiuggioHelpDeskBundle:TicketOperator:show_closed.html.twig', array(
                'entity' => $entity,
            ));
        } else {
            return $this->render('LiuggioHelpDeskBundle:TicketOperator:show_open.html.twig', array(
                'entity' => $entity,
                'comment_create_admin' => $comment_form->createView()
            ));
        }
    }

}
