<?php

namespace Liuggio\HelpDeskBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class CommentType extends AbstractType
{
    private $ticket_id;

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
            ->add('ticket','hidden', array(
            'data' => $this->ticket_id,
            'property_path' => false
        ))
            ->add('createdBy', 'hidden')
            ->add('body', 'textarea', array(
            'label' => 'comment_textarea_label'
        ));
    }

    public function getName()
    {
        return 'liuggio_HelpDeskBundle_commenttype';
    }

    public function getDefaultOptions(array $options)
    {
        $fixedOptions = array(
            'data_class' => 'Liuggio\HelpDeskBundle\Entity\Comment',
        );
        return array_merge($options, $fixedOptions);
    }
}
