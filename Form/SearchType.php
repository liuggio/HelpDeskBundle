<?php

namespace Liuggio\HelpDeskBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\Form\FormBuilderInterface;

class SearchType extends AbstractType
{

    /**
     *
     * @param \Symfony\Component\Form\FormBuilder $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('request_pattern', 'search', array(
                      'label' => 'search_ticket_label'
        ));
                
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => false,
            'empty_data' => function (Options $options, $value) {
                return $options['csrf_protection'] ? array() : $value;
            }
        ));
    }

    public function getName()
    {
        return 's';
    }


}
?>

