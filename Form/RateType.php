<?php

namespace Liuggio\HelpDeskBundle\Form;

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
                                                                '1' => 'rate_one_star',
                                                                '2' => 'rate_two_stars',
                                                                '3' => 'rate_three_stars',
                                                                '4' => 'rate_four_stars',
                                                                '5' => 'rate_five_stars'),
                                           'required'  => true,
                                           'expanded'=> true)
            );
    }

    public function getName()
    {
        return 'liuggio_HelpDeskBundle_ratetype';
    }


}
?>

