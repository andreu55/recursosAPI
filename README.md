# simpleAPI
API para la aplicación de escritores que buscan inspiración

## Instalación

Después de clonar este repositorio deberás sigue estos pasos:

- Asegúrate de que tienes __permisos de ejecucion__ en las carpetas **storage** y **bootstrap**
- **Local** hacer un chmod 777 de storage y bootstrap `sudo chmod -R 777 bootstrap` `sudo chmod -R 777 storage`
- **Production** cambiaremos el usuario propietario de las carpetas mencionadas `chown -R web10:client0`
- Para confirmar si tienes los permisos de escritura correctamente entra en la url `/public`

- Ejecuta:    
    * `composer install` en prod. o `composer update` en local para crear la carpeta _vendor_
    * `cp .env.example .env`
    * Editamos los datos de bd / `nano .env`
    * `php artisan key:generate`
    * `php artisan config:clear`
- Edita los parámetros de tu base de datos en **.env**
- Ejecuta `php artisan migrate:fresh --seed`
