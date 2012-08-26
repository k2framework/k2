<?php

namespace KumbiaPHP\Di\Definition;

use KumbiaPHP\Di\Definition\DefinitionInterface;

/**
 * Description of Service
 *
 * @author manuel
 */
class Service implements DefinitionInterface
{

    protected $id;
    protected $className;

    public function __construct($id, $className)
    {
        $this->id = $id;
        $this->className = $className;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getClassName()
    {
        return $this->className;
    }

    public function setClassName($className)
    {
        $this->className = $className;
    }

}