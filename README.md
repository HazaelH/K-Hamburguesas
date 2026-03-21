<<<<<<< HEAD
<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
=======
<p align="center">
  <a href="https://laravel.com" target="_blank">
    <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="300" alt="Laravel Logo">
  </a>
</p>

<h1 align="center">🍔 K-Hamburguesas</h1>

<p align="center">
  <strong>Sistema Integral de E-Commerce y Gestión de Restaurante</strong><br>
  <i>Llevando el "sabor que manda" a la era digital.</i>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.4-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel">
  <img src="https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind">
  <img src="https://img.shields.io/badge/Stripe-626CD9?style=for-the-badge&logo=Stripe&logoColor=white" alt="Stripe">
  <img src="https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
</p>

<hr>

Bienvenido al repositorio oficial de **K-Hamburguesas**. Este proyecto no es solo un menú en línea, sino una plataforma *Full-Stack* que abarca la experiencia completa de compra del cliente (B2C) y la gestión operativa e inteligencia de negocios del restaurante.

## ✨ Características Principales

El sistema está dividido en dos grandes módulos, diseñados con una arquitectura MVC estricta y protegidos por un sistema de autenticación basado en roles (RBAC).

### 🛒 Experiencia del Cliente (Front-End)
* 🌍 **Catálogo Dinámico y Multilingüe:** Menú interactivo con soporte para Español e Inglés.
* ⚡ **Carrito de Compras AJAX:** Gestión de productos sin recargar la página, cálculo de subtotales, IVA y envío.
* 💳 **Pasarela de Pagos Segura:** Integración con la API de **Stripe** para procesar pagos con tarjeta.
* ✉️ **Comprobantes Automatizados:** Envío de tickets digitales *(Mailables)* en formato Markdown al correo del cliente.
* 🔒 **Seguridad y Privacidad:** Sistema de registro protegido y recuperación de contraseñas por token.

### 👨‍🍳 Gestión del Restaurante (Panel Admin)
* 📊 **Dashboard de Inteligencia de Negocios (BI):** Métricas en tiempo real utilizando `Chart.js` para visualizar evolución de ventas, top de productos y horas pico.
* 🍳 **Monitor de Cocina (KDS) y POS:** Flujo de trabajo en tiempo real para transicionar estados (`Pendiente` ➔ `Preparando` ➔ `Listo` ➔ `Entregado`).
* 👥 **Gestión de Personal:** CRUD completo para cuentas de empleados y asignación de roles.

<hr>

## Instrucciones de Instalación 

Para probar este proyecto en un entorno local, por favor siga estos pasos:

1. **Clonar el repositorio:**
   git clone [https://github.com/HazaelH/K-Hamburguesas.git](https://github.com/HazaelH/K-Hamburguesas.git)
   cd K-Hamburguesas
2. Instalar dependencias de PHP y Node:
    composer install
    npm install
3. Configurar el entorno:
    Copie el archivo de ejemplo y configure sus variables de entorno.
    cp .env.example .env
Nota: Es necesario configurar las credenciales de la base de datos MySQL, las claves de STRIPE_KEY / STRIPE_SECRET, y las credenciales SMTP (ej. Mailtrap) para el envío de correos.
4. Generar la clave de la aplicación:
    php artisan key:generate
5. Migraciones y Seeders (Población de la BD):
    Este comando creará las tablas y generará datos de prueba (productos, categorías y usuarios base).
    php artisan migrate --seed
6. Compilar los assets:
    npm run build
7. Iniciar el servidor local:
    php artisan serve
>>>>>>> 433b97d2585cb68ef73e2952cfbbe7259e4eeed7
