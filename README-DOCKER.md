# Dharma en Docker

La aplicación legacy requiere PHP 5.6, Apache con `mod_rewrite` y PDO para
MySQL/MariaDB. Todas las instancias usan el mismo código fuente de `ayudas`;
la misma imagen y el mismo `docker-compose.yml` pueden ejecutar varias
instancias (`ayudas`, `becas`, `becasnt`), cada una con su propia base, dominio
y volumen de datos. Los dumps SQL se guardan en `database/dumps/`, uno por
instancia.

## Puesta en marcha

1. Instalar Docker Engine y Docker Compose v2 en el servidor.
2. Copiar este directorio al servidor y entrar en él.
3. Elegir una instancia y crear su archivo de configuración privado:

   ```sh
   cp deploy/instances/ayudas.env.example deploy/instances/ayudas.env
   chmod 600 deploy/instances/ayudas.env
   # Editar el archivo y cambiar ambas contraseñas
   ```

4. Construir y levantar:

   ```sh
   docker compose --env-file deploy/instances/ayudas.env build
   docker compose -p ayudas --env-file deploy/instances/ayudas.env up -d
   docker compose -p ayudas --env-file deploy/instances/ayudas.env ps
   ```

5. Abrir `https://ayudas.municipiogfvarela.gob.ar/`. Traefik accederá al
   puerto 80 interno del contenedor a través de `traefik-net`; MariaDB no queda
   expuesta públicamente.

## Operación

```sh
docker compose logs -f app
docker compose exec db sh -c 'mariadb -u"$MARIADB_USER" -p"$MARIADB_PASSWORD" "$MARIADB_DATABASE"'
docker compose restart app
docker compose down                 # conserva los volúmenes
docker compose down -v              # BORRA la base y archivos persistentes
```

El SQL de `/docker-entrypoint-initdb.d` solo se ejecuta cuando el volumen de
MariaDB está vacío. Para restaurar el dump desde cero hay que detener el stack
y eliminar explícitamente el volumen de esa instancia con `docker compose down -v`.

Los archivos subidos y los backups internos quedan en los volúmenes
`<instancia>_private_data` y `<instancia>_logs_data`; deben incluirse en la
estrategia de backup del servidor junto con `<instancia>_db_data`.

## Agregar otra instancia

Copiar la plantilla correspondiente, ajustar el nombre real de la base y el
    dump SQL ubicado en `database/dumps/`, y levantarla con su propio archivo
    de entorno:

```sh
cp deploy/instances/becas.env.example deploy/instances/becas.env
chmod 600 deploy/instances/becas.env
   docker compose -p becas --env-file deploy/instances/becas.env build
   docker compose -p becas --env-file deploy/instances/becas.env up -d
```

Cada instancia debe ejecutarse desde su propio directorio de despliegue o con
un nombre de proyecto Compose distinto (`-p becas`). En un mismo directorio,
usar `-p` evita que las instancias compartan servicios y volúmenes:

```sh
docker compose -p ayudas --env-file deploy/instances/ayudas.env up -d
docker compose -p becas --env-file deploy/instances/becas.env up -d
```

## Observaciones

- PHP 5.6 está fuera de soporte y debe quedar detrás de HTTPS, firewall y, de
  ser posible, un proxy reverso con autenticación/rate limiting.
- El proyecto contiene credenciales históricas en `settings.php`; el compose
  las reemplaza por variables de entorno. No publicar `.env`.
- El módulo de backups llama a `mysqldump` desde PHP. La imagen de aplicación
  no lo instala; los backups operativos deben hacerse desde el servidor o con
  `docker compose exec db mariadb-dump ...` hasta adaptar ese módulo.
- Los dumps con datos reales no deben subirse al repositorio público. Deben
  copiarse al servidor por un canal seguro o almacenarse en un repositorio
  privado de backups.
- Las bases pueden tener datos distintos, pero el código debe mantenerse
  centralizado en este repositorio.
