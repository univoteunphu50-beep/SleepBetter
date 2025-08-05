# 🧪 Carpeta de Tests y Scripts

Esta carpeta contiene todos los archivos de prueba, scripts de configuración y herramientas de desarrollo para SleepBetter.

## 📁 Archivos de Test

### Tests de Funcionalidad
- `test_auth.php` - Test de autenticación
- `test_conexion.php` - Test de conexión a base de datos
- `test_connection.php` - Test alternativo de conexión
- `test_json.php` - Test de respuesta JSON
- `test_session.php` - Test de sesiones PHP
- `test_usuarios.php` - Test del módulo de usuarios
- `test_usuarios_modulo.php` - Test completo del módulo usuarios
- `test_usuarios_paginacion.php` - Test de paginación de usuarios
- `test_db_usuarios.php` - Test de base de datos de usuarios
- `test_productos.php` - Test del módulo de productos
- `test_guardar_producto.php` - Test de guardado de productos
- `test_facturacion.php` - Test del módulo de facturación
- `test_exportar_pdf.php` - Test de exportación a PDF
- `test_vendedor.html` - Test de interfaz de vendedor

### Scripts de Configuración de Base de Datos
- `crear_tablas_stock.php` - Script para crear tablas de stock
- `crear_tabla_actividades.php` - Script para crear tabla de actividades
- `crear_tabla_productos.sql` - SQL para crear tabla de productos
- `crear_tabla_productos_completa.sql` - SQL completo para productos
- `crear_tabla_movimientos_stock.sql` - SQL para movimientos de stock

### Scripts de Datos de Prueba
- `crear_movimientos_test.php` - Generar datos de prueba para movimientos de stock

### Archivos de Imagen de Prueba
- `test_image.jpg` - Imagen de prueba para productos

## 🚀 Cómo Usar

### Ejecutar Tests de Conexión
```bash
# Test básico de conexión
php test/test_conexion.php

# Test de autenticación
php test/test_auth.php

# Test de usuarios
php test/test_usuarios.php
```

### Configurar Base de Datos
```bash
# Crear tablas de stock
php test/crear_tablas_stock.php

# Crear tabla de actividades
php test/crear_tabla_actividades.php
```

### Generar Datos de Prueba
```bash
# Generar movimientos de stock de prueba
php test/crear_movimientos_test.php
```

## 📋 Notas Importantes

1. **Ejecutar en Docker**: Los tests están diseñados para ejecutarse en el entorno Docker
2. **Base de Datos**: Asegúrate de que la base de datos esté configurada antes de ejecutar los scripts
3. **Permisos**: Algunos scripts pueden requerir permisos de escritura
4. **Backup**: Siempre haz backup antes de ejecutar scripts que modifiquen la base de datos

## 🔧 Troubleshooting

### Error de Conexión
- Verifica que el contenedor Docker esté ejecutándose
- Confirma las credenciales de base de datos
- Revisa el archivo `conexion.php`

### Error de Permisos
- Asegúrate de que los archivos tengan permisos de ejecución
- Verifica que el usuario web pueda escribir en los directorios necesarios

### Error de Base de Datos
- Verifica que las tablas existan antes de ejecutar los tests
- Confirma que la estructura de la base de datos sea correcta

## 📝 Mantenimiento

- **Limpieza**: Elimina archivos de test obsoletos regularmente
- **Actualización**: Mantén los tests actualizados con los cambios del código
- **Documentación**: Actualiza este README cuando agregues nuevos tests

## 🎯 Propósito

Esta carpeta sirve para:
- ✅ Probar funcionalidades antes de deploy
- ✅ Verificar configuración de base de datos
- ✅ Generar datos de prueba
- ✅ Debuggear problemas
- ✅ Validar cambios antes de producción 