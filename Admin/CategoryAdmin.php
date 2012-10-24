<?php
namespace Liuggio\HelpDeskBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Doctrine\ORM\EntityRepository;

class CategoryAdmin extends Admin
{

    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('name')
            ->add('description')
            ->add('isEnable')
            ->add('weight')
            ->add('operators');
    }

    public function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('General')
            ->add('name')
            ->add('description')
            ->add('isEnable')
            ->add('weight')
            ->add('operators',null, array(
                'by_reference'  => false,
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
            ->add('name')
            ->add('description')
            ->add('isEnable')
            ->add('weight')
//            ->add('operators')
        ;
    }

    public function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('name')
            ->add('description')
            ->add('isEnable')
            ->add('weight')
//            ->add('operators')
        ;
    }


}

