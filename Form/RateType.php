<?php

namespace Liuggio\HelpDeskTicketSystemBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class RateType extends AbstractType
{

    public function __construct($ticket_id = NULL)
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
            ->add('ticket_id', 'hidden', array(
                'data' => $this->ticket_id
            ))
            ->add('rate', 'choice', array('choices'   => array(
                                                                '1' => 'Poor',
                                                                '2' => 'Fair', '3' => 'Average',
                                                                '4' => 'Good',
                                                                '5' => 'Very Efficient'),
                                           'required'  => true,
                                           'expanded'=> true)
            );
    }

    public function getName()
    {
        return 'liuggio_helpdeskticketsystembundle_ratetype';
    }


}
?>

