#!/bin/bash

# Script de inicio para SleepBetter Docker - Optimizado para Render

echo "🚀 Iniciando SleepBetter en Render..."

# Configurar permisos
echo "🔧 Configurando permisos..."
chown -R www-data:www-data /var/www/html
chmod -R 755 /var/www/html

# Crear directorios si no existen
mkdir -p /var/www/html/uploads /var/www/html/logs
chown -R www-data:www-data /var/www/html/uploads /var/www/html/logs

# Configurar variables de entorno para Render
if [ -n "$PORT" ]; then
    echo "🌐 Puerto asignado por Render: $PORT"
    # Configurar Apache para escuchar en el puerto asignado
    sed -i "s/Listen 80/Listen $PORT/" /etc/apache2/ports.conf
    sed -i "s/:80/:$PORT/" /etc/apache2/sites-available/000-default.conf
fi

# Verificar conexión a la base de datos
echo "🔍 Verificando conexión a la base de datos..."
if [ -n "$MYSQL_HOST" ]; then
    echo "✅ Variables de base de datos configuradas:"
    echo "   Host: $MYSQL_HOST"
    echo "   Puerto: $MYSQL_PORT"
    echo "   Base de datos: $MYSQL_DATABASE"
    echo "   Usuario: $MYSQL_USER"
else
    echo "⚠️  Variables de base de datos no configuradas"
fi

echo "✅ SleepBetter está listo para Render!"
echo "🌐 La aplicación estará disponible en el puerto asignado por Render"

# Ejecutar el comando original
exec "$@" 