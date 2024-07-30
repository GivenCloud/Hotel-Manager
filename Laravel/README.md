# Proyecto de Gestión de Hotel

Este proyecto es una aplicación web desarrollada en Laravel para la gestión de un hotel. Proporciona funcionalidades como reservas de habitaciones, registro de clientes, administración de personal y generación de informes.

## Características principales

- Reservas de habitaciones: los usuarios pueden realizar reservas de habitaciones seleccionando la fecha de entrada y salida, así como el tipo de habitación deseado.
- Registro de clientes: se puede agregar y editar la información de los clientes, incluyendo nombre, dirección y detalles de contacto.
- Administración de personal: se pueden agregar y gestionar los datos del personal del hotel, como nombre, cargo y horario de trabajo.
- Generación de informes: la aplicación permite generar informes detallados sobre las reservas, clientes y personal del hotel.

## Instalación

1. Clonar el repositorio: `git clone https://github.com/tu-usuario/proyecto-hotel.git`
2. Instalar las dependencias: `composer install`
3. Configurar el archivo de entorno: copiar el archivo `.env.example` y renombrarlo a `.env`. Luego, configurar las variables de entorno necesarias.
4. Generar la clave de la aplicación: `php artisan key:generate`
5. Ejecutar las migraciones de la base de datos: `php artisan migrate`
6. Iniciar el servidor de desarrollo: `php artisan serve`

## Uso

Una vez que el servidor de desarrollo esté en funcionamiento, se puede acceder a la aplicación en `http://localhost:8000`. Desde allí, los usuarios podrán realizar reservas, gestionar clientes y administrar el personal del hotel.

## Contribución

Si deseas contribuir a este proyecto, puedes seguir los siguientes pasos:

1. Realizar un fork del repositorio.
2. Crear una rama para tu contribución: `git checkout -b feature/nueva-funcionalidad`
3. Realizar los cambios necesarios y hacer commit: `git commit -m "Agrega nueva funcionalidad"`
4. Subir los cambios a tu repositorio: `git push origin feature/nueva-funcionalidad`
5. Abrir un pull request en este repositorio.

## Licencia

Este proyecto está bajo la Licencia MIT. Puedes consultar el archivo [LICENSE](LICENSE) para más detalles.
