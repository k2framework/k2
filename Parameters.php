<?php

namespace KumbiaPHP\Kernel;

/**
 * Description of Parameters
 *
 * @author manuel
 */
class Parameters
{

    protected $params;

    function __construct(array $params = array())
    {
        $this->params = $params;
    }

    public function has($key)
    {
        return array_key_exists($key, $this->params);
    }

    public function get($key, $default = NULL)
    {
        return $this->has($key) ? $this->params[$key] : $default;
    }

    public function set($key, $value)
    {
        $this->params[$key] = $value;
    }

    public function all()
    {
        return $this->params;
    }

    public function count()
    {
        return count($this->params);
    }

    public function clear()
    {
        $this->params = array();
    }

}