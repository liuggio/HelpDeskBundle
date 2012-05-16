<?php

namespace Liuggio\HelpDeskTicketSystemBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class CommentType extends AbstractType
{

    public function __construct($ticket_id = null)
    {
        $this->ticket_id = $ticket_id;
    }
    /**
     *
     * @param \Symfony\Component\Form\FormBuilder $builder
     * @param array $options
     */
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('ticket')
            ->add('body')
            ->add('createdBy', 'hidden')
        ;
    }

    public function getName()
    {
        return 'liuggio_helpdeskticketsystembundle_commenttype';
    }

    public function getDefaultOptions(array $options)
    {
        $fixedOptions = array(
            'data_class' => 'Liuggio\HelpDeskTicketSystemBundle\Entity\Comment',
        );
        return array_merge($options, $fixedOptions);
    }
}
