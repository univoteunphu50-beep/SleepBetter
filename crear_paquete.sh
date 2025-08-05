#!/bin/bash

echo "🛏️ Creando paquete SleepBetter para tu amigo..."
echo "================================================"

# Verificar si zip está instalado
if ! command -v zip &> /dev/null; then
    echo "❌ zip no está instalado. Por favor instala zip:"
    echo "Ubuntu/Debian: sudo apt-get install zip"
    echo "CentOS/RHEL: sudo yum install zip"
    echo "macOS: brew install zip"
    exit 1
fi

echo "✅ zip encontrado"

# Crear directorio temporal
if [ -d "SleepBetter_Temp" ]; then
    rm -rf "SleepBetter_Temp"
fi
mkdir "SleepBetter_Temp"

echo "📁 Copiando archivos..."

# Copiar archivos principales
cp -r productos SleepBetter_Temp/
cp -r clientes SleepBetter_Temp/
cp -r facturacion SleepBetter_Temp/
cp -r usuarios SleepBetter_Temp/

# Copiar archivos de configuración
cp *.php SleepBetter_Temp/ 2>/dev/null || true
cp *.html SleepBetter_Temp/ 2>/dev/null || true
cp *.js SleepBetter_Temp/ 2>/dev/null || true
cp *.jpg SleepBetter_Temp/ 2>/dev/null || true
cp *.sql SleepBetter_Temp/ 2>/dev/null || true

# Copiar archivos Docker
cp Dockerfile SleepBetter_Temp/ 2>/dev/null || true
cp docker-compose.yml SleepBetter_Temp/ 2>/dev/null || true
cp docker-entrypoint.sh SleepBetter_Temp/ 2>/dev/null || true
cp .dockerignore SleepBetter_Temp/ 2>/dev/null || true

# Copiar scripts de ejecución
cp run-docker.bat SleepBetter_Temp/ 2>/dev/null || true
cp run-docker.sh SleepBetter_Temp/ 2>/dev/null || true

# Copiar scripts de diagnóstico
cp diagnostico.bat SleepBetter_Temp/ 2>/dev/null || true
cp diagnostico.sh SleepBetter_Temp/ 2>/dev/null || true

# Copiar documentación
cp GUIA_AMIGO.md SleepBetter_Temp/ 2>/dev/null || true
cp README_DOCKER.md SleepBetter_Temp/ 2>/dev/null || true
cp INSTRUCCIONES_RAPIDAS.md SleepBetter_Temp/ 2>/dev/null || true

# Copiar TCPDF
cp -r TCPDF-main SleepBetter_Temp/ 2>/dev/null || true

echo "✅ Archivos copiados"

# Crear archivo ZIP
echo "📦 Creando archivo ZIP..."
cd SleepBetter_Temp
zip -r ../SleepBetter_Docker.zip ./*
cd ..

if [ $? -eq 0 ]; then
    echo "✅ Archivo ZIP creado exitosamente!"
    echo "📁 Archivo: SleepBetter_Docker.zip"
    echo "📏 Tamaño: $(du -h SleepBetter_Docker.zip | cut -f1)"
else
    echo "❌ Error al crear el archivo ZIP"
fi

# Limpiar directorio temporal
rm -rf "SleepBetter_Temp"

echo ""
echo "🎉 ¡Paquete creado exitosamente!"
echo ""
echo "📋 Para enviar a tu amigo:"
echo "1. 📧 Envía el archivo: SleepBetter_Docker.zip"
echo "2. 📧 Envía el archivo: GUIA_AMIGO.md"
echo "3. 📧 Envía el enlace: https://www.docker.com/products/docker-desktop/"
echo ""
echo "💡 Tu amigo solo necesita:"
echo "- Instalar Docker Desktop"
echo "- Extraer el ZIP"
echo "- Ejecutar run-docker.sh start"
echo "- Abrir http://localhost:8080"
echo "" 