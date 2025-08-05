#!/bin/bash

# Script para ejecutar SleepBetter con Docker
# Autor: SleepBetter Team

echo "🛏️ SleepBetter - Docker Setup"
echo "================================"

# Verificar si Docker está instalado
if ! command -v docker &> /dev/null; then
    echo "❌ Docker no está instalado. Por favor instala Docker Desktop."
    exit 1
fi

# Verificar si Docker Compose está instalado
if ! command -v docker-compose &> /dev/null; then
    echo "❌ Docker Compose no está instalado. Por favor instala Docker Compose."
    exit 1
fi

echo "✅ Docker y Docker Compose están instalados"

# Función para mostrar el menú
show_menu() {
    echo ""
    echo "Selecciona una opción:"
    echo "1) 🚀 Iniciar SleepBetter"
    echo "2) 🛑 Detener SleepBetter"
    echo "3) 🔄 Reiniciar SleepBetter"
    echo "4) 📊 Ver logs"
    echo "5) 🗑️ Eliminar todo (incluyendo datos)"
    echo "6) 🔧 Construir imagen"
    echo "7) 📋 Ver estado"
    echo "8) 🆘 Ayuda"
    echo "9) ❌ Salir"
    echo ""
    read -p "Opción: " choice
}

# Función para iniciar
start_sleepbetter() {
    echo "🚀 Iniciando SleepBetter..."
    docker-compose up -d
    echo "✅ SleepBetter iniciado!"
    echo "🌐 Accede a: http://localhost:8080"
    echo "👤 Usuario: admin"
    echo "🔑 Contraseña: password"
}

# Función para detener
stop_sleepbetter() {
    echo "🛑 Deteniendo SleepBetter..."
    docker-compose down
    echo "✅ SleepBetter detenido!"
}

# Función para reiniciar
restart_sleepbetter() {
    echo "🔄 Reiniciando SleepBetter..."
    docker-compose restart
    echo "✅ SleepBetter reiniciado!"
}

# Función para ver logs
show_logs() {
    echo "📊 Mostrando logs..."
    docker-compose logs -f
}

# Función para eliminar todo
remove_all() {
    echo "⚠️ ADVERTENCIA: Esto eliminará todos los datos!"
    read -p "¿Estás seguro? (y/N): " confirm
    if [[ $confirm == [yY] ]]; then
        echo "🗑️ Eliminando todo..."
        docker-compose down -v
        docker system prune -f
        echo "✅ Todo eliminado!"
    else
        echo "❌ Operación cancelada"
    fi
}

# Función para construir
build_image() {
    echo "🔧 Construyendo imagen..."
    docker-compose build
    echo "✅ Imagen construida!"
}

# Función para ver estado
show_status() {
    echo "📋 Estado de los contenedores:"
    docker-compose ps
}

# Función para mostrar ayuda
show_help() {
    echo ""
    echo "🆘 AYUDA - SleepBetter Docker"
    echo "================================"
    echo ""
    echo "📋 Comandos disponibles:"
    echo "  ./run-docker.sh start    - Iniciar SleepBetter"
    echo "  ./run-docker.sh stop     - Detener SleepBetter"
    echo "  ./run-docker.sh restart  - Reiniciar SleepBetter"
    echo "  ./run-docker.sh logs     - Ver logs"
    echo "  ./run-docker.sh remove   - Eliminar todo"
    echo "  ./run-docker.sh build    - Construir imagen"
    echo "  ./run-docker.sh status   - Ver estado"
    echo ""
    echo "🌐 Acceso a la aplicación:"
    echo "  URL: http://localhost:8080"
    echo "  Usuario: admin"
    echo "  Contraseña: password"
    echo ""
    echo "🔧 Configuración de base de datos:"
echo "  Host: localhost"
echo "  Puerto: 3307"
echo "  Usuario: sleepbetter"
echo "  Contraseña: sleepbetter123"
    echo ""
}

# Procesar argumentos de línea de comandos
if [ $# -eq 0 ]; then
    # Modo interactivo
    while true; do
        show_menu
        case $choice in
            1) start_sleepbetter ;;
            2) stop_sleepbetter ;;
            3) restart_sleepbetter ;;
            4) show_logs ;;
            5) remove_all ;;
            6) build_image ;;
            7) show_status ;;
            8) show_help ;;
            9) echo "👋 ¡Hasta luego!"; exit 0 ;;
            *) echo "❌ Opción inválida" ;;
        esac
    done
else
    # Modo comando directo
    case $1 in
        start) start_sleepbetter ;;
        stop) stop_sleepbetter ;;
        restart) restart_sleepbetter ;;
        logs) show_logs ;;
        remove) remove_all ;;
        build) build_image ;;
        status) show_status ;;
        help) show_help ;;
        *) echo "❌ Comando inválido. Usa 'help' para ver opciones disponibles." ;;
    esac
fi 