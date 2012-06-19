<?php

namespace Liuggio\HelpDeskBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class TicketType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
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
        return 'liuggio_HelpDeskBundle_tickettype';
    }
}
