# Proyecto de Gestión de Hotel

Este proyecto es una aplicación web desarrollada en Laravel y Vue para la gestión de un hotel. Proporciona funcionalidades como reservas de habitaciones, registro de clientes, etc.

## Instalación

1. Clonar el repositorio: `git clone https://github.com/GivenCloud/Hotel-Manager.git`
2. Instalar las dependencias: `composer install`
3. Configurar el archivo de entorno: copiar el archivo `.env.example` y renombrarlo a `.env`. Luego, configurar las variables de entorno necesarias.
4. Generar la clave de la aplicación: `php artisan key:generate`
5. Ejecutar las migraciones de la base de datos: `php artisan migrate`
6. Moverse a la carpeta Vue: `cd ./Vue`
7. Instalar las dependencias: `npm install`
8. Ejecutar el comando: `npm run dev`

## Uso

Una vez que el servidor de desarrollo esté en funcionamiento, se puede acceder a la aplicación en `http://localhost:5173`. 