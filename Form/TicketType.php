<?php

namespace Liuggio\HelpDeskTicketSystemBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class TicketType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('category')
            ->add('subject')
            ->add('body')
        ;
    }

    public function getName()
    {
        return 'liuggio_helpdeskticketsystembundle_tickettype';
    }
}
