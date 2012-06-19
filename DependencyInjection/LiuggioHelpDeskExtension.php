<?php

namespace Liuggio\HelpDeskBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

use Sonata\EasyExtendsBundle\Mapper\DoctrineCollector;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class LiuggioHelpDeskExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        $container->setAlias('liuggio_help_desk.doctrine.manager', $config['object_manager']);
        $this->registerDoctrineMapping($config);
        $this->registerParameters($container, $config);
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param array $config
     */
    public function registerParameters(ContainerBuilder $container, array $config)
    {
        $container->setParameter('liuggio_help_desk.ticket.class', $config['class']['ticket']);
        $container->setParameter('liuggio_help_desk.comment.class', $config['class']['comment']);
        $container->setParameter('liuggio_help_desk.category.class', $config['class']['category']);
        $container->setParameter('liuggio_help_desk.email.sender', $config['email']['sender']);
        $container->setParameter('liuggio_help_desk.email.subject_prefix', $config['email']['subject_prefix']);
    }

    /**
     * @param array $config
     */
    public function registerDoctrineMapping(array $config)
    {
        if (!class_exists($config['class']['ticket'])) {
            return;
        }

        $collector = DoctrineCollector::getInstance();
        /*
         * Comment has manyToOne with Ticket
         *             manyToOne with User
         * */

        $collector->addAssociation($config['class']['comment'], 'mapManyToOne', array(
            'fieldName' => 'ticket',
            'targetEntity' => $config['class']['ticket']
        ));
        $collector->addAssociation($config['class']['comment'], 'mapManyToOne', array(
            'fieldName' => 'createdBy',
            'targetEntity' => $config['class']['user']
        ));
        /*
         * Ticket has oneToMany with Comment
         *            manyToOne with Category
         *            manyToOne with User
         * */
        $collector->addAssociation($config['class']['ticket'], 'mapOneToMany', array(
            'fieldName' => 'comments',
            'targetEntity' => $config['class']['comment'],
            'cascade' => array(
                'remove',
                'persist',
                'refresh',
                'merge',
                'detach',
            ),
            'mappedBy' => 'ticket',
            'orphanRemoval' => false,
        ));

        $collector->addAssociation($config['class']['ticket'], 'mapManyToOne', array(
            'fieldName' => 'category',
            'targetEntity' => $config['class']['category']
        ));

        $collector->addAssociation($config['class']['ticket'], 'mapManyToOne', array(
            'fieldName' => 'createdBy',
            'targetEntity' => $config['class']['user']
        ));


        /**
         *
         * Category has manyToMany with User
         *
         */
        $collector->addAssociation($config['class']['category'], 'mapManyToMany', array(
            'fieldName' => 'operators',
            'targetEntity' => $config['class']['user'],
            'joinTable' => array(
                'name' => 'ticket__category_user',
                'joinColumns' => array(
                    array(
                        'name' => 'Category_id',
                        'referencedColumnName' => 'id'
                    ),
                ),
                'inverseJoinColumns' => array(
                    array(
                        'name' => 'User_id',
                        'referencedColumnName' => 'id'
                    ),
                ),
            ),
        ));
    }
}
