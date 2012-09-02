<?php

namespace Demos\Modelos\Model;

use KumbiaPHP\ActiveRecord\ActiveRecord;
use KumbiaPHP\ActiveRecord\Validation\ValidationBuilder;

/**
 * Description of Usuarios
 *
 * @author maguirre
 */
class Usuarios extends ActiveRecord
{

    protected function validations(ValidationBuilder $builder)
    {
        $builder->notNull('login',array(
            'message' => "Escribe tu login por favor :-)"
        ));
        return $builder;
    }

}