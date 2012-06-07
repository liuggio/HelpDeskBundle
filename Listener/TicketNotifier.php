<?php
namespace Liuggio\HelpDeskTicketSystemBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Liuggio\HelpDeskTicketSystemBundle\Model\TicketInterface;

class TicketNotifier
{
    private $ticketManager;
    private $mailer;
    private $translator;
    private $logger;

    CONST EVENT_IS_UPDATE = 0;
    CONST EVENT_IS_PERSIST = 1;

    /**
     * @param $mailer
     * @param $translator
     */
    public function __construct($mailer, $translator, $logger, $ticketManager)
    {
        $this->setMailer($mailer);
        $this->setTranslator($translator);
        $this->setLogger($logger);
        $this->ticketManager = $ticketManager;
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
     *
     *
     * @param LifecycleEventArgs $args
     * @param int $isUpdate
     */
    public function postPersistUpdate(LifecycleEventArgs $args, $isUpdate = self::EVENT_IS_UPDATE)
    {

        $entity = $args->getEntity();
        $entityManager = $args->getEntityManager();

        //always notify the owner
        if ($entity instanceof TicketInterface) {
            $this->getLogger()->info('+++++++++++++++++++++++++' . $isUpdate . 'ENTITY YEAH');
            
            $this->getTicketManager()->setObjectManager($entityManager);

            $operators = $entity->getCategory()->getOperators();
            foreach ($operators as $operator) {
                $this->getTicketManager()->sendEmailToUser($entity, $operator);
            }

            $owner = $entity->getCreatedBy();

            // 1. notify all the operator of this ticket
            // 2. notify the owner of this ticket
        }


//
//        if ($entity instanceof $this->getTicketManager()->getTicketClass()) {
//
//            echo "wooow";
//            //extract all the operators that belongs to this ticket
////            // notify the customercares of the corresponding category
////            $category = $entity->getCategory();
////            $operators = $category->getOperators();
//
////            $message = \Swift_Message::newInstance()
////                    ->setSubject('Hello Email')
////                    ->setFrom('send@example.com')
////                    ->setTo('recipient@example.com')
////                    ->setBody($this->renderView('HelloBundle:Hello:email.txt.twig', array('name' => $name)))
////            ;
////
////            $this->get('mailer')->send($message);
//            //extract the customer that
//
////
////            $body = $event->getBody();
////
////            if (!is_null($event->getOrder()->getSeller())) {
////                $recipients = array($event->getOrder()->getSeller()->getEmail());
////            } else {
////                $recipients = array($event->getOrder()->getOwner()->getEmail());
////            }
////            $message = \Swift_Message::newInstance()
////                ->setFrom('tv.devs@gmail.com')
////                ->setTo($recipients)
////                ->setSubject($this->translator->trans('Terravision: Order Information'))
////                ->setBody($body, 'text/html')
////            ;
////
////            $this->mailer->send($message);
//
//


        //   }
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
}