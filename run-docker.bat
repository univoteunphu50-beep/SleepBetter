@echo off
chcp 65001 >nul
title SleepBetter Docker Setup

echo 🛏️ SleepBetter - Docker Setup
echo ================================

REM Verificar si Docker está instalado
docker --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ❌ Docker no está instalado. Por favor instala Docker Desktop.
    pause
    exit /b 1
)

REM Verificar si Docker Compose está instalado
docker-compose --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ❌ Docker Compose no está instalado. Por favor instala Docker Compose.
    pause
    exit /b 1
)

echo ✅ Docker y Docker Compose están instalados

:menu
echo.
echo Selecciona una opción:
echo 1) 🚀 Iniciar SleepBetter
echo 2) 🛑 Detener SleepBetter
echo 3) 🔄 Reiniciar SleepBetter
echo 4) 📊 Ver logs
echo 5) 🗑️ Eliminar todo (incluyendo datos)
echo 6) 🔧 Construir imagen
echo 7) 📋 Ver estado
echo 8) 🆘 Ayuda
echo 9) ❌ Salir
echo.
set /p choice="Opción: "

if "%choice%"=="1" goto start
if "%choice%"=="2" goto stop
if "%choice%"=="3" goto restart
if "%choice%"=="4" goto logs
if "%choice%"=="5" goto remove
if "%choice%"=="6" goto build
if "%choice%"=="7" goto status
if "%choice%"=="8" goto help
if "%choice%"=="9" goto exit
echo ❌ Opción inválida
goto menu

:start
echo 🚀 Iniciando SleepBetter...
docker-compose up -d
echo ✅ SleepBetter iniciado!
echo 🌐 Accede a: http://localhost:8080
echo 👤 Usuario: admin
echo 🔑 Contraseña: password
pause
goto menu

:stop
echo 🛑 Deteniendo SleepBetter...
docker-compose down
echo ✅ SleepBetter detenido!
pause
goto menu

:restart
echo 🔄 Reiniciando SleepBetter...
docker-compose restart
echo ✅ SleepBetter reiniciado!
pause
goto menu

:logs
echo 📊 Mostrando logs...
docker-compose logs -f
pause
goto menu

:remove
echo ⚠️ ADVERTENCIA: Esto eliminará todos los datos!
set /p confirm="¿Estás seguro? (y/N): "
if /i "%confirm%"=="y" (
    echo 🗑️ Eliminando todo...
    docker-compose down -v
    docker system prune -f
    echo ✅ Todo eliminado!
) else (
    echo ❌ Operación cancelada
)
pause
goto menu

:build
echo 🔧 Construyendo imagen...
docker-compose build
echo ✅ Imagen construida!
pause
goto menu

:status
echo 📋 Estado de los contenedores:
docker-compose ps
pause
goto menu

:help
echo.
echo 🆘 AYUDA - SleepBetter Docker
echo ================================
echo.
echo 📋 Comandos disponibles:
echo   run-docker.bat start    - Iniciar SleepBetter
echo   run-docker.bat stop     - Detener SleepBetter
echo   run-docker.bat restart  - Reiniciar SleepBetter
echo   run-docker.bat logs     - Ver logs
echo   run-docker.bat remove   - Eliminar todo
echo   run-docker.bat build    - Construir imagen
echo   run-docker.bat status   - Ver estado
echo   run-docker.bat help     - Mostrar ayuda
echo.
echo 🌐 Acceso a la aplicación:
echo   URL: http://localhost:8080
echo   Usuario: admin
echo   Contraseña: password
echo.
echo 🔧 Configuración de base de datos:
echo   Host: localhost
echo   Puerto: 3307
echo   Usuario: sleepbetter
echo   Contraseña: sleepbetter123
echo.
pause
goto menu

:exit
echo 👋 ¡Hasta luego!
pause
exit /b 0 