# Sistema de Creación de Administradores por Enlace Secreto

## Descripción

Este sistema permite crear usuarios administradores a través de un enlace secreto protegido por un token de seguridad. Es útil para la configuración inicial del sistema o para agregar administradores adicionales de forma segura.

## Características

- ✅ **Seguridad**: Protegido por token secreto
- ✅ **Validación**: Verificación de datos y contraseñas
- ✅ **Interfaz moderna**: Diseño responsive y profesional
- ✅ **Validación en tiempo real**: JavaScript para verificar contraseñas
- ✅ **Mensajes informativos**: Feedback claro al usuario
- ✅ **Prevención de duplicados**: Evita usuarios y emails duplicados

## Cómo usar

### 1. Acceder al enlace secreto

Para crear un administrador, accede a la siguiente URL:

```
http://tu-dominio.com/SleepBetter/crear_admin_secreto.php?token=SleepBetter2024Admin
```

### 2. Llenar el formulario

Completa todos los campos requeridos:
- **Usuario**: Nombre de usuario único
- **Nombre Completo**: Nombre real del administrador
- **Email**: Email válido y único
- **Contraseña**: Mínimo 6 caracteres
- **Confirmar Contraseña**: Debe coincidir con la contraseña

### 3. Crear el administrador

Haz clic en "Crear Administrador" y el sistema:
- Validará todos los datos
- Verificará que no existan duplicados
- Creará el usuario con rol de administrador
- Mostrará un mensaje de confirmación

## Configuración de Seguridad

### Cambiar el token secreto

Por seguridad, se recomienda cambiar el token por defecto. Edita el archivo `crear_admin_secreto.php` y modifica esta línea:

```php
$SECRET_TOKEN = "SleepBetter2024Admin"; // Cambia este token por uno más seguro
```

**Recomendaciones para el token:**
- Usa al menos 20 caracteres
- Combina letras, números y símbolos
- No uses información personal
- Guárdalo en un lugar seguro

### Ejemplo de token seguro:
```php
$SECRET_TOKEN = "SleepBetter_Admin_2024_Secure_Token_XYZ123!@#";
```

## Estructura de la base de datos

El sistema utiliza la tabla `usuarios` con la siguiente estructura:

```sql
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    rol ENUM('admin', 'gerente', 'vendedor') DEFAULT 'vendedor',
    activo TINYINT(1) DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

## Roles de usuario

- **admin**: Acceso completo al sistema
- **gerente**: Acceso limitado (puede gestionar vendedores)
- **vendedor**: Acceso básico

## Validaciones implementadas

### Del lado del servidor (PHP):
- ✅ Campos obligatorios
- ✅ Longitud mínima de contraseña (6 caracteres)
- ✅ Coincidencia de contraseñas
- ✅ Usuario único
- ✅ Email único
- ✅ Email válido
- ✅ Sanitización de datos

### Del lado del cliente (JavaScript):
- ✅ Validación en tiempo real de contraseñas
- ✅ Feedback inmediato al usuario

## Mensajes de error

El sistema muestra mensajes claros para:
- Campos vacíos
- Contraseñas que no coinciden
- Contraseña muy corta
- Usuario ya existe
- Email ya registrado
- Errores de base de datos

## Seguridad adicional

### Recomendaciones:

1. **Cambia el token después de usar**: Una vez creado el administrador, cambia el token
2. **Usa HTTPS**: Siempre accede a través de HTTPS en producción
3. **Monitorea el acceso**: Revisa los logs del servidor
4. **Elimina el archivo**: Considera eliminar el archivo después de usarlo
5. **Backup**: Haz backup de la base de datos antes de usar

### Deshabilitar el acceso:

Para deshabilitar temporalmente el acceso, puedes:
1. Cambiar el token
2. Renombrar el archivo
3. Mover el archivo a una ubicación diferente

## Ejemplo de uso

### URL de acceso:
```
http://localhost/SleepBetter/crear_admin_secreto.php?token=SleepBetter2024Admin
```

### Datos de ejemplo:
- **Usuario**: nuevo_admin
- **Nombre**: Juan Pérez
- **Email**: juan.perez@sleepbetter.com
- **Contraseña**: Admin2024!
- **Confirmar**: Admin2024!

## Solución de problemas

### Error 404:
- Verifica que el token sea correcto
- Asegúrate de que el archivo existe

### Error de conexión a la base de datos:
- Verifica que MySQL esté ejecutándose
- Revisa la configuración en `conexion.php`

### Usuario ya existe:
- Usa un nombre de usuario diferente
- Verifica en la tabla `usuarios`

### Email ya registrado:
- Usa un email diferente
- Verifica en la tabla `usuarios`

## Archivos relacionados

- `crear_admin_secreto.php`: Archivo principal
- `conexion.php`: Configuración de base de datos
- `auth_check.php`: Sistema de autenticación
- `login.php`: Página de inicio de sesión

## Notas importantes

⚠️ **ADVERTENCIA**: Este enlace es secreto y debe mantenerse seguro. No lo compartas públicamente.

🔒 **SEGURIDAD**: Después de crear el administrador, considera cambiar el token o eliminar el archivo.

📝 **REGISTRO**: Todos los administradores creados se registran en la tabla `usuarios` con rol 'admin'.

---

**Desarrollado para Sleep Better - Sistema de Gestión** 