# 🛍️ E-Commerce Laravel

Sistema de comercio electrónico desarrollado con **Laravel 12**, implementando autenticación con verificación de dos factores (2FA), gestión completa de productos, carrito de compras y validación de transacciones con ticket digital.

## 🌟 Características Principales

- ✅ **Autenticación de Usuarios** - Registro e inicio de sesión
- ✅ **Verificación 2FA** - Código de verificación enviado por correo
- ✅ **Catálogo de Productos** - Búsqueda y filtrado por categoría
- ✅ **Sistema de Ventas** - Carrito, checkout y generación de tickets
- ✅ **Panel de Administrador** - Estadísticas y gestión de ventas
- ✅ **Control de Acceso** - Roles de usuario (cliente, vendedor, administrador)
- ✅ **Notificaciones** - Correos transaccionales automáticos
- ✅ **Pruebas Automáticas** - Suite completa con 14 tests

## 🛠️ Stack Tecnológico

| Componente | Tecnología |
|-----------|-----------|
| **Framework** | Laravel 12 |
| **Lenguaje** | PHP 8.2+ |
| **Frontend** | Blade Templates + Tailwind CSS |
| **Base de Datos** | SQLite (desarrollo), MySQL (producción) |
| **Testing** | PHPUnit 11 |
| **Build Tools** | Vite, Node.js |
| **CI/CD** | GitHub Actions |
| **Deploy** | Render.com / Heroku / Cloud Platform |

## 📋 Requisitos Previos

- **PHP 8.2** o superior
- **Composer** (gestor de dependencias PHP)
- **Node.js 16+** y npm
- **Git**
- **MySQL** (opcional, para producción)

## 🚀 Instalación Local

### 1. Clonar el Repositorio

```bash
git clone https://github.com/NelsonPDev/ecommerce.git
cd ecommerce
```

### 2. Instalar Dependencias

```bash
# Dependencias PHP
composer install

# Dependencias Node.js
npm install
```

### 3. Configurar Variables de Entorno

```bash
# Copiar archivo de ejemplo
cp .env.example .env

# Generar clave de aplicación
php artisan key:generate
```

**Variables mínimas en .env:**

```env
APP_NAME="E-Commerce"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=database/database.sqlite

MAIL_MAILER=log
```

### 4. Preparar la Base de Datos

```bash
# Crear base de datos SQLite
touch database/database.sqlite

# Ejecutar migraciones
php artisan migrate

# Ejecutar seeders (crea datos de prueba)
php artisan db:seed
```

Esto crea:
- 100 usuarios (30 vendedores, 70 clientes)
- Categorías de productos
- Productos con relaciones
- Usuarios administrador para testing

### 5. Ejecutar la Aplicación

**Terminal 1 - Servidor Laravel:**

```bash
php artisan serve
```

**Terminal 2 - Vite (Assets):**

```bash
npm run dev
```

**Acceder a:** http://localhost:8000

## 🧪 Ejecutar Pruebas

### Todas las Pruebas

```bash
php artisan test
```

### Con Salida Detallada

```bash
php artisan test --verbose
```

### Prueba Específica

```bash
php artisan test tests/Feature/TwoFactorAuthenticationTest.php
```

### Resultado Esperado

```
✓ that true is true
✓ only administrator receives statistics data in dashboard view
✓ the application returns a successful response
✓ user must validate two factor code before login
✓ invalid two factor code does not authenticate user
✓ expired two factor code does not authenticate user
✓ only buyer or manager can view private ticket
✓ manager can validate sale and send notifications
✓ database seeder matches required distribution and relationships

Tests: 14 passed
```

## 📝 Pruebas Incluidas (14 tests)

### Unit Tests (1)
- ✅ `ExampleTest::that_true_is_true` - Validación básica

### Feature Tests (13)

| Test | Validación |
|------|-----------|
| `DashboardStatisticsAuthorizationTest::only_administrator_receives_statistics_data_in_dashboard_view` | Solo admin ve estadísticas |
| `ExampleTest::the_application_returns_a_successful_response` | Página de inicio responde (200) |
| `TwoFactorAuthenticationTest::user_must_validate_two_factor_code_before_login` | 2FA requiere código válido |
| `TwoFactorAuthenticationTest::invalid_two_factor_code_does_not_authenticate_user` | Código inválido rechaza login |
| `TwoFactorAuthenticationTest::expired_two_factor_code_does_not_authenticate_user` | Código expirado rechaza login |
| `VentaValidationAndTicketAccessTest::only_buyer_or_manager_can_view_private_ticket` | Control de acceso a tickets |
| `VentaValidationAndTicketAccessTest::manager_can_validate_sale_and_send_notifications` | Venta genera notificaciones |
| `DatabaseSeederRequirementsTest::database_seeder_matches_required_distribution_and_relationships` | Seeder crea 103 usuarios correctamente |
| `ProjectRequirementTest::login_page_responds_successfully` | Página de login responde correctamente |
| `ProjectRequirementTest::dashboard_requires_authentication` | Dashboard redirige a usuarios no autenticados |
| `ProjectRequirementTest::invalid_login_credentials_show_validation_error` | Login incorrecto muestra errores |
| `ProjectRequirementTest::authenticated_user_can_access_dashboard` | Usuario autenticado accede al dashboard |
| `ProjectRequirementTest::authorized_user_can_store_product_in_database` | Producto creado queda registrado en base de datos |

## 🔐 Credenciales de Prueba (Post-Seeder)

Los seeders crean usuarios automáticamente. Revisar la base de datos:

```bash
php artisan tinker
>>> \App\Models\Usuario::all();
```

## 🌐 Despliegue en Producción

### En Render.com (Recomendado)

1. **Crear cuenta:** https://render.com
2. **Conectar GitHub** en settings
3. **Crear servicio "Web Service"**
4. **Seleccionar repositorio:** `ecommerce`
5. **Configurar variables de entorno:**

```
APP_ENV=production
APP_DEBUG=false
APP_URL=[tu-url-render.com]
DB_CONNECTION=mysql
DB_HOST=[tu-host-mysql]
DB_DATABASE=[nombre-db]
DB_USERNAME=[usuario-mysql]
DB_PASSWORD=[contraseña]
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=[tu-email@gmail.com]
MAIL_PASSWORD=[contraseña-app]
```

6. **Build command:** `composer install && php artisan migrate --force && npm install && npm run build`
7. **Start command:** `php artisan serve --host 0.0.0.0 --port $PORT`

### URL Pública

Aplicación desplegada: https://ecommerce2-zt8z.onrender.com

### CI/CD Automático

El repositorio incluye `.github/workflows/laravel.yml` que:

✅ Ejecuta automáticamente en cada `push` a `main`  
✅ Corre todas las pruebas  
✅ Valida el código antes del despliegue  

El despliegue continuo se configura en Render mediante `render.yaml` y auto-deploy desde la rama `main`.

Ver estado: https://github.com/NelsonPDev/ecommerce/actions

## 📂 Estructura del Proyecto

```
ecommerce/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   └── Middleware/
│   ├── Models/
│   │   ├── Usuario.php
│   │   ├── Producto.php
│   │   ├── Venta.php
│   │   └── ...
│   └── Policies/
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── factories/
├── resources/
│   ├── views/
│   ├── css/
│   └── js/
├── routes/
│   └── web.php
├── tests/
│   ├── Feature/
│   │   ├── TwoFactorAuthenticationTest.php
│   │   ├── VentaValidationAndTicketAccessTest.php
│   │   └── ...
│   └── Unit/
├── .github/
│   └── workflows/
│       └── laravel.yml (GitHub Actions CI/CD)
├── composer.json
├── package.json
└── README.md
```

## 🔒 Seguridad

- ✅ Variables de entorno en `.env` (nunca en Git)
- ✅ Hash seguro de contraseñas (bcrypt)
- ✅ Verificación 2FA por correo
- ✅ Políticas de autorización por rol
- ✅ Validación de entrada en formularios
- ✅ CSRF protection activada
- ✅ XSS protection en vistas

**⚠️ NUNCA subir `.env` al repositorio**

## 🚨 Troubleshooting

### Error: "No such file or directory" en database.sqlite

```bash
touch database/database.sqlite
php artisan migrate
```

### Error: "PDOException: SQLSTATE[HY000]"

Verificar que el archivo `.env` existe:

```bash
cp .env.example .env
php artisan key:generate
```

### Las pruebas fallan

```bash
php artisan config:clear
php artisan test
```

### Problemas con Mail en desarrollo

El `.env` tiene `MAIL_MAILER=log`, revisa `storage/logs/laravel.log`

## 📚 Documentación Adicional

- [Laravel 12 Docs](https://laravel.com/docs/12.x)
- [Tailwind CSS](https://tailwindcss.com)
- [GitHub Actions](https://github.com/features/actions)
- [PHPUnit Testing](https://phpunit.de)

## 👥 Equipo

- **Developer:** Nelson P.
- **Proyecto:** Mini Proyecto 4 - CI/CD y Deploy en Nube
- **Asignatura:** Desarrollo de Aplicaciones Web

## 📄 Licencia

Este proyecto está bajo licencia **MIT**. Ver archivo `LICENSE` para más detalles.

## 📞 Soporte

Para reportar bugs o sugerencias:

1. Abrir issue en [GitHub Issues](https://github.com/NelsonPDev/ecommerce/issues)
2. Enviar email a: [tu-email]

---

**Última actualización:** 21 de mayo de 2026  
**Versión:** 1.0.0 (Production-Ready)
