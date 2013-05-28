El Servicio Session
=============

El servicio session es una implementación de la interfaz `K2\\Kernel\\Session\\SessionInterface <https://github.com/k2framework/Core/blob/master/src/K2/Kernel/Session/SessionInterface.php>`_

Mediante este servicio podremos guardar y leer datos de la sessión de php de forma Orientada a Objetos, veamos algunos ejemplos de como usar el servicio:

    <?php
    
    use K2\Kernel\App;
    
    class indexController extends Controller
    {
        //seteando data en la sesión
        public function set_action()
        {
            $session = App::get('session');
            
            $session->set("nombre", "Manuel");
            $session->set("id", 62);
            
            $session->set("nombre", "Aguirre", 'admin'); //etablecemos un namespace
            $session->set("id", 10, 'admin'); //etablecemos un namespace
        }
    
        // leyendo data de la sesión.
        public function get_action()
        {
            echo App::get('session')->get("nombre");
            $this->id = App::get('session')->get("id");
            
            $session = App::get('session');
            
            if($session->has("nombre", 'admin')){
                $this->nombre2 = $session->get('nombre', 'admin'); //lo leemos del namespace especificado
            }
            
            $this->allSession = App::get("session")->all();
            $this->allSessionAdmin = App::get("session")->all('admin');
            $this->allSessionotro = $session->all('otro');
            
        }
    
        // eliminando data de la sesión
        public function del_action()
        {
            App::get('session')->delete("nombre");
            App::get('session')->delete("nombre", 'admin');
            App::get('session')->delete(null, 'admin'); //elimina toda la data del namespace admin
            
            $session App::get("session");
            
            $session->destroy(); //usar con precaución (generalmente se usa antes de un redirect).
        }
    }
