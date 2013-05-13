<?php

namespace Index\Form;

class TestForm extends \K2\Form\Field\AbstractField
{

    public function build(\K2\Form\FormBuilder $builder, array $options = array())
    {
        $builder->add('nombre', null, array(
                    'label' => 'Nombres',
                    'value' => 'Manuel José',
                ))
                ->add('apellido');
    }

    public function getName()
    {
        return 'form_test';
    }

}
