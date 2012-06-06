<?php

namespace Liuggio\HelpDeskTicketSystemBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class TicketType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('category',null, array(
            'label' => 'category_property'
        ))
            ->add('subject','text', array(
            'label' => 'subject_property'
        ))
            ->add('body','textarea', array(
            'label' => 'body_property'
        ));
    }

    public function getName()
    {
        return 'liuggio_helpdeskticketsystembundle_tickettype';
    }
}
