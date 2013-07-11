<?php
namespace Liuggio\HelpDeskBundle\Model;


abstract class AbstractInfoExtractor implements InfoExtractorInterface
{

    /**
     * Return result of $this->$info($parameters) call
     * Ex. return $this->username
     *
     * @param $info
     * @param $ticket
     * @return string
     */
    public function extractInfo($info, $ticket)
    {
        $info = ucfirst($info);
        $method = sprintf("get%s", $info);
        try {
            $value = $this->$method($ticket);
            return $value;

        } catch ( \BadMethodCallException $e) {
            return "Error: No info";
        }

    }

}
