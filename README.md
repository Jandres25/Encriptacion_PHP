
# Sistema de Autenticación y Recuperación de Contraseña

[![PHP Version](https://img.shields.io/badge/PHP->=7.4-777BB4.svg?style=flat-square&logo=php)](https://php.net/)
[![PHPMailer](https://img.shields.io/badge/PHPMailer-^6.0-1F3B5F.svg?style=flat-square)](https://github.com/PHPMailer/PHPMailer)

Este proyecto es una aplicación web desarrollada en PHP que implementa un sistema de autenticación seguro con funcionalidades de encriptación de contraseñas y recuperación de credenciales mediante correo electrónico.

## 🚀 Características

- Sistema de login seguro
- Encriptación de contraseñas utilizando `password_hash()`
- Sistema de recuperación de contraseña vía email
- Integración con PHPMailer para el envío de correos
- Interfaz de usuario intuitiva

## 📋 Requisitos Previos

- PHP >= 7.4
- MySQL/MariaDB
- Servidor web (Apache recomendado — XAMPP)
- Cuenta de correo con contraseña de aplicación (Gmail recomendado)

## 🔧 Instalación

1. Clona el repositorio:
```bash
git clone https://github.com/Jandres25/Encriptacion_PHP.git
```

2. Navega al directorio del proyecto:
```bash
cd Encriptacion_PHP
```

3. Copia el archivo de configuración y edítalo con tus credenciales:
```bash
cp .env.example .env
```

Variables requeridas en `.env`:
```
DB_HOST=localhost
DB_USERNAME=root
DB_PASSWORD=
DB_DATABASE=login

SMTP_HOST=smtp.gmail.com
SMTP_USERNAME=tu@gmail.com
SMTP_PASSWORD=tu_contraseña_de_aplicacion
SMTP_PORT=587

APP_URL=http://localhost/Encriptacion_PHP
APP_TIMEZONE=America/Bogota
```

4. Importa la base de datos:
```bash
mysql -u tu_usuario -p < login.sql
```

5. Coloca el proyecto en la carpeta web de tu servidor (ej. `htdocs/` en XAMPP) y accede vía `APP_URL`.

## 📁 Estructura del Proyecto

```
├── config/            # Bootstrap: carga .env, DB, constante APP_URL
├── controlador/       # Lógica de negocio (login, reset, sesión)
├── model/             # Acceso a datos y módulo de usuarios
│   └── usuario/       # CRUD de usuarios (admin)
├── templates/         # Header y footer compartidos
├── PHPMailer-master/  # Librería de envío de correos (sin Composer)
├── DataTables/        # Librería para tablas dinámicas
├── css/ js/ img/      # Assets estáticos
├── index.php          # Dashboard principal (requiere sesión)
├── login.php          # Inicio de sesión
├── forgot_password.php # Solicitud de recuperación de contraseña
├── reset_password.php  # Formulario de nueva contraseña (vía token)
├── .env.example       # Plantilla de variables de entorno
└── login.sql          # Schema e inserción de datos iniciales
```

## 🔒 Seguridad

- Contraseñas hasheadas con `password_hash()` (bcrypt)
- Sesión asignada únicamente tras `password_verify()` exitoso
- Tokens de recuperación de 256 bits, con expiración de 1 hora y uso único
- Todos los queries de base de datos usan MySQLi prepared statements
- Correos validados con `filter_var()` antes de consultar la BD
- SMTP con STARTTLS (puerto 587)

## 💡 Uso

1. Accede a `APP_URL/login.php` en tu navegador
2. Inicia sesión con las credenciales del seed (usuario `Admin`)
3. Los usuarios con `EsAdmin = 1` pueden acceder al módulo de gestión de usuarios en `model/usuario/`
4. Para recuperar contraseña, usa el enlace "¿Olvidaste tu contraseña?" en el login

## 🤝 Contribución

Las contribuciones son bienvenidas. Para contribuir:

1. Haz un Fork del proyecto
2. Crea una nueva rama (`git checkout -b feature/AmazingFeature`)
3. Realiza tus cambios
4. Haz commit de tus cambios (`git commit -m 'Add some AmazingFeature'`)
5. Haz Push a la rama (`git push origin feature/AmazingFeature`)
6. Abre un Pull Request

## 📝 Licencia

Este proyecto está bajo la Licencia MIT - mira el archivo `LICENSE` para más detalles.

## 📧 Contacto

Jandres25 - jandrespb4@gmail.com

Link del proyecto: [https://github.com/Jandres25/Encriptacion_PHP](https://github.com/Jandres25/Encriptacion_PHP)