# Guía de Despliegue en Render.com con SQLite

## Descripción General

Esta guía te ayudará a desplegar la aplicación E-Commerce en Render.com usando SQLite como base de datos. Render soporta almacenamiento persistente en disco, lo que es perfecto para SQLite.

## Requisitos Previos

1. Una cuenta en [Render.com](https://render.com)
2. Tu repositorio GitHub conectado a Render
3. APP_KEY generada en tu .env local

## Pasos para el Despliegue

### 1. Preparar el Repositorio

Asegúrate de que todos estos archivos están en tu repositorio:
- `render.yaml` - Configuración de Render
- `Procfile` - Define cómo ejecutar la aplicación
- `Dockerfile` - Imagen Docker personalizada (opcional)
- `.env.production.example` - Variables de entorno de ejemplo
- `deploy.sh` - Script de deployment
- `docker/apache.conf` - Configuración de Apache

### 2. Generar APP_KEY

Genera una clave de encriptación para tu aplicación:

```bash
php artisan key:generate
```

Copia el valor generado (sin el prefijo `base64:`).

### 3. Crear un Nuevo Servicio en Render

1. Ve a [Render Dashboard](https://dashboard.render.com)
2. Haz clic en "New +" → "Web Service"
3. Conecta tu repositorio GitHub (autoriza Render si es necesario)
4. Selecciona el repositorio `ecommerce2`
5. Configura los siguientes valores:

   - **Name**: `ecommerce2` (o tu nombre preferido)
   - **Environment**: `Docker`
   - **Build Command**: (dejar en blanco, se usa el Dockerfile)
   - **Start Command**: (dejar en blanco, se usa el Procfile)
   - **Plan**: Free (u otra según necesites)

### 4. Configurar Variables de Entorno

En la sección "Environment" de tu servicio, agrega estas variables:

| Variable | Valor |
|----------|-------|
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_URL` | `https://tu-app.render.com` |
| `APP_KEY` | Tu clave generada (sin `base64:`) |
| `DB_CONNECTION` | `sqlite` |
| `DB_DATABASE` | `/var/data/database.sqlite` |
| `SESSION_DRIVER` | `database` |
| `CACHE_STORE` | `database` |
| `QUEUE_CONNECTION` | `database` |
| `MAIL_MAILER` | `log` |
| `MAIL_HOST` | `smtp.gmail.com` |
| `MAIL_PORT` | `587` |
| `MAIL_ENCRYPTION` | `tls` |
| `MAIL_USERNAME` | Tu email |
| `MAIL_PASSWORD` | Tu contraseña de aplicación |
| `LOG_CHANNEL` | `stack` |

> **⚠️ Advertencia sobre Envío de Correos (SMTP):** El plan gratuito de Render bloquea las conexiones salientes en los puertos 25, 465 y 587 para evitar el spam. Si usas `smtp` y Gmail, recibirás un error `Connection timed out`. 
> Se recomienda usar `MAIL_MAILER=log` para pruebas (los correos y códigos 2FA aparecerán en la pestaña Logs de Render) o integrar un servicio de correo basado en API (como Resend, SendGrid o Mailgun) si necesitas enviar correos reales.

### 5. Crear un Persistent Disk

1. En la página de tu servicio, ve a "Disks"
2. Haz clic en "Add Disk"
3. Configura:
   - **Name**: `sqlite_storage`
   - **Mount Path**: `/var/data`
   - **Size**: 5 GB (suficiente para SQLite)

### 6. Iniciar el Deployment

1. Haz clic en "Deploy"
2. Render ejecutará automáticamente:
   - El build de Docker
   - Las migraciones de base de datos
   - Los seeders
   - La limpieza de caché

3. Espera a que el deployment se complete (puede tomar 5-10 minutos)

## Monitorear el Deployment

Puedes ver los logs en tiempo real desde el dashboard de Render:
1. Ve a tu servicio en Render
2. Abre la sección "Logs"
3. Observa el progreso del build y deployment

## Problemas Comunes

### Error: "Database does not exist"

**Solución**: Asegúrate de que:
1. El Persistent Disk está configurado correctamente
2. El directorio `/var/data` existe y tiene permisos de escritura
3. Las migraciones se ejecutaron en el pre-deploy

### Error: "Permission denied"

**Solución**: 
```bash
chmod -R 775 /var/data
```

### La base de datos se reinicia después de cada deploy

**Nota**: Esto es normal si estás en el plan Free de Render. Considera subir a un plan pagado para deployments más confiables, o usa una base de datos externa.

### Error en migraciones

**Solución**:
1. Verifica el `deploy.sh` tiene permisos ejecutables
2. Revisa que tu `database.php` está configurado para SQLite
3. Comprueba que los seeders no tienen datos duplicados

## Acceder a tu Aplicación

Una vez completado el deployment:

1. Tu aplicación estará disponible en: `https://tu-app-name.render.com`
2. Comparte esta URL de forma segura
3. Todos los datos se almacenarán en el Persistent Disk

## Actualizar la Aplicación

Para hacer deploy de nuevos cambios:

1. Haz push de tus cambios a GitHub
2. Render detectará el cambio automáticamente
3. El nuevo deployment comenzará automáticamente
4. Las migraciones y seeders se ejecutarán si es necesario

## Backup de la Base de Datos

Para hacer backup de tu base de datos SQLite:

```bash
# Desde tu terminal local
render-api db-backup ecommerce2

# O accede via SSH (si está habilitado)
ssh -i ~/.render/ssh_key YOUR_SERVICE_UUID@ssh.render.com
cd /var/data
sqlite3 database.sqlite ".dump" > backup.sql
```

## Optimizaciones Recomendadas

1. **Compresión de Assets**: Ya configurada con Tailwind CSS y Vite
2. **Caching**: Usa Redis para mejor rendimiento (plan pagado)
3. **Background Jobs**: Considera usar un worker separado para colas

## Soporte

Si tienes problemas, revisa:
- [Documentación de Render](https://render.com/docs)
- [Documentación de Laravel](https://laravel.com/docs)
- [Issues en tu repositorio GitHub](https://github.com/tu-usuario/ecommerce2/issues)
