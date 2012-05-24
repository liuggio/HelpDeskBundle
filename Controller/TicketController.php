<?php

namespace Liuggio\HelpDeskTicketSystemBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;


use Liuggio\HelpDeskTicketSystemBundle\Entity\Ticket;
use Liuggio\HelpDeskTicketSystemBundle\Form\TicketType;
use Liuggio\HelpDeskTicketSystemBundle\Form\RateType;
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
    CONST USER_TAB_STATE_OPEN = 'open';
    CONST USER_TAB_STATE_CLOSE = 'close';
    CONST USER_TAB_STATE_ALL = 'all';

    /**
     * Lists all Ticket entities.
     *
     */
    public function indexAction($status = self::USER_TAB_STATE_OPEN)
    {
        //$status could be : Open, Closed, All
        $em = $this->getDoctrine()->getEntityManager();
        $entities=null;
        if ($status == self::USER_TAB_STATE_ALL) {
                
        } else {
            $baseQuery = 'SELECT t FROM LiuggioHelpDeskTicketSystemBundle:Ticket t JOIN t.state st where st.code %s :state_closed';
            $query =  $query = sprintf($baseQuery, '=');
            if ($status == self::USER_TAB_STATE_OPEN) {
                $query = sprintf($baseQuery, '!=');
            } else if ($status == self::USER_TAB_STATE_CLOSE) {
                $query = sprintf($baseQuery, '=');
            }
            $query = $em->createQuery($query);
            $query->setParameter('state_closed', TicketState::STATE_CLOSED);

            //var_dump($query->getSql());
            $entities = $query->getResult();
        }
        // @TODO Pagination
        return $this->render('LiuggioHelpDeskTicketSystemBundle:Ticket:index.html.twig', array(
            'entities' => $entities
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
        $comment_form = $this->createForm(new CommentType($entity->getId()), $comment);
        if ($entity->getState()->getCode() == TicketState::STATE_CLOSED) {
            return $this->render('LiuggioHelpDeskTicketSystemBundle:Ticket:show_closed.html.twig', array(
                'entity' => $entity,
            ));
        }
        else {

            return $this->render('LiuggioHelpDeskTicketSystemBundle:Ticket:show_open.html.twig', array(
                'entity' => $entity,
                'comment_form' => $comment_form->createView()
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
     * Displays a form to edit an existing Ticket entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('LiuggioHelpDeskTicketSystemBundle:Ticket')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Ticket entity.');
        }

        $editForm = $this->createForm(new TicketType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('LiuggioHelpDeskTicketSystemBundle:Ticket:edit.html.twig', array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing Ticket entity.
     *
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('LiuggioHelpDeskTicketSystemBundle:Ticket')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Ticket entity.');
        }

        $editForm = $this->createForm(new TicketType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('ticket_edit', array('id' => $id)));
        }

        return $this->render('LiuggioHelpDeskTicketSystemBundle:Ticket:edit.html.twig', array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Ticket entity.
     *
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $entity = $em->getRepository('LiuggioHelpDeskTicketSystemBundle:Ticket')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Ticket entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('ticket'));
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

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm();
    }
}
