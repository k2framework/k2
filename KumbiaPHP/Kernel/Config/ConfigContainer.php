<?php

namespace KumbiaPHP\Kernel\Config;

use KumbiaPHP\Kernel\Kernel;
use KumbiaPHP\Kernel\Parameters;

/**
 * Description of ConfigContainer
 *
 * @author manuel
 */
class ConfigContainer
{

    /**
     *
     * @var Parameters 
     */
    protected $configs;
    protected $kernel;

    public function __construct(Kernel $kernel)
    {
        $this->kernel = $kernel;
        $this->configs = new Parameters();

        $this->init();
    }

    protected function init()
    {
        //obtengo la configuracion general de la App.
        $iniApp = $this->kernel->getAppPath() . 'config/config.ini';
        $this->configs->set('_default', parse_ini_file($iniApp, TRUE));
        foreach (array_unique($this->kernel->getModules()) as $moduleName => $moduleDir) {
            $file = rtrim($moduleDir, '/') . '/config.ini';
            if (is_file($file)) {
                $this->configs->set($moduleName, parse_ini_file($file, TRUE));
            }
        }
    }

    /**
     * Este metodo deberá unificar toda la configuración de cada
     * modulo en un solo esquema
     *  
     */
    public function compile()
    {
        $configsSection = new Parameters();
        $servicesSection = new Parameters();
        $listenersSection = new Parameters();
        $parametersSection = new Parameters();

        foreach ($this->configs->all() as $module => $values) {
            if (array_key_exists('config', $values)) {
                foreach ($values['config'] as $index => $v) {
                    $configsSection->set($index, $v);
                    //$configsSection->set($module . '.' . $index, $v);
                }
            }
            if (array_key_exists('services', $values)) {
                foreach ($values['services'] as $index => $v) {
                    $servicesSection->set($index, $v);
//                    $servicesSection->set($module . '.' . $index, $v);
                }
            }
            if (array_key_exists('listeners', $values)) {
                foreach ($values['listeners'] as $index => $v) {
                    $listenersSection->set($index, $v);
//                    $listenersSection->set($module . '.' . $index, $v);
                }
            }
            if (array_key_exists('parameters', $values)) {
                foreach ($values['parameters'] as $index => $v) {
                    $parametersSection->set($index, $v);
//                    $parametersSection->set($module . '.' . $index, $v);
                }
            }
        }
        $this->configs = new Parameters(array(
                    'config' => $configsSection,
                    'services' => $servicesSection,
                    'listener' => $listenersSection,
                    'parameters' => $parametersSection,
                ));
    }

    public function getConfig()
    {
        return $this->configs;
    }

}