# Aplicación de gestión de Indicadores de Compromiso (IoC)

### Autor: Miquel Vives Marcus
El Trabajo de Fin de Grado ha sido realizado en la Universitat de las Islas Baleares. Se ha desarrollado una aplicación para la gestión de indicadores de compromiso implantada en el CTI de la universidad.

El repositorio contiene todo el código fuente de la aplicación. Está dividido en 4 módulos: el script de tratamiento, registro y envío de mensajes; la creación de la base de datos, el panel de control y el optimizador de listas (usado en API).

## Resumen
Un indicador de compromiso es un objeto que relaciona un ataque informático con la identificación del atacante. En un entorno corporativo es necesaria una correcta gestión para bloquear ataques informáticos y prevenir pérdidas económicas y/o reputacionales. En una organización donde existe una gran cantidad de tráfico en la red, es difícil de mantener, actualmente hay falsos positivos imposibles de rastrear o atacantes no bloqueados por el firewall. En un futuro puede ocurrir el caso de que distintos dispositivos de seguridad no puedan enviarse información debido a no compartir los mismos protocolos de comunicación.

El objetivo de este proyecto es resolver estas problemáticas a partir de la implementación propia de una aplicación que gestione todo el tráfico de avisos de indicadores de compromiso y permita la comunicación entre dispositivos de forma transparente a su protocolo de comunicación. La aplicación debe permitir, desde un único panel de control, administrar un conjunto de dispositivos, usuarios e indicadores de forma sencilla y centralizada.
La creación de este proyecto se ha llevado a cabo totalmente en una organización concreta. Se ha logrado implantarlo en la red interna de la empresa y se tiene previsto que sustituya al actual gestor de indicadores de compromiso. Esto disminuirá el tiempo de realización de ciertas tareas y proveerá mayor seguridad y control a la organización.

## Script de tratamiento, registro y envío de mensajes
_Script_ desarrollado con _Python_ que tratará, registrará y enviará los mensajes (iocmanager_syslog.py).

## Base de datos
Permite crear las clases de la base de datos mediante la importación del archivo iocmanager.sql.

## Panel de control
El panel de control permite gestionar la aplicación. Es una aplicación web, por tanto, el _front-end_ está compuesto por archivos HTML, CSS y JavaScript. Para realizar el _back-end_ se ha usado PHP.

## Optimizador de listas
_Script_ desarrollado con _Python_ que optimiza un conjunto de IP, convirtiendo direcciones IP únicas a bloques de direcciones IP (iocmanager_ipv4_quarantine_optimizer.py).
