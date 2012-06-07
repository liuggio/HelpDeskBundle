<?php
namespace Liuggio\HelpDeskTicketSystemBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Liuggio\HelpDeskTicketSystemBundle\Model\TicketInterface;

class TicketNotifier
{
    private $ticketManager;
    private $logger;
    private $container;
    private $emailSender;
    private $emailSubjectPrefix;


    CONST EVENT_IS_UPDATE = 0;
    CONST EVENT_IS_PERSIST = 1;

    /**
     * @param $mailer
     * @param $translator
     */
    public function __construct($container)
    {
        $this->container = $container;


    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        return $this->postPersistUpdate($args, self::EVENT_IS_UPDATE);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {

        return $this->postPersistUpdate($args, self::EVENT_IS_PERSIST);

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
            ->setBody($this->getTemplating()->render($template, array('user' => $user, 'ticket' => $ticket)));
        $this->getMailer()->send($message);
        $this->getLogger()->debug('???????? email sent to' . $to);
    }
    /**
     *
     *
     * @param LifecycleEventArgs $args
     * @param int $isUpdate
     */
    public function postPersistUpdate(LifecycleEventArgs $args, $isUpdate = self::EVENT_IS_UPDATE)
    {

        $this->setLogger($this->container->get('logger'));
        $this->ticketManager = $this->container->get('liuggio_help_desk_ticket_system.ticket.manager_no_doctrine');
        $this->setTemplating($this->container->get('templating'));
        $this->setMailer($this->container->get('mailer'));

        $entity = $args->getEntity();
        $entityManager = $args->getEntityManager();

        $op_mail_tpl = 'LiuggioHelpDeskTicketSystemBundle:Email:email_operator.html.twig';
        $owner_mail_tpl = 'LiuggioHelpDeskTicketSystemBundle:Email:email_operator.html.twig';

        //always notify the owner
        if ($entity instanceof TicketInterface) {
            $this->getLogger()->info('+++++++++++++++++++++++++' . $isUpdate . 'ENTITY YEAH');

            $this->getTicketManager()->setObjectManager($entityManager);

            $operators = $entity->getCategory()->getOperators();
            foreach ($operators as $operator) {
                $this->sendEmailToUser($entity, $operator, $op_mail_tpl);
            }

            $this->sendEmailToUser($entity, $entity->getCreatedBy(), $owner_mail_tpl);

            // 1. notify all the operator of this ticket
            // 2. notify the owner of this ticket
        }
    }


    public function setTranslator($translator)
    {
        $this->translator = $translator;
    }

    public function getTranslator()
    {
        return $this->translator;
    }

    public function setMailer($mailer)
    {
        $this->mailer = $mailer;
    }

    public function getMailer()
    {
        return $this->mailer;
    }

    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    public function getLogger()
    {
        return $this->logger;
    }

    public function setTicketManager($ticketManager)
    {
        $this->ticketManager = $ticketManager;
    }

    public function getTicketManager()
    {
        return $this->ticketManager;
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

    public function setTemplating($templating)
    {
        $this->templating = $templating;
    }

    public function getTemplating()
    {
        return $this->templating;
    }

    public function setContainer($container)
    {
        $this->container = $container;
    }

    public function getContainer()
    {
        return $this->container;
    }
}