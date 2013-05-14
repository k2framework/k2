<?php

namespace Index\Form;

class TestForm extends \K2\Form\Field\AbstractField
{

    public function build(\K2\Form\FormBuilder $builder, array $options = array())
    {
        $e = array('Aragua', 'Falcon', 'Merida');
        $builder->add('estados', 'choice', array(
            'label' => 'Estados',
            'options' => $e,
            'expanded' => true,
            //'multiple' => true,
        ));
        $builder->add('estados2', 'choice', array(
            'label' => 'Estados',
            'options' => $e,
            'expanded' => true,
            'multiple' => true,
        ));
        $builder->add('estados_select', 'choice', array(
            'label' => 'Estados',
            'options' => $e,
            //'multiple' => true,
        ));
    }

    public function getName()
    {
        return 'form_test';
    }

}
