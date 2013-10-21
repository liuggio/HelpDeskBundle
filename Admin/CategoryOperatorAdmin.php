<?php

namespace Liuggio\HelpDeskBundle\Admin;

use Liuggio\HelpDeskBundle\Model\BaseCategoryOperator;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Doctrine\ORM\EntityRepository;

class CategoryOperatorAdmin extends Admin
{
    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('emailRequested')
            ->add('operator')
        ;
    }

    public function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('General')
            ->add('emailRequested', 'choice', array(
                'choices' => array(
                    BaseCategoryOperator::EMAIL_REQUESTED => BaseCategoryOperator::EMAIL_REQUESTED,
                    BaseCategoryOperator::EMAIL_NOT_REQUESTED => BaseCategoryOperator::EMAIL_NOT_REQUESTED
                ))
            )

            ->add('operator', null, array(
                    'query_builder'  => function(EntityRepository $er) {
                        return $er->createQueryBuilder('u')
                            ->join('u.groups','g')
                            ->where('g.name IN (:groupsName)')
                            ->orderBy('u.username', 'ASC')
                            ->setParameter('groupsName', array('admin_group','help_desk_group'))
                            ;
                    }
                ))
            ->end();
    }

    public function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('emailRequested')
            ->add('operator')
        ;
    }

    public function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('emailRequested')
           // ->add('operator')
        ;
    }


}