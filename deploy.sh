#!/bin/bash
# Script de deploy automático para Render.com con SQLite
# Se ejecuta automáticamente durante el pre-deploy en Render

set -e

echo "🚀 Iniciando deploy en producción..."

# Asegurar que el directorio de datos existe
echo "📁 Creando directorio para datos SQLite..."
mkdir -p /var/data
chmod 775 /var/data

# Instalar dependencias (si no están instaladas)
if [ ! -d "vendor" ]; then
    echo "📦 Instalando dependencias PHP..."
    composer install --no-dev --optimize-autoloader
fi

if [ ! -d "node_modules" ]; then
    echo "📦 Instalando dependencias Node..."
    npm install --omit=dev
    
    # Construir assets
    echo "🎨 Compilando assets..."
    npm run build
fi

# Crear base de datos SQLite si no existe
echo "🗄️ Inicializando base de datos SQLite..."
touch /var/data/database.sqlite
chmod 666 /var/data/database.sqlite

# Ejecutar migraciones
echo "🗄️ Ejecutando migraciones..."
php artisan migrate --force

# Seedear datos iniciales (si es primera ejecución)
echo "🌱 Ejecutando seeders..."
php artisan db:seed --force || true

# Limpiar cache
echo "🧹 Limpiando caché..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Generar clave si es necesaria
php artisan key:generate --force || true

echo "✅ Deploy completado exitosamente!"
