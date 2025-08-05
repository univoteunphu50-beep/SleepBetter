# 🚀 Deploy SleepBetter en Render

Esta guía te ayudará a desplegar tu aplicación SleepBetter en Render usando Docker.

## 📋 Prerrequisitos

1. Una cuenta en [Render](https://render.com)
2. Tu código en un repositorio Git (GitHub, GitLab, etc.)
3. Docker configurado localmente para pruebas

## 🔧 Configuración para Render

### 1. Archivos de Configuración

Los siguientes archivos ya están configurados para Render:

- `render.yaml` - Configuración de servicios de Render
- `Dockerfile` - Optimizado para Render
- `docker-entrypoint.sh` - Script de inicio adaptado
- `.dockerignore` - Optimización del build

### 2. Variables de Entorno

Render configurará automáticamente las siguientes variables de entorno:

- `MYSQL_HOST` - Host de la base de datos
- `MYSQL_PORT` - Puerto de la base de datos
- `MYSQL_DATABASE` - Nombre de la base de datos
- `MYSQL_USER` - Usuario de la base de datos
- `MYSQL_PASSWORD` - Contraseña de la base de datos
- `PORT` - Puerto asignado por Render

## 🚀 Pasos para el Deploy

### Paso 1: Preparar el Repositorio

1. Asegúrate de que todos los archivos estén en tu repositorio Git
2. Haz commit y push de los cambios:

```bash
git add .
git commit -m "Configuración para deploy en Render"
git push origin main
```

### Paso 2: Crear el Proyecto en Render

1. Ve a [Render Dashboard](https://dashboard.render.com)
2. Haz clic en "New +"
3. Selecciona "Blueprint"
4. Conecta tu repositorio Git
5. Render detectará automáticamente el archivo `render.yaml`

### Paso 3: Configurar el Blueprint

1. **Nombre del proyecto**: `sleepbetter-app`
2. **Branch**: `main` (o tu rama principal)
3. **Root Directory**: `/` (dejar vacío si el código está en la raíz)

### Paso 4: Revisar la Configuración

Render creará automáticamente:

- **Base de datos MySQL**: `sleepbetter-mysql`
- **Aplicación web**: `sleepbetter-app`

### Paso 5: Deploy

1. Haz clic en "Apply"
2. Render comenzará el proceso de deploy
3. Monitorea el progreso en el dashboard

## 🔍 Verificación del Deploy

### 1. Verificar Logs

En el dashboard de Render, puedes ver los logs en tiempo real:

- **Build logs**: Durante la construcción de la imagen Docker
- **Runtime logs**: Durante la ejecución de la aplicación

### 2. Verificar la Aplicación

Una vez completado el deploy:

1. Ve a la URL proporcionada por Render
2. Verifica que la aplicación cargue correctamente
3. Prueba las funcionalidades principales

### 3. Verificar la Base de Datos

La base de datos MySQL estará disponible y conectada automáticamente.

## 🛠️ Solución de Problemas

### Problema: Build Falla

**Solución**:
- Verifica que el Dockerfile esté correcto
- Revisa los logs de build en Render
- Asegúrate de que todas las dependencias estén incluidas

### Problema: Error de Conexión a Base de Datos

**Solución**:
- Verifica que las variables de entorno estén configuradas
- Revisa los logs de la aplicación
- Asegúrate de que la base de datos esté creada

### Problema: Error 500

**Solución**:
- Revisa los logs de Apache en Render
- Verifica los permisos de archivos
- Asegúrate de que las extensiones PHP estén instaladas

## 📊 Monitoreo

### Logs Disponibles

- **Build logs**: Durante la construcción
- **Runtime logs**: Durante la ejecución
- **Database logs**: Logs de la base de datos

### Métricas

Render proporciona métricas básicas:
- Uso de CPU
- Uso de memoria
- Tiempo de respuesta

## 🔄 Actualizaciones

Para actualizar la aplicación:

1. Haz cambios en tu código local
2. Haz commit y push a tu repositorio
3. Render detectará automáticamente los cambios
4. Iniciará un nuevo deploy automáticamente

## 💰 Costos

- **Plan Free**: Incluye 750 horas por mes
- **Base de datos**: Gratis con limitaciones
- **Aplicación web**: Gratis con limitaciones

## 📞 Soporte

Si tienes problemas:

1. Revisa los logs en Render Dashboard
2. Verifica la configuración en `render.yaml`
3. Consulta la [documentación de Render](https://render.com/docs)

## ✅ Checklist de Deploy

- [ ] Repositorio Git configurado
- [ ] Archivos de configuración en su lugar
- [ ] Proyecto creado en Render
- [ ] Deploy completado exitosamente
- [ ] Aplicación accesible
- [ ] Base de datos conectada
- [ ] Funcionalidades principales probadas

¡Tu aplicación SleepBetter estará lista para usar en Render! 🎉 