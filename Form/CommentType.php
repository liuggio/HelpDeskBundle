<?php

namespace Liuggio\HelpDeskBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

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
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('ticket', 'hidden', array(
                'data' => $this->ticket_id,
                'mapped' => false
        ))
            ->add('createdBy','hidden', array(
            'mapped'    => false,
        ))
            ->add('body', 'textarea', array(
                'label' => 'comment_textarea_label',
                'attr'  => array('class' => 'text-area-full input-themed')
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)    {

        $classOptions = array(
            'data_class' =>  '\Liuggio\HelpDeskBundle\Entity\Comment' ,
            'csrf_protection' => false
        );

        $resolver->setDefaults($classOptions);
    }

    public function getName()
    {
        return 'liuggio_HelpDeskBundle_commenttype';
    }


}
