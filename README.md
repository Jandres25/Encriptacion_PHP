
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
- Servidor web (Apache/Nginx)
- Composer (para gestionar dependencias)
- Cuenta de correo electrónico para el envío de notificaciones

## 🔧 Instalación

1. Clona el repositorio:
```bash
git clone https://github.com/Jandres25/Encriptacion_PHP.git
```

2. Navega al directorio del proyecto:
```bash
cd Encriptacion_PHP
```

3. Instala las dependencias con Composer:
```bash
composer install
```

4. Importa la base de datos:
```bash
mysql -u tu_usuario -p < login.sql
```

5. Configura las credenciales de tu base de datos y correo electrónico en los archivos correspondientes.

## 📁 Estructura del Proyecto

```
├── DataTables/        # Librería para tablas dinámicas
├── PHPMailer-master/  # Librería para envío de correos
├── controlador/       # Lógica de negocio
├── css/              # Estilos CSS
├── img/              # Imágenes del proyecto
├── js/               # Scripts JavaScript
├── model/            # Modelos de datos
├── templates/        # Plantillas HTML
├── webfonts/         # Fuentes web
├── index.php         # Punto de entrada
├── login.php         # Manejo de autenticación
├── forgot_password.php # Recuperación de contraseña
├── reset_password.php # Restablecimiento de contraseña
└── README.md         # Documentación
```

## 🔒 Seguridad

El proyecto implementa las siguientes medidas de seguridad:

- Encriptación de contraseñas mediante `password_hash()`
- Protección contra inyección SQL
- Tokens seguros para recuperación de contraseña
- Validación de datos de entrada

## 💡 Uso

1. Accede a la aplicación a través de tu navegador web
2. Regístrate como nuevo usuario
3. Inicia sesión con tus credenciales
4. En caso de olvidar tu contraseña, utiliza la opción "¿Olvidaste tu contraseña?"

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