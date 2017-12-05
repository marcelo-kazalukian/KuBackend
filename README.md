# Login, administración de Permisos y administración de Usuarios realizado con KumbiaPHP.

## Características
La aplicación permite tener una parte de la aplicación privada y otra parte pública. 


Todos los controladores que hereden de AdminController son la parte privada (se necesita usuario y password). 

Todos los controladores que hereden de AppController son la parte pública (landing page, API, etc).

Las password se encritan usando password_hash de PHP.

Permite crear perfiles y asociar usuarios a los distintos perfiles. A cada perfil se le asignan los recursos a los que puede ingresar y también permite que un perfil herede los permisos de otro perfil (con esto se consigue una jerarquìa de los perfiles).


## Instalación
1. Definir la constante PASS_KEY en el archivo default/app/libs/k_auth.php.
2. Definir la constante SESSION_KEY en el archivo default/app/libs/k_auth.php.
3. Abrir el archivo /app/temp/db/start.sql generar la clave para los usuarios predeterminados del sistema (ver instrucciones en el archivo start.sql)
4. Ingresar a la aplicación con el usuario generado en el punto 4 y ayudar a mejorarla :)
5. En el archivo /app/models/perfil.php se definen los permisos de cada perfil y que perfiles son administradores de usuarios del sistema.

La aplicación solo es una base para comenzar a trabajar. 

La aplicación No tiene ningún archivo CSS ni javascript incluido.


