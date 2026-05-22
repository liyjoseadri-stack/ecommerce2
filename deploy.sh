#!/bin/bash
# Script de deploy automático para Render.com con TiDB Cloud

set -e

echo "🚀 Iniciando deploy en producción..."

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

# Ejecutar migraciones y seeders (borra la BD y la recrea en cada deploy)
echo "🗄️ Borrando, recreando y sembrando la base de datos..."
php artisan migrate:fresh --seed --force

# Limpiar cache
echo "🧹 Limpiando caché..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Generar clave si es necesaria
php artisan key:generate --force || true

echo "✅ Deploy completado exitosamente!"
