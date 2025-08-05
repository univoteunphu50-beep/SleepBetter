@echo off
chcp 65001 >nul
title Diagnóstico SleepBetter Docker

echo 🔍 Diagnóstico de SleepBetter Docker
echo =====================================

echo.
echo 📋 Verificando Docker...
docker --version
if %errorlevel% neq 0 (
    echo ❌ Docker no está instalado o no está en el PATH
    echo 💡 Instala Docker Desktop desde: https://www.docker.com/products/docker-desktop/
    pause
    exit /b 1
)

echo ✅ Docker encontrado

echo.
echo 📋 Verificando Docker Compose...
docker-compose --version
if %errorlevel% neq 0 (
    echo ❌ Docker Compose no está disponible
    pause
    exit /b 1
)

echo ✅ Docker Compose encontrado

echo.
echo 📋 Verificando estado de contenedores...
docker-compose ps

echo.
echo 📋 Verificando logs del contenedor web...
docker-compose logs --tail=10 web

echo.
echo 📋 Verificando conectividad...
curl -s http://localhost:8080 >nul 2>&1
if %errorlevel% equ 0 (
    echo ✅ La aplicación responde en http://localhost:8080
) else (
    echo ❌ La aplicación no responde en http://localhost:8080
    echo 💡 Intenta: docker-compose down -v && docker-compose up -d
)

echo.
echo 📋 Verificando puertos en uso...
netstat -an | findstr :8080

echo.
echo 🎯 URLs para probar:
echo - http://localhost:8080
echo - http://localhost:8080/login.php
echo.
echo 🔑 Credenciales:
echo - Usuario: admin
echo - Contraseña: password
echo.
echo 💡 Si hay problemas:
echo 1. docker-compose down -v
echo 2. docker-compose up -d
echo 3. Accede a http://localhost:8080
echo.
pause 