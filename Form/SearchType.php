<?php

namespace Liuggio\HelpDeskTicketSystemBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class SearchType extends AbstractType
{

    /**
     *
     * @param \Symfony\Component\Form\FormBuilder $builder
     * @param array $options
     */
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('request_pattern', 'search', array(
                      'label' => 'search_ticket_label'
        ));
                
    }

    public function getDefaultOptions(array $options)
    {
        $options = parent::getDefaultOptions($options);
        $options['csrf_protection'] = false;

        return $options;
    }

    public function getName()
    {
        return 's';
    }


}
?>

