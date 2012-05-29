<?php
namespace Liuggio\HelpDeskTicketSystemBundle\Controller\Admin;

use Sonata\AdminBundle\Controller\CRUDController as Controller;

class TicketAdminController extends Controller
{
    /**
     * return the Response object associated to the list action
     *
     * @return Response
     */
    public function listAction()
    {
        if (false === $this->admin->isGranted('LIST')) {
            throw new AccessDeniedException();
        }

        $datagrid = $this->admin->getDatagrid();
        $datagrid->setValue('state', null, $this->admin->getPersistentParameter('state'));

        $formView = $datagrid->getForm()->createView();
        // set the theme for the current Admin Form
        $this->get('twig')->getExtension('form')->setTheme($formView, $this->admin->getFilterTheme());

        return $this->render($this->admin->getListTemplate(), array(
            'action'   => 'list',
            'form'     => $formView,
            'datagrid' => $datagrid
        ));
    }
    
    
    /**
     * Lists all Ticket entities.
     *
     */
    public function indexAction($state = self::STATE_OPEN)
    {
        // show all tickets if user has ROLE_CUSTOMERCARE
        if ($this->get('security.context')->isGranted('ROLE_CUSTOMERCARE')) {
            // Load operator content here
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
        
            //$state GET parameter could be : open | closed | all
            $em = $this->getDoctrine()->getEntityManager();
            $qb = $em->createQueryBuilder();
        
            //Retrive the CUSTOMERCARE from the Session
            $operator = $this->get('security.context')->getToken()->getUser();
            
            //Retrive categories customercare belongs to
            
            /* SELECT ct FROM Category ct
             * LEFTJOIN  Users ON ct.operators = User.Id
             *
             */
            $qb->select('ct')
                ->from('LiuggioHelpDeskTicketSystemBundle:Category', 'ct')
                ->leftjoin('ct.operators','opr');
            $qb->where('ct.operators = :customercare');
            $qb->setParameter('customercare', $operator);
            $categories = $qb->getQuery()->getResult();
            
            
           /* SELECT * FROM Ticket t
            * LEFT-JOIN     Ticket_State st ON t.state = st.code
            * WHERE      (  st.code = '$query_state'
            *                AND ( t.subject  LIKE  %request_pattern%
            *                      OR t.body LIKE  %request_pattern% 
            *                     )
            *             )
            */
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
            
        }
        
        

}

