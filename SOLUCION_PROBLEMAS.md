# 🆘 Solución de Problemas - SleepBetter Docker

## ❌ "This page isn't working localhost didn't send any data"

### 🔍 Diagnóstico Rápido

#### 1. **Verificar que Docker esté ejecutándose**
```bash
docker --version
docker-compose --version
```

#### 2. **Verificar que los contenedores estén activos**
```bash
docker-compose ps
```
**Deberías ver:**
- `sleepbetter_mysql` - Up
- `sleepbetter_web` - Up

#### 3. **Verificar logs del contenedor web**
```bash
docker-compose logs web
```

### 🛠️ Soluciones Comunes

#### **Problema 1: Puerto ocupado**
**Síntoma:** Error al iniciar contenedores
**Solución:**
1. Cambia el puerto en `docker-compose.yml`:
   ```yaml
   ports:
     - "8081:80"  # Cambia 8080 por 8081
   ```
2. Reinicia:
   ```bash
   docker-compose down
   docker-compose up -d
   ```
3. Accede a: `http://localhost:8081`

#### **Problema 2: Docker Desktop no está ejecutándose**
**Síntoma:** Error "Cannot connect to the Docker daemon"
**Solución:**
1. Abre Docker Desktop
2. Espera a que inicie completamente
3. Ejecuta: `docker-compose up -d`

#### **Problema 3: URL incorrecta**
**Síntoma:** Página en blanco o error
**Solución:**
- ✅ **Correcto**: `http://localhost:8080`
- ❌ **Incorrecto**: `http://localhost/SleepBetter`
- ❌ **Incorrecto**: `http://localhost:8080/SleepBetter`

#### **Problema 4: Contenedores no inician**
**Solución:**
```bash
# Detener todo
docker-compose down -v

# Limpiar imágenes
docker system prune -f

# Reconstruir
docker-compose up -d --build
```

#### **Problema 5: Error de permisos**
**Síntoma:** "Permission denied"
**Solución:**
- **Windows**: Ejecutar como administrador
- **Linux/Mac**: `sudo docker-compose up -d`

### 🔧 Comandos de Diagnóstico

#### **Verificar estado de contenedores:**
```bash
docker-compose ps
```

#### **Ver logs en tiempo real:**
```bash
docker-compose logs -f web
```

#### **Entrar al contenedor web:**
```bash
docker exec -it sleepbetter_web bash
```

#### **Verificar conectividad:**
```bash
curl http://localhost:8080
```

#### **Reiniciar todo:**
```bash
docker-compose down -v
docker-compose up -d
```

### 📋 Checklist de Verificación

- [ ] Docker Desktop está ejecutándose
- [ ] Contenedores están activos (`docker-compose ps`)
- [ ] Puerto 8080 está libre
- [ ] URL correcta: `http://localhost:8080`
- [ ] No hay errores en logs (`docker-compose logs web`)

### 🎯 URLs Correctas

- **Aplicación principal**: `http://localhost:8080`
- **Login**: `http://localhost:8080/login.php`
- **Productos**: `http://localhost:8080/productos/`
- **Clientes**: `http://localhost:8080/clientes/`
- **Facturación**: `http://localhost:8080/facturacion/`

### 🔑 Credenciales de Acceso

- **Usuario**: `admin`
- **Contraseña**: `password`

### 📞 Si nada funciona

1. **Resetear completamente:**
   ```bash
   docker-compose down -v
   docker system prune -f
   docker-compose up -d --build
   ```

2. **Verificar recursos del sistema:**
   - Memoria RAM disponible
   - Espacio en disco
   - CPU no saturada

3. **Contactar al desarrollador** con:
   - Sistema operativo
   - Versión de Docker
   - Logs completos: `docker-compose logs`

---

**¡La mayoría de problemas se resuelven con un reinicio completo! 🔄** 