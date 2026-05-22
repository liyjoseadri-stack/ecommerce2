FROM php:8.2-apache

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Instalar extensiones PHP necesarias
RUN docker-php-ext-install pdo pdo_mysql

# Habilitar mod_rewrite
RUN a2enmod rewrite

# Establecer directorio de trabajo
WORKDIR /app

# Copiar solo package.json y package-lock.json PRIMERO
COPY package*.json ./

# Instalar Node.js y npm
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

# Limpiar cache de npm y instalar dependencies
RUN npm cache clean --force && npm install

# AHORA copiar TODO el código de la aplicación
COPY . .

# Copiar configuración de Apache
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

# Compilar assets con Vite DESPUÉS de copiar todo
RUN npm run build

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Instalar dependencias PHP (sin dev)
RUN composer install --no-dev --optimize-autoloader

# Remover node_modules despues del build para reducir tamaño
RUN rm -rf node_modules

# Permisos de almacenamiento
RUN chown -R www-data:www-data storage bootstrap/cache

# Ejecutar migraciones al iniciar
CMD php artisan migrate --force && apache2-foreground
