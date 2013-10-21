<?php

namespace Liuggio\HelpDeskBundle\Model;

class BaseCategoryOperator {

    CONST EMAIL_REQUESTED = 'SI';
    CONST EMAIL_NOT_REQUESTED = 'NO';

    /**
     * @var integer $id
     */
    protected $id;

    /**
     * @var
     */
    protected $operator;

    /**
     * @var
     */
    protected $emailRequested;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $emailRequested
     */
    public function setEmailRequested($emailRequested)
    {
        $this->emailRequested = $emailRequested;
    }

    /**
     * @return mixed
     */
    public function getEmailRequested()
    {
        return $this->emailRequested;
    }

    /**
     * @param mixed $operator
     */
    public function setOperator($operator)
    {
        $this->operator = $operator;
    }

    /**
     * @return mixed
     */
    public function getOperator()
    {
        return $this->operator;
    }

    public function __toString()
    {
        return $this->operator .'::'. $this->emailRequested;
    }

    public function hasEmailRequested()
    {
        return $this->emailRequested == self::EMAIL_REQUESTED;
    }

}