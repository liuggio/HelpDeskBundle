<?php

namespace Liuggio\HelpDeskBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Liuggio\HelpDeskBundle\Entity\Ticket;
use Liuggio\HelpDeskBundle\Form\TicketType;
use Liuggio\HelpDeskBundle\Form\CloseTicketType;
use Liuggio\HelpDeskBundle\Form\RateType;
use Liuggio\HelpDeskBundle\Form\SearchType;
use Liuggio\HelpDeskBundle\Entity\TicketState;
use Liuggio\HelpDeskBundle\Form\CommentType;
use Doctrine\ORM\Tools\Pagination\Paginator;

use Liuggio\HelpDeskBundle\Exception;

/**
 * Ticket controller.
 *
 */
class TicketController extends Controller
{
    const ELEMENTS_PER_PAGE = 20;
    /**
     * Lists all Ticket entities.
     *
     */
    public function indexAction($state = Ticket::STATE_OPEN)
    {
        if (!$this->container->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw new AccessDeniedException();
        }
        //Retrive the User from the Session
        $user = $this->get('security.context')->getToken()->getUser();

        $states = Ticket::$STATE[$state];



        //Create the Search Form
        $form = $this->createForm(new SearchType());
        $request = $this->getRequest();
        $form->submit($request);

        $request_pattern = null;

        if ($form->isValid()) {
            $formData = $form->getData();
            $request_pattern = $formData['request_pattern'];
        } else {
            $this->get('session')->getFlashBag()->add('invalid_search_form_notice', 'invalid_search_form_notice');
        }
        $ticketRepository = $this->get('liuggio_help_desk.ticket.manager')->getTicketRepository();
        $tickets = $ticketRepository->findTicketsByStatesAndCustomer($user, $states, $request_pattern, true);

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

        return $this->render('LiuggioHelpDeskBundle:Ticket:index.html.twig', array(
            'entities' => $paginator->getResult(),
            'form' => $form->createView(),
            'state' => $state,
            'pages' => range(0, $pages),
        ));
    }

    /**
     * Finds and displays a Ticket entity.
     *
     */
    public function showAction($id)
    {
        //Retrive the User from the Session
        if (!$this->container->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw new AccessDeniedException();
        }

        $user = $this->get('security.context')->getToken()->getUser();

        $entity = $this->get('liuggio_help_desk.ticket.manager')
            ->getTicketRepository()
            ->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Ticket entity.');
        }

        $aclManager = $this->get('liuggio_help_desk.acl.manager');
        $aclManager->checkPermissions($entity);

        $comment = $this->get('liuggio_help_desk_comment.manager')
            ->createEntity();

        $ticket_form = $this->createForm(new CloseTicketType($entity->getId()));
        $comment_form = $this->createForm(new CommentType($entity->getId()), $comment);
        if ($entity->getState()->getCode() == TicketState::STATE_CLOSED) {
            return $this->render('LiuggioHelpDeskBundle:Ticket:show_closed.html.twig', array(
                'entity' => $entity,
            ));
        } else {
            return $this->render('LiuggioHelpDeskBundle:Ticket:show_open.html.twig', array(
                'entity' => $entity,
                'comment_create' => $comment_form->createView(),
                'ticket_close' => $ticket_form->createView()
            ));
        }
    }

    /**
     * Displays a form to create a new Ticket entity.
     *
     */
    public function newAction()
    {
        $entity = $this->get('liuggio_help_desk.ticket.manager')
            ->createTicket();

        $form = $this->createForm(new TicketType(), $entity);

        return $this->render('LiuggioHelpDeskBundle:Ticket:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView()
        ));
    }

    /**
     * Creates a new Ticket entity.
     *
     */
    public function createAction()
    {
        //Retrive the User from the Session
        if (!$this->container->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw new AccessDeniedException();
        }

        $user = $this->get('security.context')->getToken()->getUser();
        $entity = $this->get('liuggio_help_desk.ticket.manager')
            ->createTicket();

        $request = $this->getRequest();
        $form = $this->createForm(new TicketType(), $entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->get('liuggio_help_desk.doctrine.manager');
            $locale = $this->getRequest()->getLocale();
            if (isset($locale)) {
                $entity->setLanguage($locale);
            }
            $state_new = $em->getRepository('\Liuggio\HelpDeskBundle\Entity\TicketState')
                ->findOneByCode(\Liuggio\HelpDeskBundle\Entity\TicketState::STATE_NEW);

            if ($state_new) {
                $entity->setState($state_new);
            } else {
                throw new Exception();
            }
            //Set the createdBy user
            $entity->setCreatedBy($user);
            $em->persist($entity);
            $em->flush();
            //Set the ACE
            $aclManager = $this->get('liuggio_help_desk.acl.manager');
            $aclManager->insertAce($entity, $user);


            return $this->redirect($this->generateUrl('ticket_show', array('id' => $entity->getId())));

        }

        return $this->render('LiuggioHelpDeskBundle:Ticket:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView()
        ));
    }

    /**
     * Close the Ticket
     *
     */
    public function closeAction($id)
    {
        $em = $this->get('liuggio_help_desk.doctrine.manager');
        $entity = $this->get('liuggio_help_desk.ticket.manager')
            ->getTicketRepository()
            ->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Ticket entity.');
        }

        $aclManager = $this->get('liuggio_help_desk.acl.manager');
        $aclManager->checkPermissions($entity);

        if ($entity->getState()->getCode() == TicketState::STATE_CLOSED) {

            return $this->redirect($this->generateUrl('ticket'));
        }

        $state_closed = $em->getRepository('\Liuggio\HelpDeskBundle\Entity\TicketState')
            ->findOneByCode(\Liuggio\HelpDeskBundle\Entity\TicketState::STATE_CLOSED);

        if ($state_closed) {
            $entity->setState($state_closed);
        }

        $em->persist($entity);
        $em->flush();

        $form = $this->createForm(new RateType($entity->getId()));

        return $this->render('LiuggioHelpDeskBundle:Ticket:rate.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView()
        ));
    }

    /**
     * Rate the Ticket
     *
     */
    public function rateAction()
    {

        $request = $this->getRequest();
        $form = $this->createForm(new RateType());
        $form->handleRequest($request);

        if ($form->isValid()) {

            $formData = $form->getData();
            $ticket_id = $formData['ticket_id'];
            $rate_val = $formData['rate'];

            $em = $this->get('liuggio_help_desk.doctrine.manager');

            $entity = $this->get('liuggio_help_desk.ticket.manager')
                ->getTicketRepository()
                ->find($ticket_id);

            $aclManager = $this->get('liuggio_help_desk.acl.manager');
            $aclManager->checkPermissions($entity);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Ticket entity.');
            }

            $session = $this->get('session');

            if ($entity->getRate() != NULL) {

                if ($session) {

                    $session->getFlashBag()->add('reRate_error_msg', 'You can not re-Rate this Ticket!');
                    $session->getFlashBag()->add('ratedTickedId', $ticket_id);
                    $session->getFlashBag()->add('rating:', $entity->getRate());
                }

                return $this->redirect($this->generateUrl('ticket'));
            }

            $entity->setRate($rate_val);
            $em->persist($entity);
            $em->flush();
            if ($session) {

                $session->getFlashBag()->add('thank_rate_msg', 'Thank you for rating our services!');
                $session->getFlashBag()->add('ratedTickedId', $ticket_id);
                $session->getFlashBag()->add('rating', $rate_val);

            }
        }

        return $this->redirect($this->generateUrl('ticket'));

    }
}
