/* Agregar a las tablas usuario y perfil los campos que se necesiten */
DROP TABLE IF EXISTS `usuario`;
DROP TABLE IF EXISTS `perfil`;

CREATE TABLE `perfil` (
  `id` int(3) NOT NULL AUTO_INCREMENT COMMENT 'Identificador del perfil',
  `nombre` varchar(60) NOT NULL COMMENT 'Nombre del perfil',
  PRIMARY KEY (`id`),
  UNIQUE perfil_nombre_unique(nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT='Tabla que contiene los perfiles';

INSERT INTO `perfil` VALUES (1,'Super Usuario');

CREATE TABLE `usuario` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identificador del usuario',
  `nombre` varchar(70) NOT NULL COMMENT 'Nombre del Usuario',
  `apellido` varchar(70) NOT NULL COMMENT 'Apellido del usuario',
  `login` varchar(45) NOT NULL COMMENT 'Nombre de usuario',
  `password` varchar(256) NOT NULL COMMENT 'Contraseña de acceso al sistema',
  `perfil_id` int(3) NOT NULL COMMENT 'Identificador del perfil', 
  PRIMARY KEY (`id`),
  UNIQUE usuario_login_unique(login),
  KEY `fk_usuario_perfil_idx` (`perfil_id`),
  CONSTRAINT `fk_usuario_perfil` FOREIGN KEY (`perfil_id`) REFERENCES `perfil` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT='Tabla que contiene los usuarios';

/* 
Luego de haber definido la constante PASS_KEY en el archivo default/app/libs/k_auth.php
Generar en el siguiente sitio el password para los dos usuarios predeterminados.
El password se debe generar concatenando el password_seleccionado y la PASS_KEY.
http://www.passwordtool.hu/php5-password-hash-generator
*/

/* Si desea puede cambiar el nombre, apellido y el login para hacerlo más personalizado */
INSERT INTO `usuario` VALUES 
/* Usuario para tareas automatizadas */
(1,'Cronjob','System','cronjob','password_generado', 1),
/* Usuario administrador. Usuario para administrar toda la app.  */
(2,'Super','Admin','admin','password_generado', 1);