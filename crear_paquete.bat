@echo off
chcp 65001 >nul
title Crear Paquete SleepBetter para Amigo

echo 🛏️ Creando paquete SleepBetter para tu amigo...
echo ================================================

REM Verificar si 7-Zip está instalado
where 7z >nul 2>&1
if %errorlevel% neq 0 (
    echo ❌ 7-Zip no está instalado. Por favor instala 7-Zip desde: https://7-zip.org/
    echo.
    echo Alternativa: Usa WinRAR o cualquier compresor ZIP
    pause
    exit /b 1
)

echo ✅ 7-Zip encontrado

REM Crear directorio temporal
if exist "SleepBetter_Temp" rmdir /s /q "SleepBetter_Temp"
mkdir "SleepBetter_Temp"

echo 📁 Copiando archivos...

REM Copiar archivos principales
xcopy /E /I /Y "productos" "SleepBetter_Temp\productos"
xcopy /E /I /Y "clientes" "SleepBetter_Temp\clientes"
xcopy /E /I /Y "facturacion" "SleepBetter_Temp\facturacion"
xcopy /E /I /Y "usuarios" "SleepBetter_Temp\usuarios"

REM Copiar archivos de configuración
copy "*.php" "SleepBetter_Temp\"
copy "*.html" "SleepBetter_Temp\"
copy "*.js" "SleepBetter_Temp\"
copy "*.jpg" "SleepBetter_Temp\"
copy "*.sql" "SleepBetter_Temp\"

REM Copiar archivos Docker
copy "Dockerfile" "SleepBetter_Temp\"
copy "docker-compose.yml" "SleepBetter_Temp\"
copy "docker-entrypoint.sh" "SleepBetter_Temp\"
copy ".dockerignore" "SleepBetter_Temp\"

REM Copiar scripts de ejecución
copy "run-docker.bat" "SleepBetter_Temp\"
copy "run-docker.sh" "SleepBetter_Temp\"

REM Copiar scripts de diagnóstico
copy "diagnostico.bat" "SleepBetter_Temp\"
copy "diagnostico.sh" "SleepBetter_Temp\"

REM Copiar documentación
copy "GUIA_AMIGO.md" "SleepBetter_Temp\"
copy "README_DOCKER.md" "SleepBetter_Temp\"
copy "INSTRUCCIONES_RAPIDAS.md" "SleepBetter_Temp\"

REM Copiar TCPDF
xcopy /E /I /Y "TCPDF-main" "SleepBetter_Temp\TCPDF-main"

echo ✅ Archivos copiados

REM Crear archivo ZIP
echo 📦 Creando archivo ZIP...
7z a -tzip "SleepBetter_Docker.zip" "SleepBetter_Temp\*"

if %errorlevel% equ 0 (
    echo ✅ Archivo ZIP creado exitosamente!
    echo 📁 Archivo: SleepBetter_Docker.zip
    echo 📏 Tamaño: 
    dir "SleepBetter_Docker.zip" | findstr "SleepBetter_Docker.zip"
) else (
    echo ❌ Error al crear el archivo ZIP
)

REM Limpiar directorio temporal
rmdir /s /q "SleepBetter_Temp"

echo.
echo 🎉 ¡Paquete creado exitosamente!
echo.
echo 📋 Para enviar a tu amigo:
echo 1. 📧 Envía el archivo: SleepBetter_Docker.zip
echo 2. 📧 Envía el archivo: GUIA_AMIGO.md
echo 3. 📧 Envía el enlace: https://www.docker.com/products/docker-desktop/
echo.
echo 💡 Tu amigo solo necesita:
echo - Instalar Docker Desktop
echo - Extraer el ZIP
echo - Ejecutar run-docker.bat
echo - Abrir http://localhost:8080
echo.
pause 