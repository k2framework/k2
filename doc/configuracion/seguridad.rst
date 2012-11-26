Seguridad en K2
===============

Gran parte de la aplicaciones desarrolladas en la actualidad requieren de módulos que estan protegidos de alguna manera, es decir, módulos que no pueden ser accedidos por cualquier usuario dentro de la aplicación. Ejemplos de ellos son los backends, los carritos de compras ( para usarlos se debe haber iniciado sesión en el sistema ), creación de blogs, etc..., todos estos módulos de alguna manera requieren algun tipo de autenticación.

Como funciona la Seguridad
--------------------------

La seguridad en K2 trabaja mediante el uso de algunas clases tales como: una clase usuario, una clase proveedora de usuarios, y una clase llamada Token que suministra la data del logueo al proveedor contiene al usuario mientras está logueado.

Veamos un ejemplo:

Tenemos una tabla en nuestra base de datos que contiene la información de los usuarios en la aplicación, y queremos loguearnos en la misma. Estamos en el formulario de logueo, ingresamos los datos correspondientes como nombre de usuario y contraseña por ejemplo, y enviamos el formulario.

Ahora se inicia el proceso de seguridad en la app, Se verifica que el formulario de logueo allá sido enviado, y si es así se pasa la data del form a una clase Token. Esta clase es usada por el proveedor de usuarios (En este caso ActiveRecord), para obtener el usuario desde la base de datos(haciendo una consulta por el username), si se encuentra el usuario en la BD se almacena en el Token y se guarda este ultimo en la session.

Con esto ya se ha iniciado sesión en el sistema.

Protegiendo la Aplicación
-------------------------

Las aplicaciones en K2 se protegen configurando el archivo **app/config/security.ini**, en el cual se configuran ciertos parametros que permitirán establecer como queremos proteger la app.

El código por defecto del security.ini es el siguente:

.. code-block:: ini

    [security]
    provider = memory
    type = form
    login_url = /admin/index/login
    target_login = /admin/index
    target_logout = /

    [model_config]
    ;user[class] = Demos\Modelos\Model\Usuarios
    ;user[username] = login

    [users]
    admin[123] = usuario_comun

    [routes]
    /admin* = admin,usuario_comun

Este archivo consta de 4 secciones principales:

    * [security]
    * [model_config]
    * [users]
    * [routes]

Seccion security
________________

En esta sección especificamos como va a ser el proceso de autenticación en la aplicación, de donde viene la data del usuario (provider), donde se encuentra el formulario de logueo, a donde redirigir luego de loguear, entre otras cosas, cada parametro se detalla a continuación:

provider
........

Este parametro especifica el proveedor de los usuarios, es decir de donde vienen los usuarios, de una BD, archivos, memoria, etc...

Los parametros posibles son:

    * memory: cuando usamos memory los usuarios se obtienen de la sección `[users] <#seccion-users>`_ del security.ini
    * active_record: si especificamos active_record, los usuarios vienen del modelo especificado en la sección `[model_config] <#seccion-model_config>`_
    * @servicio: si especificamos el nombre de un servicio (debe ir un @ delante), se usará dicha clase para obtener el usuario.

Cabe destacar que si nuestro proveedor es un servicio propio, este debe extender de **KumbiaPHP\\Security\\Auth\\Provider\\AbstractProvider** ó implementar la interfaz **KumbiaPHP\\Security\\Auth\\Provider\\UserProviderInterface**

type
....

El parametro **type** define el método de obtención de la data para el proceso de logueo, los valores posibles son: 

    * form: muestra un formulario html para el inicio de sesión.
    * http: utiliza la autenticación de php (donde aparece el cuadro de dialogo pidiendonos los datos de acceso).

login_url
.........

El parametro **login_url** nos permite indicar en donde se encuentra nuestro formulario de inicio de sesión ( cuando el parametro **type** es form), y es allí a donde el usuario será redirigido si intenta accedera una url protegida y no está logueado aun.

target_login
............

Ruta hacia la que el usuario será redirigido luego de iniciar sesión (si llegó al formulario de logueo porque fué redirigido por el sistema, al iniciar sesión volverá a la url original a la que intentó acceder).

target_logout
.............

Ruta hacia la que el usuario será redirigido al cerrar sesión en el sistema.

Seccion model_config
____________________

Acá especificamos la configuración para el modelo a usar (La clase que define a un usuario), y el nombre del campo en el form de logueo que contendrá el nombre de usuario.

Los parametros posibles son:

    * user[class]: Acá especificamos la clase que se usará para contener la info del usuario (el proveedor memory tiene su propia clase User).
    * user[username]: nombre del campo en el form de logueo y en el atributo de la clase definida en user[class] (El proveedor usará este valor para obtener el usuario, por ejemplo en el active record). por defecto es username
    * user[password]: nombre del campo en el form que contiene la clave del usuario, lo usa el proveedor memory, y por defecto es password

Seccion users
_____________

En la sección [users] podemos especificar nombres de usuario y claves que serán usados cuando el proveedor sea de tipo memory, muy util cuando queramos proteger un módulo sin usar una base de datos.

La definición para los usuarios es la siguiente:

.. code-block:: ini

    admin[123] = usuario_admin ; nombre de usuario "admin", contraseña "123", rol (perfil) "usuario_admin"
    carlos[carloS_2] = usuario_comun ; nombre de usuario "carlos_2", contraseña "carloS_2", rol (perfil) "usuario_comun"
    maria[123456] = usuario_comun,usuario_admin ; nombre de usuario "maria", contraseña "123456", rol (perfil) "usuario_comun" y "usuario_admin"

El parametro define el nombre de usuario y el indice del mismo indica la contraseña, el valor de dicho parametro define los roles que posee.

Los roles son usados en la sección `[routes] <#seccion-routes>`_ para saber que perfiles tienen acceso a cada ruta.

Seccion routes
______________

Acá especificamos los prefijos de ruta que requieren de autenticación, y los roles que pueden acceder a dichas urls.

Ejemplos:

.. code-block:: ini
    
    /admin* = usuario_admin,usuario_comun ;toda url que comienze por /admin requiere autenticación, y los roles que tienen acceso son usuario_admin,usuario_comun
    /reportes* = TRUE ;toda url que comienze por/reportes requiere autenticación, no importa el rol del usuario que acceda.
    /admin/auditorias* = usuario_admin ;solo los usuarios de rol "usuario_admin" pueden entrar a las rutas que comienzen con /admin/auditorias