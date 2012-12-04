<?php
namespace Liuggio\HelpDeskBundle\Model;

/**
 * Created by JetBrains PhpStorm.
 * User: toretto460
 * Date: 12/4/12
 * Time: 10:57 AM
 * To change this template use File | Settings | File Templates.
 */
interface InfoExtractorInterface
{

    function extractInfo($info, $ticket);


}
