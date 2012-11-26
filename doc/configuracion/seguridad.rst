Seguridad en K2
===============

Gran parte de la aplicaciones desarrolladas en la actualidad requieren de módulos que estan protegidos de alguna manera, es decir, módulos que no pueden ser accedidos por cualquier usuario dentro de la aplicación. Ejemplos de ellos son los backends, los carritos de compras ( para usarlos se debe haber iniciado sesión en el sistema ), creación de blogs, etc..., todos estos módulos de alguna manera requieren algun tipo de autenticación.

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

Cabe destacar que si nuestro proveedor es un servicio propio, este debe extender de **KumbiaPHP\Security\Auth\Provider\AbstractProvider** ó implementar la interfaz **KumbiaPHP\Security\Auth\Provider\UserProviderInterface**

Seccion model_config
____________________
Seccion users
________________
Seccion routes
________________