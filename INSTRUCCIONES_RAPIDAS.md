# 🚀 Instrucciones Rápidas - SleepBetter

## Para tu amigo: Cómo ejecutar SleepBetter

### 📋 Requisitos
- Docker Desktop instalado
- Windows, Mac o Linux

### ⚡ Instalación Rápida (3 pasos)

#### 1. Descargar el proyecto
- Descarga todos los archivos de SleepBetter
- Extrae en una carpeta (ej: `C:\SleepBetter`)

#### 2. Ejecutar con Docker
**En Windows:**
```cmd
cd C:\SleepBetter
run-docker.bat
```
Selecciona opción `1` para iniciar

**En Mac/Linux:**
```bash
cd /ruta/a/SleepBetter
chmod +x run-docker.sh
./run-docker.sh start
```

#### 3. Acceder a la aplicación
- Abre tu navegador
- Ve a: `http://localhost:8080`
- **Usuario**: `admin`
- **Contraseña**: `password`

### 🎯 ¡Listo!
Ya puedes usar SleepBetter con todas sus funcionalidades:
- ✅ Gestión de productos
- ✅ Gestión de clientes  
- ✅ Facturación
- ✅ Gestión de usuarios
- ✅ Exportar a Excel

### 🛠️ Comandos útiles

**Iniciar aplicación:**
```bash
docker-compose up -d
```

**Detener aplicación:**
```bash
docker-compose down
```

**Ver logs:**
```bash
docker-compose logs -f
```

**Reiniciar:**
```bash
docker-compose restart
```

### 🔧 Configuración de Base de Datos
- **Host**: `localhost`
- **Puerto**: `3307`
- **Usuario**: `sleepbetter`
- **Contraseña**: `sleepbetter123`
- **Base de datos**: `sleepbetter_db`

### 🆘 Si tienes problemas

1. **Puerto ocupado**: Cambia el puerto en `docker-compose.yml` línea 25:
   ```yaml
   ports:
     - "8081:80"  # Cambia 8080 por 8081
   ```

2. **Docker no inicia**: Verifica que Docker Desktop esté ejecutándose

3. **Error de permisos**: Ejecuta como administrador

4. **Resetear todo**: 
   ```bash
   docker-compose down -v
   docker-compose up -d
   ```

### 📞 Soporte
Si algo no funciona:
1. Verifica que Docker esté ejecutándose
2. Revisa los logs: `docker-compose logs`
3. Reinicia los contenedores
4. Contacta al desarrollador

---

**¡Disfruta usando SleepBetter! 🛏️✨** 