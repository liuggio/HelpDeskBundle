<?php

namespace Liuggio\HelpDeskTicketSystemBundle\Model;



interface TicketInterface
{

    public function __toString();

    public function setId($id);

    public function getId();

    public function setBody($body);

    public function getBody();
}