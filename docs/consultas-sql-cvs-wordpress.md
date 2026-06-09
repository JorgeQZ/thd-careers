# Consultas SQL - Usuarios Y CVs En WordPress

## Uso Del Prefijo De Tablas

Estas consultas usan placeholders porque el prefijo real de tablas puede cambiar entre ambientes y tambien puede variar si el sitio esta dentro de un WordPress Network / Multisite.

Reemplaza los placeholders por los prefijos reales de tu base de datos. Incluye el guion bajo final cuando el prefijo lo tenga.

| Placeholder | Uso | Ejemplo sitio principal | Ejemplo micrositio en network |
|---|---|---|---|
| `{USER_TABLE_PREFIX}` | Tablas globales de usuarios: `users`, `usermeta`. | `wp_` | `wp_` |
| `{SITE_TABLE_PREFIX}` | Tablas del sitio/micrositio: `posts`, `postmeta`. | `wp_` | `wp_2_` |

En WordPress Multisite, normalmente `users` y `usermeta` quedan con el prefijo base del network, mientras que `posts` y `postmeta` usan el prefijo del sitio. Por eso las consultas separan ambos placeholders.

Ejemplos:

| Contexto | Tabla usuarios | Tabla usermeta | Tabla posts | Tabla postmeta |
|---|---|---|---|---|
| Sitio principal con `wp_` | `wp_users` | `wp_usermeta` | `wp_posts` | `wp_postmeta` |
| Micrositio con blog ID 2 | `wp_users` | `wp_usermeta` | `wp_2_posts` | `wp_2_postmeta` |
| Sitio principal con `thd_` | `thd_users` | `thd_usermeta` | `thd_posts` | `thd_postmeta` |
| Micrositio con blog ID 3 | `thd_users` | `thd_usermeta` | `thd_3_posts` | `thd_3_postmeta` |

## SQL 1 - Usuarios Con Nombre De Archivo CV En GCS

Consulta principal para obtener usuario y nombre de archivo/objeto guardado en `gcs_url_name`.

```sql
SELECT
    u.ID AS user_id,
    u.user_login,
    u.user_email,
    gcs.meta_value AS gcs_object_name,
    cv.meta_value AS cv_gcs_url
FROM {USER_TABLE_PREFIX}users u
INNER JOIN {USER_TABLE_PREFIX}usermeta gcs
    ON gcs.user_id = u.ID
    AND gcs.meta_key = 'gcs_url_name'
LEFT JOIN {USER_TABLE_PREFIX}usermeta cv
    ON cv.user_id = u.ID
    AND cv.meta_key = 'cv_gcs_url'
WHERE gcs.meta_value IS NOT NULL
  AND gcs.meta_value <> ''
ORDER BY u.ID ASC;
```

El campo que identifica el archivo en el bucket es:

```text
gcs_object_name
```

## SQL 2 - Buscar Un Usuario Especifico Por Email

```sql
SELECT
    u.ID AS user_id,
    u.user_login,
    u.user_email,
    gcs.meta_value AS gcs_object_name,
    cv.meta_value AS cv_gcs_url
FROM {USER_TABLE_PREFIX}users u
LEFT JOIN {USER_TABLE_PREFIX}usermeta gcs
    ON gcs.user_id = u.ID
    AND gcs.meta_key = 'gcs_url_name'
LEFT JOIN {USER_TABLE_PREFIX}usermeta cv
    ON cv.user_id = u.ID
    AND cv.meta_key = 'cv_gcs_url'
WHERE u.user_email = 'correo@ejemplo.com'
ORDER BY u.ID ASC;
```

## SQL 3 - Usuarios Con URL De CV Pero Sin Object Name

Sirve para detectar registros que tienen `cv_gcs_url`, pero no tienen `gcs_url_name`.

```sql
SELECT
    u.ID AS user_id,
    u.user_login,
    u.user_email,
    cv.meta_value AS cv_gcs_url,
    gcs.meta_value AS gcs_object_name
FROM {USER_TABLE_PREFIX}users u
INNER JOIN {USER_TABLE_PREFIX}usermeta cv
    ON cv.user_id = u.ID
    AND cv.meta_key = 'cv_gcs_url'
LEFT JOIN {USER_TABLE_PREFIX}usermeta gcs
    ON gcs.user_id = u.ID
    AND gcs.meta_key = 'gcs_url_name'
WHERE cv.meta_value IS NOT NULL
  AND cv.meta_value <> ''
  AND (gcs.meta_value IS NULL OR gcs.meta_value = '')
ORDER BY u.ID ASC;
```

## SQL 4 - CVs Guardados En Postulaciones

Algunas postulaciones pueden guardar un CV especifico en el meta `nombre_de_cv`.

```sql
SELECT
    p.ID AS postulacion_id,
    p.post_title AS postulacion,
    user_id_meta.meta_value AS user_id,
    u.user_login,
    u.user_email,
    cv_post.meta_value AS gcs_object_name
FROM {SITE_TABLE_PREFIX}posts p
INNER JOIN {SITE_TABLE_PREFIX}postmeta cv_post
    ON cv_post.post_id = p.ID
    AND cv_post.meta_key = 'nombre_de_cv'
LEFT JOIN {SITE_TABLE_PREFIX}postmeta user_id_meta
    ON user_id_meta.post_id = p.ID
    AND user_id_meta.meta_key = 'id_postulante'
LEFT JOIN {USER_TABLE_PREFIX}users u
    ON u.ID = CAST(user_id_meta.meta_value AS UNSIGNED)
WHERE p.post_type = 'postulaciones'
  AND cv_post.meta_value IS NOT NULL
  AND cv_post.meta_value <> ''
ORDER BY p.ID ASC;
```

## SQL 5 - Vista Consolidada De CVs Por Usuario

Une el CV del perfil (`gcs_url_name`) con CVs cargados en postulaciones (`nombre_de_cv`).

```sql
SELECT
    'perfil' AS source_type,
    u.ID AS user_id,
    u.user_login,
    u.user_email,
    NULL AS postulacion_id,
    gcs.meta_value AS gcs_object_name
FROM {USER_TABLE_PREFIX}users u
INNER JOIN {USER_TABLE_PREFIX}usermeta gcs
    ON gcs.user_id = u.ID
    AND gcs.meta_key = 'gcs_url_name'
WHERE gcs.meta_value IS NOT NULL
  AND gcs.meta_value <> ''

UNION ALL

SELECT
    'postulacion' AS source_type,
    u.ID AS user_id,
    u.user_login,
    u.user_email,
    p.ID AS postulacion_id,
    cv_post.meta_value AS gcs_object_name
FROM {SITE_TABLE_PREFIX}posts p
INNER JOIN {SITE_TABLE_PREFIX}postmeta cv_post
    ON cv_post.post_id = p.ID
    AND cv_post.meta_key = 'nombre_de_cv'
LEFT JOIN {SITE_TABLE_PREFIX}postmeta user_id_meta
    ON user_id_meta.post_id = p.ID
    AND user_id_meta.meta_key = 'id_postulante'
LEFT JOIN {USER_TABLE_PREFIX}users u
    ON u.ID = CAST(user_id_meta.meta_value AS UNSIGNED)
WHERE p.post_type = 'postulaciones'
  AND cv_post.meta_value IS NOT NULL
  AND cv_post.meta_value <> ''

ORDER BY user_id ASC, source_type ASC, postulacion_id ASC;
```

## Nota Sobre El Nombre Del Objeto

El valor de `gcs_object_name` corresponde al nombre del archivo/objeto guardado en el bucket.

Si el nombre contiene `/`, debe codificarse como `%2F` antes de usarlo en una URL de metadata o descarga:

```text
object_name         = carpeta/cv.pdf
object_name_encoded = carpeta%2Fcv.pdf
```
