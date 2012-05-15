<?php
namespace Liuggio\HelpDeskTicketSystemBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class CommentAdmin extends Admin
{

    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('ticket')
            ->add('createdBy')
            ->add('body')
            ->add('createdAt')
        ;
    }

    public function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('General')
                ->add('createdBy', 'sonata_type_model', array('required'=>true))
                ->add('ticket', 'sonata_type_model', array('required'=>true))
                ->add('body')
                ->add('createdAt')
            ->end()
        ;
    }

    public function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('ticket')
            ->add('createdBy')
            ->add('body')
            ->add('createdAt')
        ;
    }

    public function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('ticket')
            ->add('createdBy')
            ->add('body')
            ->add('createdAt')
        ;
    }

}

