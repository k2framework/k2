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
        ));
    }

    public function getName()
    {
        return 'form_test';
    }

}
