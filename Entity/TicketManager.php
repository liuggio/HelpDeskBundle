<?php

namespace Liuggio\HelpDeskTicketSystemBundle\Entity;


use Doctrine\Common\Persistence\ObjectManager;
use Liuggio\HelpDeskTicketSystemBundle\Model\TicketManager as BaseTicketManager;

class TicketManager extends BaseTicketManager
{
    private $mailer;
    private $translator;
    private $templating;
    private $emailSender;
    private $emailSubjectPrefix;


    public function __construct($objectManager, $ticketClass, $commentClass, $mailer, $translator, $templating, $emailSender, $emailSubjectPrefix)
    {
        parent::__construct($objectManager, $ticketClass, $commentClass);
        $this->setMailer($mailer);
        $this->setTranslator($translator);
        $this->setTemplating($templating);
        $this->setEmailSender($emailSender);
        $this->setEmailSubjectPrefix($emailSubjectPrefix);
    }
    /**
     * @param $ticket
     * @param $user
     * @return bool
     */
    public function isOperatorGrantedForThisTicket($ticket, $operator)
    {

        $qb = $this->getObjectManager()->createQueryBuilder();

        $qb->select('t')
            ->from('LiuggioHelpDeskTicketSystemBundle:Ticket', 't')
            ->leftjoin('t.category', 'ct')
            ->leftjoin('ct.operators','opr')
            ->where('t = :ticket')
            ->andWhere('opr = :user')
            ->setParameter('ticket', $ticket)
            ->setParameter('user', $operator);

        $result = $qb->getQuery()->getResult();

        if(empty($result)) {
            return false;
        }
        return true;
    }

    /**
     * thi function notifies the user
     * @param $user
     */
    public function sendEmailToUser($ticket, $user, $template, $subject = '')
    {
        $to = $user->getEmail();
        $subject = sprintf('%s %s', $this->getEmailSubjectPrefix(), $subject);
        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($this->getEmailSender())
            ->setTo($to)
            ->setBody($this->getTemplating()->renderView($template, array('user' => $user, 'ticket' => $ticket)));
        $this->getMailer()->send($message);
        $this->getLogger()->debug('email sent to' . $to);
    }

    public function setEmailSender($emailSender)
    {
        $this->emailSender = $emailSender;
    }

    public function getEmailSender()
    {
        return $this->emailSender;
    }

    public function setEmailSubjectPrefix($emailSubjectPrefix)
    {
        $this->emailSubjectPrefix = $emailSubjectPrefix;
    }

    public function getEmailSubjectPrefix()
    {
        return $this->emailSubjectPrefix;
    }

    public function setMailer($mailer)
    {
        $this->mailer = $mailer;
    }

    public function getMailer()
    {
        return $this->mailer;
    }

    public function setTemplating($templating)
    {
        $this->templating = $templating;
    }

    public function getTemplating()
    {
        return $this->templating;
    }

    public function setTranslator($translator)
    {
        $this->translator = $translator;
    }

    public function getTranslator()
    {
        return $this->translator;
    }

}