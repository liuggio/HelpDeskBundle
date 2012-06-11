<?php
namespace Liuggio\HelpDeskBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Liuggio\HelpDeskBundle\Model\TicketInterface;

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
    public function sendEmailToUser($bodyTemplate, $bodyTemplateArgs, $from, $to, $subject = '', $subjectPrefix = '')
    {
        $subject = sprintf('%s %s', $subjectPrefix, $subject);
        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($from)
            ->setTo($to)
            ->setBody($this->getTemplating()->render($bodyTemplate, $bodyTemplateArgs));
        $this->getMailer()->send($message);
        $this->getLogger()->debug('HelpDesk: Email sent to' . $to);
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
        $this->ticketManager = $this->container->get('liuggio_help_desk.ticket.manager_no_doctrine');
        $this->setTemplating($this->container->get('templating'));
        $this->setMailer($this->container->get('mailer'));

        $this->setEmailSender($this->container->getParameter('liuggio_help_desk.email.sender'));
        $this->setEmailSubjectPrefix($this->container->getParameter('liuggio_help_desk.email.subject.prefix'));

        $entity = $args->getEntity();
        $entityManager = $args->getEntityManager();

        $mailTemplateOperator = 'LiuggioHelpDeskBundle:Email:email_operator.html.twig';
        $mailTemplateCreator = 'LiuggioHelpDeskBundle:Email:email_customer.html.twig';

        //always notify the owner
        if ($entity instanceof TicketInterface) {
            $this->getLogger()->info('HelpDeskTicketSystem: persist update' . $isUpdate);

            $this->getTicketManager()->setObjectManager($entityManager);

            $operators = $entity->getCategory()->getOperators();
            foreach ($operators as $operator) {
                $bodyTemplateArgs = array('ticket' => $entity, 'user' => $operator->getEmail(), 'action' => $isUpdate);
                $from = $this->getEmailSender();
                $to = $operator->getEmail();
                $subject = sprintf('Ticket Event on #%d %s', $entity->getId(), $entity->getState());
                $subjectPrefix = $this->getEmailSubjectPrefix();
                $this->sendEmailToUser($mailTemplateOperator, $bodyTemplateArgs, $from, $to, $subject, $subjectPrefix);
            }

            $bodyTemplateArgs = array('ticket' => $entity, 'user' => $entity->getCreatedBy(), 'action' => $isUpdate);
            $from = $this->getEmailSender();
            $to =  $entity->getCreatedBy()->getEmail();
            $subject = sprintf('Ticket Event on #%d %s', $entity->getId(), $entity->getState());
            $subjectPrefix = $this->getEmailSubjectPrefix();
            $this->sendEmailToUser($mailTemplateOperator, $bodyTemplateArgs, $from, $to, $subject, $subjectPrefix);

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