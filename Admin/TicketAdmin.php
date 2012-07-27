<?php
namespace Liuggio\HelpDeskBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Liuggio\HelpDeskBundle\Model\Ticket;

class TicketAdmin extends Admin
{

    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('createdBy')
            ->add('category')
            ->add('subject')
            ->add('body')
            ->add('language')
            ->add('state')
            ->add('comments');
    }

    public function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('General')
            ->add('category', 'sonata_type_model', array('required' => true))
            ->add('subject')
            ->add('body')
            ->add('language')
            ->add('state', 'sonata_type_model', array('required' => true))
            ->add('comments', 'sonata_type_model', array('required' => false))
            ->add('createdBy', 'sonata_type_model', array('required' => true))
            ->end();
    }

    public function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('rate')
            ->add('category')
            ->add('subject')
            ->add('language')
            ->add('state.code')
            ->add('comments')
            ->add('createdBy');
    }

    public function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('category')
            ->add('subject')
            ->add('body')
            ->add('language')
            ->add('state')
            ->add('createdBy')
            ->add('comments');
    }

    public function getPersistentParameters()
    {
        if (!$this->hasRequest()) {
            return array();
        }
        $state = $this->getRequest()->get('state');

        return array(
            'state' => $state,
        );
    }
}

