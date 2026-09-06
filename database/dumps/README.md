# Dumps SQL por instancia

Cada instancia debe tener su propio dump SQL en esta carpeta:

```text
database/dumps/ayudas.sql
database/dumps/becas.sql
database/dumps/becasnt.sql
```

Los archivos `.sql` reales se mantienen fuera del repositorio porque pueden
contener datos personales. Para iniciar una instancia, copiar su dump al
servidor con el nombre indicado en `APP_SQL_DUMP`.

El Compose importa el dump automáticamente solo cuando el volumen de MariaDB
está vacío.
