<?php

namespace Scaffold\Controller;

use \KumbiaPHP\Form\Form;
use KumbiaPHP\ActiveRecord\ActiveRecord;
use KumbiaPHP\Kernel\Controller\Controller;

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
    protected $model;

    abstract protected function beforeFilter();

    public function index($page = 1)
    {
        $this->checkModel();

        $this->paginator = $this->model->paginate($page);
    }

    public function show($id)
    {
        $this->checkModel();

        $this->model = $this->model->findByPK((int) $id);
    }

    public function create()
    {
        $this->checkModel();

        $this->form = new Form($this->model, true);
    }

    public function edit($id)
    {
        
    }

    public function delete($id)
    {
        
    }

    private function checkModel()
    {
        if (!$this->model instanceof ActiveRecord) {
            throw new \LogicException("el Atributo \"model\" debe ser una instancia de un objeto ActiveRecord");
        }
    }

}