# Aplicación de gestión de Indicadores de Compromiso (IoC)

### Autor: Miquel Vives Marcus
El Trabajo de Fin de Grado ha sido realizado en la Universitat de las Islas Baleares. Se ha desarrollado una aplicación para la gestión de indicadores de compromiso implantada en el CTI de la universidad. 

El repositorio contiene todo el código fuente de la aplicación. Està dividido en 4 módulos: el _script_ de tratamiento, registro y envío de mensajes; la creación de la base de datos, el panel de control y el optimizador de listas (usado en API).  
## Script de tratamiento, registro y envío de mensajes
_Script_ desarrollado con _Python_ que tratará, registrará y enviará los mensajes.

## Base de datos
Permite crear las classes de la base de datos mediante la importación del archivo .sql.

## Panel de control
El panel de control permite gestionar la aplicación. És una aplicación web, por tanto el _front-end_ está compuesto por archivos HTML, CSS y JavaScript. Para realizar el _back-end_ se ha usado PHP.

## Optimizador de listas
_Scriot_ desarrollado con _Python_ que optimiza un conjunto de IP, convirtiendo direcciones IP únicas a bloques de direcciones IP.
