<?php

namespace Liuggio\HelpDeskBundle\Model;


interface TicketInterface
{

    public function __toString();

    public function getId();

    public function setBody($body);

    public function getBody();
}