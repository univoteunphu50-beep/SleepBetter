@echo off
REM Script de deploy para SleepBetter en Render (Windows)

echo 🚀 Preparando deploy de SleepBetter en Render...

REM Verificar que estamos en el directorio correcto
if not exist "Dockerfile" (
    echo ❌ Error: No se encontró Dockerfile. Asegúrate de estar en el directorio raíz del proyecto.
    pause
    exit /b 1
)

REM Verificar archivos necesarios
echo 📋 Verificando archivos necesarios...
if not exist "docker-entrypoint.sh" (
    echo ❌ Error: Falta el archivo docker-entrypoint.sh
    pause
    exit /b 1
)
if not exist "render.yaml" (
    echo ❌ Error: Falta el archivo render.yaml
    pause
    exit /b 1
)
if not exist "sleepbetter_db.sql" (
    echo ❌ Error: Falta el archivo sleepbetter_db.sql
    pause
    exit /b 1
)
echo ✅ Todos los archivos necesarios están presentes

REM Verificar si git está configurado
if not exist ".git" (
    echo ⚠️  Advertencia: No se detectó un repositorio Git
    echo    Para hacer deploy en Render, necesitas subir tu código a un repositorio Git
    echo    Puedes usar GitHub, GitLab, o Bitbucket
) else (
    echo ✅ Repositorio Git detectado
    
    REM Verificar estado de git
    git status --porcelain >nul 2>&1
    if %errorlevel% neq 0 (
        echo 📝 Hay cambios sin commitear. ¿Quieres hacer commit?
        set /p "continue=¿Continuar? (y/n): "
        if /i "%continue%"=="y" (
            git add .
            git commit -m "Preparación para deploy en Render"
            echo ✅ Cambios commiteados
        )
    ) else (
        echo ✅ No hay cambios pendientes
    )
)

echo.
echo 🎯 Próximos pasos para el deploy:
echo.
echo 1. 📤 Sube tu código a un repositorio Git (GitHub, GitLab, etc.)
echo 2. 🌐 Ve a https://dashboard.render.com
echo 3. ➕ Haz clic en 'New +' y selecciona 'Blueprint'
echo 4. 🔗 Conecta tu repositorio Git
echo 5. ⚙️  Render detectará automáticamente el archivo render.yaml
echo 6. 🚀 Haz clic en 'Apply' para iniciar el deploy
echo.
echo 📚 Para más información, consulta README_DEPLOY.md
echo.
echo ✅ Script de preparación completado!
pause 