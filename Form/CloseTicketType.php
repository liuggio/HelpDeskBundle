<?php

namespace Liuggio\HelpDeskBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class CloseTicketType extends AbstractType
{

    public function __construct($ticket_id)
    {
        $this->ticket_id = $ticket_id;
    }

    /**
     *
     * @param \Symfony\Component\Form\FormBuilder $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('ticket_id', 'hidden');
    }

    public function getName()
    {
        return 'liuggio_HelpDeskBundle_closetickettype';
    }


}

