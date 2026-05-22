# Despliegue en Render.com

## Paso 1: Crear Cuenta y Conectar GitHub

1. Ir a https://render.com
2. Sign up con GitHub
3. Autorizar repositorio

## Paso 2: Crear Web Service

1. Click "New +" > "Web Service"
2. Seleccionar `ecommerce` repo
3. Rama: `main`
4. Configurar:
   - **Build:** `composer install && npm install && npm run build`
   - **Start:** `php artisan serve --host 0.0.0.0 --port $PORT`

## Paso 3: Variables de Entorno

En Settings > Environment, agregar:

```
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tu-app.onrender.com

DB_CONNECTION=mysql
DB_HOST=[tu-db-host]
DB_DATABASE=ecommerce
DB_USERNAME=[usuario]
DB_PASSWORD=[contraseña]

MAIL_MAILER=log
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=[tu-email]
MAIL_PASSWORD=[contraseña-app]
```

> **⚠️ Nota sobre correos en Render:** El plan gratuito de Render bloquea el tráfico de salida en puertos SMTP (25, 465, 587). Para que el sistema de 2FA no genere un error `Connection timed out`, se configura `MAIL_MAILER=log`, lo que imprimirá los códigos de acceso en la pestaña "Logs" del dashboard de Render. Para enviar correos reales, actualiza a un plan de pago o usa un servicio basado en API como Resend, Sendgrid o Mailgun.

## Paso 4: Deploy

Click "Deploy" y esperar a que termine.

## Ver Logs

Render Dashboard > Logs (en vivo)

