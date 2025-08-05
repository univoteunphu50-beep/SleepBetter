-- Script para actualizar la tabla productos con las nuevas columnas
-- Ejecutar este script si la tabla ya existe pero faltan las nuevas columnas

-- Agregar columna con_lote si no existe
ALTER TABLE productos ADD COLUMN IF NOT EXISTS con_lote BOOLEAN DEFAULT 0;

-- Agregar columna numero_lote si no existe
ALTER TABLE productos ADD COLUMN IF NOT EXISTS numero_lote VARCHAR(50);

-- Agregar columna fecha_fabricacion si no existe
ALTER TABLE productos ADD COLUMN IF NOT EXISTS fecha_fabricacion DATE;

-- Agregar columna fecha_expiracion si no existe (puede que ya exista como 'expiracion')
ALTER TABLE productos ADD COLUMN IF NOT EXISTS fecha_expiracion DATE;

-- Agregar columna temperatura_almacenamiento si no existe
ALTER TABLE productos ADD COLUMN IF NOT EXISTS temperatura_almacenamiento VARCHAR(100);

-- Agregar columna condiciones_especiales si no existe
ALTER TABLE productos ADD COLUMN IF NOT EXISTS condiciones_especiales TEXT;

-- Renombrar columna 'expiracion' a 'fecha_expiracion' si existe
-- (MySQL no permite renombrar directamente, así que creamos una nueva y copiamos datos)
-- Primero verificamos si existe la columna expiracion
-- Si existe, copiamos los datos a fecha_expiracion y eliminamos la antigua

-- Verificar si existe la columna 'lote' y renombrarla a 'numero_lote' si es necesario
-- (Esto se puede hacer manualmente si es necesario)

-- Comentarios sobre las columnas:
-- con_lote: BOOLEAN - Indica si el producto tiene control de lotes
-- numero_lote: VARCHAR(50) - Número del lote del producto
-- fecha_fabricacion: DATE - Fecha de fabricación del lote
-- fecha_expiracion: DATE - Fecha de expiración del lote
-- temperatura_almacenamiento: VARCHAR(100) - Temperatura requerida para almacenamiento
-- condiciones_especiales: TEXT - Condiciones especiales de almacenamiento o manejo 