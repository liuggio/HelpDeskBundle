<?php

namespace Liuggio\HelpDeskBundle\Model;


interface CommentInterface
{

    public function __toString();

    public function setId($id);

    public function getId();

    public function setBody($body);

    public function getBody();
}