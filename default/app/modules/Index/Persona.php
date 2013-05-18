<?php

namespace Index;

use K2\Datamapper\MapperInterface;

class Persona extends \K2\ActiveRecord\ActiveRecord implements MapperInterface
{

    protected $nombre;
    public $apellido;

    public function getNombre()
    {
        return $this->nombre;
    }

    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    public function getApellido()
    {
        return $this->apellido;
    }

    public function setApellido($apellido)
    {
        $this->apellido = strtoupper($apellido);
    }

    public function map(\K2\Datamapper\MapperBuilder $builder, array $options = array())
    {
        $builder->add('nombre', array(
            FILTER_SANITIZE_STRING,
            FILTER_CALLBACK => array(
                'options' => 'trim',
            ),
        ));
    }

    protected function validations(\K2\ActiveRecord\Validation\ValidationBuilder $builder)
    {
//        $builder->notNull($field)
        //var_dump($builder->getValidations());
    }

}
