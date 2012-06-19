<?php

namespace Liuggio\HelpDeskBundle\Model;

use Doctrine\Common\Persistence\ObjectManager;

class AbstractManager
{
    protected $objectManager;
    protected $entityClass;
    protected $entityRepository;

    function __construct($objectManager, $entityClass)
    {
        $this->objectManager = $objectManager;
        $this->entityClass = $entityClass; 
    }

    public function setEntityClass($entityClass)
    {
        $this->entityClass = $entityClass;
    }

    public function getEntityClass()
    {
        return $this->entityClass;
    }

    public function setObjectManager(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function getObjectManager()
    {
        return $this->objectManager;
    }

    public function setEntityRepository($entityRepository)
    {
        $this->entityRepository = $entityRepository;
    }

    public function getEntityRepository()
    {
        if (null == $this->entityRepository) {
            $this->setEntityRepository($this->objectManager->getRepository($this->getEntityClass()));
        }
        return $this->entityRepository;
    }

    /**
     * Returns an empty entity instance
     *
     * @return entityInterface
     */
    public function createEntity()
    {
        $class = $this->getEntityClass();
        $entity = new $class;

        return $entity;
    }


}