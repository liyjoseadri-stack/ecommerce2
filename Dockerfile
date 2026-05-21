FROM php:8.2-apache

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    sqlite3 \
    libsqlite3-dev \
    && rm -rf /var/lib/apt/lists/*

# Instalar extensiones PHP necesarias
RUN docker-php-ext-install pdo pdo_sqlite

# Habilitar mod_rewrite
RUN a2enmod rewrite

# Copiar configuración de Apache
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

# Establecer directorio de trabajo
WORKDIR /app

# Copiar archivos de la aplicación
COPY . .

# Crear directorio para la base de datos SQLite
RUN mkdir -p storage/sqlite && chmod -R 775 storage bootstrap/cache

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Instalar dependencias PHP
RUN composer install --no-dev --optimize-autoloader

# Instalar Node.js y dependencias de frontend
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && npm install --omit=dev \
    && npm run build

# Permisos de almacenamiento
RUN chown -R www-data:www-data storage bootstrap/cache

# Ejecutar migraciones al iniciar
CMD php artisan migrate --force && apache2-foreground
