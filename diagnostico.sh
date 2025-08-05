#!/bin/bash

echo "🔍 Diagnóstico de SleepBetter Docker"
echo "====================================="

echo ""
echo "📋 Verificando Docker..."
if ! command -v docker &> /dev/null; then
    echo "❌ Docker no está instalado o no está en el PATH"
    echo "💡 Instala Docker Desktop desde: https://www.docker.com/products/docker-desktop/"
    exit 1
fi

echo "✅ Docker encontrado"
docker --version

echo ""
echo "📋 Verificando Docker Compose..."
if ! command -v docker-compose &> /dev/null; then
    echo "❌ Docker Compose no está disponible"
    exit 1
fi

echo "✅ Docker Compose encontrado"
docker-compose --version

echo ""
echo "📋 Verificando estado de contenedores..."
docker-compose ps

echo ""
echo "📋 Verificando logs del contenedor web..."
docker-compose logs --tail=10 web

echo ""
echo "📋 Verificando conectividad..."
if curl -s http://localhost:8080 >/dev/null 2>&1; then
    echo "✅ La aplicación responde en http://localhost:8080"
else
    echo "❌ La aplicación no responde en http://localhost:8080"
    echo "💡 Intenta: docker-compose down -v && docker-compose up -d"
fi

echo ""
echo "📋 Verificando puertos en uso..."
netstat -an | grep :8080 2>/dev/null || echo "Puerto 8080 no está en uso"

echo ""
echo "🎯 URLs para probar:"
echo "- http://localhost:8080"
echo "- http://localhost:8080/login.php"
echo ""
echo "🔑 Credenciales:"
echo "- Usuario: admin"
echo "- Contraseña: password"
echo ""
echo "💡 Si hay problemas:"
echo "1. docker-compose down -v"
echo "2. docker-compose up -d"
echo "3. Accede a http://localhost:8080"
echo "" 