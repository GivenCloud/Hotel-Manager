# Proyecto de Gestión de Hotel

Este proyecto es una aplicación web desarrollada en Laravel para la gestión de un hotel. Proporciona funcionalidades como reservas de habitaciones, registro de clientes, administración de personal y generación de informes.

## Instalación

1. Clonar el repositorio: `git clone https://github.com/GivenCloud/Hotel-Manager.git`
2. Instalar las dependencias: `composer install`
3. Configurar el archivo de entorno: copiar el archivo `.env.example` y renombrarlo a `.env`. Luego, configurar las variables de entorno necesarias.
4. Generar la clave de la aplicación: `php artisan key:generate`
5. Ejecutar las migraciones de la base de datos: `php artisan migrate`
6. Iniciar el servidor de desarrollo: `php artisan serve`

## Uso

Una vez que el servidor de desarrollo esté en funcionamiento, se puede acceder a la aplicación en `http://localhost:8000`. 