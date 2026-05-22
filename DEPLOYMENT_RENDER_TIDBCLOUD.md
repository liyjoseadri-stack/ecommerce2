# Guía de Despliegue en Render.com con TiDB Cloud

## Descripción General

Esta guía te ayudará a desplegar la aplicación E-Commerce en Render.com usando TiDB Cloud como base de datos. TiDB es una base de datos MySQL compatible y escalable.

## Requisitos Previos

1. Una cuenta en [Render.com](https://render.com)
2. Una cuenta en [TiDB Cloud](https://tidbcloud.com)
3. Un cluster TiDB creado en TiDB Cloud
4. Tu repositorio GitHub conectado a Render
5. APP_KEY generada en tu .env local

## Datos de Conexión TiDB Cloud

Tienes los siguientes datos:
- **Host**: `gateway01.us-east-1.prod.aws.tidbcloud.com`
- **Port**: `4000`
- **Username**: `3Xsa1BX9gtxfW9N.root`
- **Password**: Tu contraseña (obtén del dashboard de TiDB)
- **Database**: `sys` (o la base de datos que prefieras)

## Pasos para el Despliegue

### 1. Preparar el Repositorio

Asegúrate de que todos estos archivos están en tu repositorio:
- `render.yaml` - Configuración de Render
- `Dockerfile` - Imagen Docker personalizada
- `.env.production.example` - Variables de entorno de ejemplo
- `deploy.sh` - Script de deployment
- `docker/apache.conf` - Configuración de Apache

### 2. Generar APP_KEY

Genera una clave de encriptación para tu aplicación:

```bash
php artisan key:generate
```

Copia el valor completo (incluyendo el prefijo `base64:` si lo tiene).

### 3. Crear un Nuevo Servicio en Render

1. Ve a [Render Dashboard](https://dashboard.render.com)
2. Haz clic en "New +" → "Web Service"
3. Conecta tu repositorio GitHub (autoriza Render si es necesario)
4. Selecciona el repositorio `ecommerce2`
5. Configura los siguientes valores:

   - **Name**: `ecommerce2` (o tu nombre preferido)
   - **Environment**: `Docker`
   - **Region**: `Oregon` (o tu región preferida)
   - **Branch**: `main`
   - **Plan**: Free (u otra según necesites)

### 4. Configurar Variables de Entorno

En la sección "Environment" de tu servicio, agrega estas variables:

| Variable | Valor |
|----------|-------|
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_URL` | `https://tu-app.render.com` |
| `APP_KEY` | Tu clave generada (con `base64:`) |
| `DB_CONNECTION` | `mysql` |
| `DB_HOST` | `gateway01.us-east-1.prod.aws.tidbcloud.com` |
| `DB_PORT` | `4000` |
| `DB_DATABASE` | `sys` |
| `DB_USERNAME` | `3Xsa1BX9gtxfW9N.root` |
| `DB_PASSWORD` | Tu contraseña de TiDB |
| `DB_CHARSET` | `utf8mb4` |
| `DB_COLLATION` | `utf8mb4_unicode_ci` |
| `SESSION_DRIVER` | `database` |
| `SESSION_DRIVER` | `database` |
| `CACHE_STORE` | `database` |
| `QUEUE_CONNECTION` | `database` |
| `MAIL_MAILER` | `log` |
| `MAIL_HOST` | `smtp.gmail.com` |
| `MAIL_PORT` | `587` |
| `MAIL_ENCRYPTION` | `tls` |
| `MAIL_USERNAME` | Tu email |
| `MAIL_PASSWORD` | Tu contraseña de aplicación |
| `LOG_CHANNEL` | `stderr` |

> **⚠️ Advertencia sobre Envío de Correos (SMTP):** El plan gratuito de Render bloquea las conexiones salientes en los puertos 25, 465 y 587 para evitar el spam. Si usas `smtp` y Gmail, recibirás un error `Connection timed out`. 
> Se recomienda usar `MAIL_MAILER=log` para pruebas (los correos y códigos 2FA aparecerán en la pestaña Logs de Render) o integrar un servicio de correo basado en API (como Resend, SendGrid o Mailgun) si necesitas enviar correos reales.

### 5. Iniciar el Deployment

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

### Error: "Connection refused" a TiDB

**Soluciones**:
1. Verifica que el cluster TiDB está activo en TiDB Cloud
2. Comprueba que la contraseña es correcta
3. Asegúrate de que las variables `DB_HOST`, `DB_PORT`, `DB_USERNAME` y `DB_PASSWORD` son exactas

### Error: "Unknown database"

**Solución**: 
- Asegúrate de que la base de datos `sys` existe en TiDB
- O crea una nueva base de datos en TiDB Cloud y actualiza `DB_DATABASE`

### Error en migraciones

**Soluciones**:
1. Verifica que el usuario de TiDB tiene permisos para crear tablas
2. Revisa que tus migraciones son compatibles con TiDB
3. Comprueba los logs en TiDB Cloud para ver qué pasó

### La aplicación es lenta

**Causa**: Puede ser latencia de red entre Render y TiDB Cloud

**Soluciones**:
1. Asegúrate de que tu región en Render (`Oregon`) es cercana a tu cluster TiDB
2. Considera usar un pool de conexiones
3. Optimiza tus queries en Laravel

## Acceder a tu Aplicación

Una vez completado el deployment:

1. Tu aplicación estará disponible en: `https://tu-app-name.render.com`
2. Comparte esta URL de forma segura
3. Todos los datos se almacenarán en TiDB Cloud

## Actualizar la Aplicación

Para hacer deploy de nuevos cambios:

1. Haz push de tus cambios a GitHub en la rama `main`
2. Render detectará el cambio automáticamente
3. El nuevo deployment comenzará automáticamente
4. Las migraciones se ejecutarán si es necesario

## Backup de la Base de Datos

Para hacer backup de tu base de datos TiDB:

1. Ve a [TiDB Cloud Dashboard](https://tidbcloud.com)
2. Selecciona tu cluster
3. Ve a "Backups"
4. Haz clic en "Create Backup"

O usar la CLI:
```bash
# Exportar los datos
mysqldump -h gateway01.us-east-1.prod.aws.tidbcloud.com \
  -P 4000 \
  -u 3Xsa1BX9gtxfW9N.root \
  -p sys > backup.sql
```

## Monitoreo de Performance

TiDB Cloud te proporciona métricas en tiempo real:
1. Ve a TiDB Cloud Dashboard
2. Selecciona tu cluster
3. Observa métricas de CPU, Memoria, Conexiones, etc.

## Soporte

Si tienes problemas, revisa:
- [Documentación de Render](https://render.com/docs)
- [Documentación de TiDB Cloud](https://docs.tidbcloud.com)
- [Documentación de Laravel](https://laravel.com/docs)
- [Issues en tu repositorio GitHub](https://github.com/tu-usuario/ecommerce2/issues)

## Diferencias con SQLite

- **Escalabilidad**: TiDB es mucho más escalable
- **Concurrencia**: Maneja mejor múltiples conexiones simultáneas
- **Performance**: Mejor rendimiento con datos grandes
- **Costo**: Hay un costo de TiDB Cloud (Free tier disponible)
- **Mantenimiento**: No necesitas gestionar discos persistentes
