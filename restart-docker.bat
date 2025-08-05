@echo off
echo ========================================
echo Reiniciando contenedores de Docker
echo ========================================

echo.
echo Deteniendo contenedores existentes...
docker-compose down

echo.
echo Limpiando contenedores y volúmenes...
docker system prune -f

echo.
echo Iniciando contenedores de Docker...
docker-compose up -d

echo.
echo Esperando a que los servicios estén listos...
timeout /t 10 /nobreak > nul

echo.
echo Verificando estado de los contenedores...
docker ps

echo.
echo ========================================
echo Docker reiniciado correctamente
echo ========================================
echo.
echo Accede a la aplicación en:
echo http://localhost:8080
echo.
echo Para ver los logs:
echo docker-compose logs -f
echo.
pause 