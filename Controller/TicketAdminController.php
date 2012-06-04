<?php

namespace Liuggio\HelpDeskTicketSystemBundle\Controller;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use Tvision\Bundle\UserBundle\Entity\User;
use Liuggio\HelpDeskTicketSystemBundle\Entity\Ticket;
use Liuggio\HelpDeskTicketSystemBundle\Form\TicketType;
use Liuggio\HelpDeskTicketSystemBundle\Form\CloseTicketType;
use Liuggio\HelpDeskTicketSystemBundle\Form\RateType;
use Liuggio\HelpDeskTicketSystemBundle\Form\SearchType;
use Liuggio\HelpDeskTicketSystemBundle\Entity\TicketState;
use Liuggio\HelpDeskTicketSystemBundle\Form\CommentType;
use Liuggio\HelpDeskTicketSystemBundle\Entity\Comment;
use Liuggio\HelpDeskTicketSystemBundle\Exception;


class TicketAdminController extends Controller
{
    CONST STATE_OPEN = 'open';
    CONST STATE_CLOSE = 'closed';

    /**
     * Lists all Ticket entities.
     *
     */
    public function indexAction($state = self::STATE_OPEN)
    {
        //Create the Search Form
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

        $em = $this->getDoctrine()->getEntityManager();
        $qb = $em->createQueryBuilder();

        //Retrieve the CUSTOMERCARE from the Session
        $operator = $this->get('security.context')->getToken()->getUser();

        $qb->select('t')
            ->from('LiuggioHelpDeskTicketSystemBundle:Ticket', 't')
            ->leftjoin('t.state','st')
            ->leftjoin('t.category', 'ct')
            ->leftjoin('ct.operators','opr')
            ->where('opr = :user')
            ->setParameter('user', $operator);

        //$state GET parameter could be : open | closed
        if($state == self::STATE_OPEN) {
            // Retrieve NEW and PENDING Tickets
            $qb->andWhere( $qb->expr()->orx(
                    $qb->expr()->eq('st.code', ':new'),
                    $qb->expr()->eq('st.code', ':pending')
                )
            );
            $qb->setParameters(array('new' => TicketState::STATE_NEW, 'pending' => TicketState::STATE_PENDING));
        } else{//STATE_CLOSED
            // Retrieve REPLIED and CLOSED Tickets
            $qb->andWhere( $qb->expr()->orx(
                    $qb->expr()->eq('st.code', ':replied'),
                    $qb->expr()->eq('st.code', ':closed')
                )
            );
            $qb->setParameters(array('replied' => TicketState::STATE_REPLIED, 'closed' => TicketState::STATE_CLOSED));
        }

        $tickets = $qb->getQuery()->getResult();

        // @TODO Pagination
        return $this->render('LiuggioHelpDeskTicketSystemBundle:TicketAdmin:index.html.twig', array(
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
        $entity = $em->getRepository('LiuggioHelpDeskTicketSystemBundle:Ticket')->find($id);
        $qb = $em->createQueryBuilder();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Ticket entity.');
        }

        // check CustomerCare permissions
        $aclManager = $this->get('liuggio_help_desk_ticket_system.acl.manager');
        $aclManager->checkOpPermissions($entity, $operator, $qb);

        $comment = new Comment();
        $comment->setCreatedBy($operator);
        $comment_form = $this->createForm(new CommentType($entity->getId()), $comment);
        if ($entity->getState()->getCode() == TicketState::STATE_CLOSED) {
            return $this->render('LiuggioHelpDeskTicketSystemBundle:TicketAdmin:show_closed.html.twig', array(
                'entity' => $entity,
            ));
        }else {

            return $this->render('LiuggioHelpDeskTicketSystemBundle:TicketAdmin:show_open.html.twig', array(
                'entity' => $entity,
                'comment_create_admin' => $comment_form->createView()
            ));
        }
    }

}
