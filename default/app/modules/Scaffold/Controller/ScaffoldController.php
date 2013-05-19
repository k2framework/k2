<?php

namespace Scaffold\Controller;

use K2\Kernel\App;
use K2\Scaffold\FormConfig;
use K2\ActiveRecord\ActiveRecord;
use K2\Kernel\Controller\Controller;

/**
 * Description of ScaffoldController
 *
 * @author manuel
 */
abstract class ScaffoldController extends Controller
{

    /**
     *
     * @var ActiveRecord 
     */
    public $model;
    public $scaffold = 'kumbia';

    abstract protected function beforeFilter();

    public function index_action($page = 1)
    {
        $this->checkModel();

        $this->paginator = $this->model->paginate($page);
    }

    public function ver_action($id)
    {
        $this->checkModel();

        $this->model = $this->model->findByID($id);
    }

    public function crear_action()
    {
        $this->checkModel();

        $this->form = new FormConfig($this->model);

        if ($this->getRequest()->isMethod('POST')) {

            App::get('mapper')->bindPublic($this->model, 'model');

        }
    }

    public function editar_action($id)
    {
        $this->checkModel();

        $this->setView('crear');

        if (!$this->model = $this->model->findByID($id)) {
            $this->renderNotFound("No existe el Registro");
        }

        $this->form = new FormConfig($this->model);

        if ($this->getRequest()->isMethod('POST')) {

            App::get('mapper')->bindPublic($this->model, 'model');

            if ($this->model->save()) {
                App::get('flash')->success("El Guardado fué exitoso");
                return $this->getRouter()->toAction('index');
            }
        }
    }

    public function borrar_action($id)
    {
        $this->checkModel();

        if (!$model = $this->model->findByID($id)) {
            $this->renderNotFound("No existe el Registro");
        }

        if ($model->deleteByID($id)) {
            App::get('flash')->success("El Registro fué Eliminado");
        } else {
            App::get('flash')->error("No se pudo eliminar el registro");
        }

        return $this->getRouter()->toAction('index');
    }

    private function checkModel()
    {
        if (!$this->model instanceof ActiveRecord) {
            throw new \LogicException("el Atributo \"model\" debe ser una instancia de un objeto ActiveRecord");
        }
    }

}