# phpost-alpha
PHPOST ALPHA 1.0 (2011)

![version](https://img.shields.io/badge/version-1.0%20alpha-green) ![language](https://img.shields.io/badge/language-PHP-blue)

Copyright: 2010 CubeBox ®

#### Nota importante: esta es una versión deprecada, por lo que no posee ni soporte ni actualizaciones, está en este repositorio a modo histórico y para conservar el código para que esté disponible a quien quiera usarlo. 

## Instalacion:

1. subir los archivos de la carpeta upload a la carpeta del servidor
2. crear una base de datos
3. importar en la base de datos el archivo database.sql
4. editar el archivo config.inc.php con las configuraciones del host
5. hacer la siguiente consulta a la base de datos para poder configurar el sitio:
(Es importante configurar bien el url, porque sino no podrá cargar bien el sitio)

Titulo, slogan, url, email:

```
UPDATE `w_configuracion` SET `titulo` = 'Mi sitio', 
`slogan` = 'Mi slogan', 
`url` = 'http://localhost', 
`email` = 'miemail@email.com' 
WHERE `w_configuracion`.`tscript_id` = 1;
```

6. Ejecutar el sitio web

#### Nota importante: en caso de usar xampp, a partir del punto 3 es importante que tanto apache como mysql se encuentren corriendo de fondo. sino usar el shell para cargar la base mediante el siguiente comando:

```
mysql --user=usuario_de_la_db --password=contra_de_la_db base_de_datos_creada < database.sql
```
