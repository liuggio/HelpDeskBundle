<?php
namespace Liuggio\HelpDeskBundle\Model;

interface InfoExtractorInterface
{

    function extractInfo($info, $ticket);


}
