<?php
namespace Liuggio\HelpDeskBundle\Controller\Admin;

use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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
            'action' => 'list',
            'form' => $formView,
            'datagrid' => $datagrid
        ));
    }


}

