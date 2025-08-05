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

# Detectar tipo de base de datos
if [ -n "$DB_HOST" ]; then
    echo "✅ Variables de PostgreSQL configuradas:"
    echo "   Host: $DB_HOST"
    echo "   Puerto: $DB_PORT"
    echo "   Base de datos: $DB_NAME"
    echo "   Usuario: $DB_USER"
    DB_TYPE="postgresql"
    
    # Ejecutar script de inicialización de base de datos
    echo "🔧 Inicializando base de datos PostgreSQL..."
    php /var/www/html/init_database.php
    
elif [ -n "$MYSQL_HOST" ]; then
    echo "✅ Variables de MySQL configuradas:"
    echo "   Host: $MYSQL_HOST"
    echo "   Puerto: $MYSQL_PORT"
    echo "   Base de datos: $MYSQL_DATABASE"
    echo "   Usuario: $MYSQL_USER"
    DB_TYPE="mysql"
else
    echo "⚠️  Variables de base de datos no configuradas"
    DB_TYPE="local"
fi

echo "📊 Tipo de base de datos detectado: $DB_TYPE"

# Crear archivo de configuración de base de datos si es necesario
if [ "$DB_TYPE" = "postgresql" ]; then
    echo "🔧 Configurando para PostgreSQL..."
    # Aquí podrías agregar lógica para inicializar la base de datos PostgreSQL
elif [ "$DB_TYPE" = "mysql" ]; then
    echo "🔧 Configurando para MySQL..."
    # Aquí podrías agregar lógica para inicializar la base de datos MySQL
fi

echo "✅ SleepBetter está listo para Render!"
echo "🌐 La aplicación estará disponible en el puerto asignado por Render"

# Ejecutar el comando original
exec "$@" 