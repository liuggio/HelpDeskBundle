<?php

namespace Liuggio\HelpDeskTicketSystemBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;


use Liuggio\HelpDeskTicketSystemBundle\Entity\Ticket;
use Liuggio\HelpDeskTicketSystemBundle\Form\TicketType;
use Liuggio\HelpDeskTicketSystemBundle\Form\CloseTicketType;
use Liuggio\HelpDeskTicketSystemBundle\Form\RateType;
use Liuggio\HelpDeskTicketSystemBundle\Form\SearchType;
use Liuggio\HelpDeskTicketSystemBundle\Entity\TicketState;
use Liuggio\HelpDeskTicketSystemBundle\Form\CommentType;
use Liuggio\HelpDeskTicketSystemBundle\Entity\Comment;
use Liuggio\HelpDeskTicketSystemBundle\Exception;

/**
 * Ticket controller.
 *
 */
class TicketController extends Controller
{
    CONST STATE_OPEN = 'open';
    CONST STATE_CLOSE = 'closed';
    CONST STATE_ALL = 'all';

    /**
     * Lists all Ticket entities.
     *
     */
    public function indexAction($state = self::STATE_OPEN)
    {

        $form = $this->createForm(new SearchType());
        $request = $this->getRequest();
        $form->bindRequest($request);

        $request_pattern = null;

        if ($form->isValid()) {
            $formData = $form->getData();
            $request_pattern = $formData['request_pattern'];
        } else {
            $this->get('session')->setFlash('notice', 'Invalid Form!');
        }

        //$state could be : open | closed | all
        $em = $this->getDoctrine()->getEntityManager();
        $qb = $em->createQueryBuilder();

        $qb->select('t')
            ->from('LiuggioHelpDeskTicketSystemBundle:Ticket', 't')
            ->leftjoin('t.state','st');

        switch($state) {
            case self::STATE_OPEN:
                $qb->where('st.code != :state');
                $qb->setParameter('state', TicketState::STATE_CLOSED);
                break;
            case self::STATE_CLOSE:
                $qb->where('st.code = :state');
                $qb->setParameter('state', TicketState::STATE_CLOSED);
                break;
            case self::STATE_ALL:
                break;
            default:
                throw $this->createNotFoundException('Routing Problem (You should not be here)');
                break;
        }

        if($request_pattern){
            $qb->andWhere(
                $qb->expr()->orx( $qb->expr()->like('t.subject', ':pattern'), $qb->expr()->like('t.body', ':pattern') )

            )
                ->setParameter('pattern', "%".$request_pattern."%");
        }

        $tickets = $qb->getQuery()->getResult();

        // @TODO Pagination
        return $this->render('LiuggioHelpDeskTicketSystemBundle:Ticket:index.html.twig', array(
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
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('LiuggioHelpDeskTicketSystemBundle:Ticket')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Ticket entity.');
        }

        $comment = new Comment();
        $comment->setCreatedBy(null);
        $ticket_form = $this->createForm(new CloseTicketType($entity->getId()));
        $comment_form = $this->createForm(new CommentType($entity->getId()), $comment);
        if ($entity->getState()->getCode() == TicketState::STATE_CLOSED) {
            return $this->render('LiuggioHelpDeskTicketSystemBundle:Ticket:show_closed.html.twig', array(
                'entity' => $entity,
            ));
        }
        else {

            return $this->render('LiuggioHelpDeskTicketSystemBundle:Ticket:show_open.html.twig', array(
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
        $entity = new Ticket();
        $form = $this->createForm(new TicketType(), $entity);

        return $this->render('LiuggioHelpDeskTicketSystemBundle:Ticket:new.html.twig', array(
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
        $entity = new Ticket();
        $request = $this->getRequest();
        $form = $this->createForm(new TicketType(), $entity);
        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $locale = $this->getRequest()->getSession()->getLocale();
            if (isset($locale)) {
                $entity->setLanguage($locale);
            }
            $state_new = $em->getRepository('\Liuggio\HelpDeskTicketSystemBundle\Entity\TicketState')
                ->findOneByCode(\Liuggio\HelpDeskTicketSystemBundle\Entity\TicketState::STATE_NEW);

            if ($state_new) {
                $entity->setState($state_new);
            } else {
                throw new Exception();
            }
            // @TODO SEND EVENT
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('ticket_show', array('id' => $entity->getId())));

        }

        return $this->render('LiuggioHelpDeskTicketSystemBundle:Ticket:new.html.twig', array(
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
        $em = $this->getDoctrine()->getEntityManager();
        $entity = $em->getRepository('LiuggioHelpDeskTicketSystemBundle:Ticket')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Ticket entity.');
        }

        if ($entity->getState()->getCode() == TicketState::STATE_CLOSED) {

            return $this->redirect($this->generateUrl('ticket'));
        }

        $state_closed = $em->getRepository('\Liuggio\HelpDeskTicketSystemBundle\Entity\TicketState')
            ->findOneByCode(\Liuggio\HelpDeskTicketSystemBundle\Entity\TicketState::STATE_CLOSED);

        if ($state_closed) {
            $entity->setState($state_closed);
        }

        $em->persist($entity);
        $em->flush();

        $form = $this->createForm(new RateType($entity->getId()));

        return $this->render('LiuggioHelpDeskTicketSystemBundle:Ticket:rate.html.twig', array(
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
        $entity = new Ticket();
        $request = $this->getRequest();
        $form = $this->createForm(new RateType());
        $form->bindRequest($request);

        if ($form->isValid()) {

            $formData = $form->getData();
            $ticket_id = $formData['ticket_id'];
            $rate_val = $formData['rate'];

            $em = $this->getDoctrine()->getEntityManager();
            $entity = $em->getRepository('LiuggioHelpDeskTicketSystemBundle:Ticket')->find($ticket_id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Ticket entity.');
            }

            $session = $this->get('session');

            if ($entity->getRate() != NULL) {

                if ($session) {

                    $session->setFlash('thankYouMsg', 'You can not re-Rate this Ticket!');
                    $session->setFlash('ratedTickedId', $ticket_id);
                    $session->setFlash('rating:', $entity->getRate());
                }

                return $this->redirect($this->generateUrl('ticket'));
            }

            $entity->setRate($rate_val);
            $em->persist($entity);
            $em->flush();
            if ($session) {

                $session->setFlash('thankYouMsg', 'Thank you for rating our services!');
                $session->setFlash('ratedTickedId', $ticket_id);
                $session->setFlash('rating', $rate_val);

            }
        }

        return $this->redirect($this->generateUrl('ticket'));

    }
}