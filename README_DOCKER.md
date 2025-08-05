# SleepBetter - Docker Setup

## 🐳 Configuración con Docker

Este proyecto incluye configuración completa de Docker para ejecutar SleepBetter fácilmente.

## 📋 Requisitos Previos

- Docker Desktop instalado
- Docker Compose instalado
- Git (opcional)

## 🚀 Instalación Rápida

### Opción 1: Usar Docker Compose (Recomendado)

1. **Clona o descarga el proyecto**
   ```bash
   git clone <url-del-repositorio>
   cd SleepBetter
   ```

2. **Ejecuta con Docker Compose**
   ```bash
   docker-compose up -d
   ```

3. **Accede a la aplicación**
   - Abre tu navegador
   - Ve a: `http://localhost:8080`
   - Usuario por defecto: `admin`
   - Contraseña por defecto: `password`

### Opción 2: Construir imagen manualmente

1. **Construir la imagen**
   ```bash
   docker build -t sleepbetter .
   ```

2. **Ejecutar el contenedor**
   ```bash
   docker run -d -p 8080:80 --name sleepbetter_app sleepbetter
   ```

## 🔧 Configuración de Base de Datos

### Credenciales por defecto:
- **Host**: `mysql` (dentro de Docker) o `localhost` (desde fuera)
- **Puerto**: `3306`
- **Base de datos**: `sleepbetter_db`
- **Usuario**: `sleepbetter`
- **Contraseña**: `sleepbetter123`
- **Root password**: `root`

### Para conectar desde fuera del contenedor:
- **Host**: `localhost`
- **Puerto**: `3307`
- **Usuario**: `sleepbetter`
- **Contraseña**: `sleepbetter123`

## 📁 Estructura de Volúmenes

```
SleepBetter/
├── uploads/          # Archivos subidos por usuarios
├── logs/            # Logs de la aplicación
└── sleepbetter_db.sql  # Script de inicialización de BD
```

## 🛠️ Comandos Útiles

### Ver logs
```bash
docker-compose logs -f
```

### Detener servicios
```bash
docker-compose down
```

### Reiniciar servicios
```bash
docker-compose restart
```

### Acceder al contenedor
```bash
docker exec -it sleepbetter_web bash
```

### Ver estado de los contenedores
```bash
docker-compose ps
```

## 🔍 Solución de Problemas

### Puerto 8080 ocupado
Si el puerto 8080 está ocupado, cambia el puerto en `docker-compose.yml`:
```yaml
ports:
  - "8081:80"  # Cambia 8080 por 8081
```

### Problemas de permisos
```bash
docker-compose down
docker-compose up -d --force-recreate
```

### Resetear base de datos
```bash
docker-compose down -v
docker-compose up -d
```

## 🌐 Acceso a la Aplicación

- **URL principal**: http://localhost:8080
- **Login**: http://localhost:8080/login.php
- **Dashboard**: http://localhost:8080/index.php

## 📝 Notas Importantes

1. **Primera ejecución**: La base de datos se inicializa automáticamente
2. **Persistencia**: Los datos se mantienen entre reinicios
3. **Seguridad**: Cambia las contraseñas por defecto en producción
4. **Backup**: Los datos se guardan en el volumen `mysql_data`

## 🔒 Configuración de Seguridad

Para producción, modifica las variables de entorno en `docker-compose.yml`:

```yaml
environment:
  MYSQL_ROOT_PASSWORD: tu_password_seguro
  MYSQL_PASSWORD: tu_password_seguro
```

## 📞 Soporte

Si tienes problemas:
1. Verifica que Docker esté ejecutándose
2. Revisa los logs: `docker-compose logs`
3. Asegúrate de que los puertos no estén ocupados
4. Reinicia los contenedores si es necesario

---

**¡Disfruta usando SleepBetter! 🛏️✨** 