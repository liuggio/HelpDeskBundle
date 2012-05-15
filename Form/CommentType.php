<?php

namespace Liuggio\HelpDeskTicketSystemBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class CommentType extends AbstractType
{
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
            ->add('createdBy')
        ;
    }

    public function getName()
    {
        return 'liuggio_helpdeskticketsystembundle_commenttype';
    }
}
