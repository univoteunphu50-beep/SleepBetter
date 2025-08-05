#!/bin/bash

# Script de deploy para SleepBetter en Render

echo "🚀 Preparando deploy de SleepBetter en Render..."

# Verificar que estamos en el directorio correcto
if [ ! -f "Dockerfile" ]; then
    echo "❌ Error: No se encontró Dockerfile. Asegúrate de estar en el directorio raíz del proyecto."
    exit 1
fi

# Verificar archivos necesarios
echo "📋 Verificando archivos necesarios..."
required_files=("Dockerfile" "docker-entrypoint.sh" "render.yaml" "sleepbetter_db.sql")
for file in "${required_files[@]}"; do
    if [ ! -f "$file" ]; then
        echo "❌ Error: Falta el archivo $file"
        exit 1
    fi
done
echo "✅ Todos los archivos necesarios están presentes"

# Verificar si git está configurado
if [ ! -d ".git" ]; then
    echo "⚠️  Advertencia: No se detectó un repositorio Git"
    echo "   Para hacer deploy en Render, necesitas subir tu código a un repositorio Git"
    echo "   Puedes usar GitHub, GitLab, o Bitbucket"
else
    echo "✅ Repositorio Git detectado"
    
    # Verificar estado de git
    if [ -n "$(git status --porcelain)" ]; then
        echo "📝 Hay cambios sin commitear. ¿Quieres hacer commit?"
        read -p "¿Continuar? (y/n): " -n 1 -r
        echo
        if [[ $REPLY =~ ^[Yy]$ ]]; then
            git add .
            git commit -m "Preparación para deploy en Render"
            echo "✅ Cambios commiteados"
        fi
    else
        echo "✅ No hay cambios pendientes"
    fi
fi

echo ""
echo "🎯 Próximos pasos para el deploy:"
echo ""
echo "1. 📤 Sube tu código a un repositorio Git (GitHub, GitLab, etc.)"
echo "2. 🌐 Ve a https://dashboard.render.com"
echo "3. ➕ Haz clic en 'New +' y selecciona 'Blueprint'"
echo "4. 🔗 Conecta tu repositorio Git"
echo "5. ⚙️  Render detectará automáticamente el archivo render.yaml"
echo "6. 🚀 Haz clic en 'Apply' para iniciar el deploy"
echo ""
echo "📚 Para más información, consulta README_DEPLOY.md"
echo ""
echo "✅ Script de preparación completado!" 