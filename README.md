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
