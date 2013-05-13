<?php

namespace Index\Form;

class TestForm extends \K2\Form\Field\AbstractField
{

    public function build(\K2\Form\FormBuilder $builder)
    {
        $builder->add('nombre');
    }

    public function getName()
    {
        return 'form_test';
    }

}
