<?php

namespace Liuggio\HelpDeskBundle\Model;

/**
 * Liuggio\HelpDeskBundle\Entity\Category
 */
class Category
{
    /**
     * @var integer $id
     */
    protected $id;

    /**
     * @var string $name
     */
    protected $name;

    /**
     * @var string $description
     */
    protected $description;

    /**
     * @var datetime $createdAt
     */
    protected $createdAt;

    /**
     * @var datetime $updatedAt
     */
    protected $updatedAt;

    /**
     * @var boolean $isEnable
     */
    protected $isEnable;

    /**
     * @var integer $weight
     */
    protected $weight;

    /**
     * @var Application\Sonata\UserBundle\Entity\User
     */
    protected $operators;

    public function __construct()
    {
        $this->operators = new \Doctrine\Common\Collections\ArrayCollection();
    }


    public function prePersist()
    {
        $now = new \DateTime('NOW');
        $this->setUpdatedAt($now);
        $this->setCreatedAt($now);
    }

    public function preUpdate()
    {
        $now = new \DateTime('NOW');
        $this->setUpdatedAt($now);
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    public function __toString()
    {

        return $this->getName();
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Category
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Category
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set createdAt
     *
     * @param datetime $createdAt
     * @return Category
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * Get createdAt
     *
     * @return datetime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param datetime $updatedAt
     * @return Category
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return datetime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set isEnable
     *
     * @param boolean $isEnable
     * @return Category
     */
    public function setIsEnable($isEnable)
    {
        $this->isEnable = $isEnable;
        return $this;
    }

    /**
     * Get isEnable
     *
     * @return boolean
     */
    public function getIsEnable()
    {
        return $this->isEnable;
    }

    /**
     * Set weight
     *
     * @param integer $weight
     * @return Category
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
        return $this;
    }

    /**
     * Get weight
     *
     * @return integer
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * Add operators
     * @deprecated
     * @param Tvision\Bundle\UserBundle\Entity\User $operators
     * @return Category
     */
    public function addOperator($operators)
    {
        $this->operators[] = $operators;
        return $this;
    }

    /**
     * Remove an operator
     * @param Tvision\Bundle\UserBundle\Entity\User
     */
    public function removeOperator($operator)
    {
        $this->operators->removeElement($operator);
    }

    /**
     * Add operators
     *
     * @param Tvision\Bundle\UserBundle\Entity\User $operators
     * @return Category
     */
    public function addUser($operators)
    {
        $this->operators[] = $operators;
        return $this;
    }

    /**
     * Get operators
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getOperators()
    {
        return $this->operators;
    }
}