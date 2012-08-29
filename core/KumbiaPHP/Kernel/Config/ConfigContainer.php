<?php

namespace KumbiaPHP\Kernel\Config;

use KumbiaPHP\Kernel\AppContext;
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

    /**
     *
     * @var AppContext 
     */
    protected $app;

    public function __construct(AppContext $app)
    {
        $this->app = $app;
        $this->configs = new Parameters();

        $this->init();
    }

    protected function init()
    {
        //obtengo la configuracion general de la App.
        $iniApp = $this->app->getAppPath() . 'config/config.ini';
        $this->configs->set('_default', parse_ini_file($iniApp, TRUE));
        foreach (array_unique($this->app->getNamespaces()) as $namespace => $dir) {
            $file = rtrim($dir, '/') . '/' . $namespace . '/config.ini';
            if (is_file($file)) {
                $this->configs->set($namespace, parse_ini_file($file, TRUE));
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

        $this->explodeIndexes($servicesSection, $parametersSection);

        $this->configs = new Parameters(array(
                    'config' => $configsSection,
                    'services' => $servicesSection,
                    'listeners' => $listenersSection,
                    'parameters' => $parametersSection,
                ));
    }

    public function getConfig()
    {
        return $this->configs;
    }

    /**
     * Busca en el config.ini de la aplicación
     * los indices que representen servicios definidos, y que tengan
     * un punto que separe al nombre del servicio de un parametro del mismo
     * ( el parametro tambien debe estár definido )
     * 
     * @example
     * 
     * tenemos un servicio llamada    mi_servico
     * tiene un parametro definido    nombre_app  con valor = 'Mi App'
     *  
     * Si queremos cambiar ese valor, debemos hacerlo en el config.ini de 
     * la App.
     * 
     * y colocar los siguiente en la seccion [config]:
     * 
     * mi_servico.nombre_app = 'Nuevo nombre asignado'
     * 
     * @param Parameters $services
     * @param Parameters $params 
     */
    protected function explodeIndexes(Parameters $services, Parameters $params)
    {
        foreach ($this->configs->get('_default') as $key => $value) {
            if ($key === 'config') {
                foreach ($value as $index => $val) {
                    $explode = explode('.', $index);
                    //si hay un punto y el valor delante del punto
                    //es el nombre de un servicio existente
                    if (count($explode) > 1 && $services->has($explode[0])) {
                        //le asignamos el nuevo valor al parametro
                        //que usará ese servicio
                        if ($params->has($explode[1])) {
                            $params->set($explode[1], $val);
                        }
                    }
                }
            }
        }
    }

}