# Bodegas - Sistema de Gestión de Inventario

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-10.10-orange?style=flat-square&logo=laravel" alt="Laravel Version">
  <img src="https://img.shields.io/badge/PHP-8.1+-purple?style=flat-square&logo=php" alt="PHP Version">
  <img src="https://img.shields.io/badge/MySQL-8.0+-blue?style=flat-square&logo=mysql" alt="MySQL Version">
</p>

## Descripción

**Bodegas** es un sistema de gestión de inventario completo construido con Laravel 10. Diseñado para facilitar el control de entradas, salidas, stock y ventas de productos en una bodega o comercio minorista.

### Características Principales

- **Gestión de Inventario**: Control completo de productos con seguimiento por lotes y fechas de vencimiento
- **Entradas de Mercancía**: Registro de compras y entradas de productos con costos
- **Salidas/Ventas**: Registro de ventas con cálculo automático de ganancias
- **Moneda Dual**: Soporte para USD y Bolívares (Bs) con integración BCV
- **Generación de Facturas**: Creación de facturas PDF en formato de 80mm (impresora térmica)
- **Control de Acceso**: Sistema de roles y permisos (Spatie)
- **Dashboard con Estadísticas**: Gráficos interactivos de ventas y ganancias
- **Búsqueda en Tiempo Real**: Búsqueda por producto, código de barras o cliente

## Requisitos

- PHP 8.1 o superior
- Composer
- MySQL 8.0 o superior
- Node.js 16+ (para compilar assets)

## Instalación

1. **Clonar el repositorio**
```bash
git clone <repo-url>
cd bodegas
```

2. **Instalar dependencias de PHP**
```bash
composer install
```

3. **Instalar dependencias de Node.js**
```bash
npm install
```

4. **Configurar el archivo de entorno**
```bash
cp .env.example .env
php artisan key:generate
```

5. **Configurar la base de datos**

Edita el archivo `.env` con tus credenciales:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bodegas
DB_USERNAME=root
DB_PASSWORD=
```

6. **Ejecutar migraciones y seeders**
```bash
php artisan migrate
php artisan db:seed
```

7. **Compilar assets**
```bash
npm run dev
# O para producción:
npm run build
```

8. **Iniciar el servidor**
```bash
php artisan serve
```

## Configuración

### Variables de Entorno Importantes

```env
# Contraseña Maestra (para recuperación de contraseñas)
MASTER_PASSWORD=tu_contraseña_maestra

# Tasa BCV (opcional, se obtiene automáticamente)
# BCV_RATE=50.50
```

### Roles y Permisos

El sistema viene con los siguientes permisos preconfigurados:

- `read-entries`, `create-entries`, `update-entries`, `delete-entries`
- `read-outputs`, `create-outputs`, `update-outputs`, `delete-outputs`
- `read-inventories`

El rol `admin` tiene todos los permisos activos por defecto.

### Usuario Administrador

Después de ejecutar los seeders, puedes iniciar sesión con:

- **Cédula**: 12345678
- **Contraseña**: password

## Estructura del Proyecto

```
bodegas/
├── app/
│   ├── Http/
│   │   ├── Controllers/    # Controladores de la aplicación
│   │   └── Middleware/     # Middleware personalizado
│   ├── Models/             # Modelos Eloquent
│   └── Services/           # Servicios de lógica de negocio
├── database/
│   ├── migrations/         # Migraciones de base de datos
│   └── seeders/            # Seeders para datos iniciales
├── resources/
│   └── views/              # Vistas Blade
└── routes/
    └── web.php             # Rutas web
```

## Tecnologías Utilizadas

- **Backend**: Laravel 10.10
- **Frontend**: Tailwind CSS 4, Bootstrap 5
- **Base de Datos**: MySQL
- **Autenticación**: Laravel Sanctum + Spatie Permissions
- **Gráficos**: ApexCharts
- **PDF**: FPDF (codedge/laravel-fpdf)
- **Build Tool**: Vite

## Licencia

Este proyecto es software privado y no está disponible bajo ninguna licencia de código abierto.

---

**Autor**: Juan Donquis  
**Versión**: 1.0.0  
**Fecha**: Abril 2026
