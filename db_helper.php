<?php
// Archivo: db_helper.php
// Helper para manejar consultas compatibles con PostgreSQL y MySQL

/**
 * Ejecuta una consulta preparada de manera compatible con PDO y MySQLi
 */
function executeQuery($conn, $sql, $params = []) {
    if ($conn instanceof PDO) {
        // Usar PDO para PostgreSQL
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    } else {
        // Usar MySQLi para MySQL
        $stmt = $conn->prepare($sql);
        if (!empty($params)) {
            $types = str_repeat('s', count($params)); // Asumimos strings por defecto
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        return $stmt;
    }
}

/**
 * Obtiene todos los resultados de una consulta
 */
function fetchAllResults($stmt) {
    if ($stmt instanceof PDOStatement) {
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}

/**
 * Obtiene un solo resultado de una consulta
 */
function fetchSingleResult($stmt) {
    if ($stmt instanceof PDOStatement) {
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        return $stmt->get_result()->fetch_assoc();
    }
}

/**
 * Obtiene el número de filas afectadas
 */
function getAffectedRows($stmt) {
    if ($stmt instanceof PDOStatement) {
        return $stmt->rowCount();
    } else {
        return $stmt->affected_rows;
    }
}

/**
 * Obtiene el último ID insertado
 */
function getLastInsertId($conn) {
    if ($conn instanceof PDO) {
        return $conn->lastInsertId();
    } else {
        return $conn->insert_id;
    }
}

/**
 * Cierra una declaración preparada
 */
function closeStatement($stmt) {
    if (!($stmt instanceof PDOStatement)) {
        $stmt->close();
    }
}

/**
 * Cierra una conexión de base de datos
 */
function closeConnection($conn) {
    if (!($conn instanceof PDO)) {
        $conn->close();
    }
}

/**
 * Verifica si hay resultados en una consulta
 */
function hasResults($stmt) {
    if ($stmt instanceof PDOStatement) {
        return $stmt->rowCount() > 0;
    } else {
        return $stmt->get_result()->num_rows > 0;
    }
}

/**
 * Ejecuta una consulta SELECT y retorna todos los resultados
 */
function selectAll($conn, $sql, $params = []) {
    $stmt = executeQuery($conn, $sql, $params);
    $results = fetchAllResults($stmt);
    closeStatement($stmt);
    return $results;
}

/**
 * Ejecuta una consulta SELECT y retorna un solo resultado
 */
function selectOne($conn, $sql, $params = []) {
    $stmt = executeQuery($conn, $sql, $params);
    $result = fetchSingleResult($stmt);
    closeStatement($stmt);
    return $result;
}

/**
 * Ejecuta una consulta INSERT, UPDATE o DELETE
 */
function executeUpdate($conn, $sql, $params = []) {
    $stmt = executeQuery($conn, $sql, $params);
    $affected = getAffectedRows($stmt);
    closeStatement($stmt);
    return $affected;
}

/**
 * Ejecuta una consulta INSERT y retorna el último ID
 */
function executeInsert($conn, $sql, $params = []) {
    $stmt = executeQuery($conn, $sql, $params);
    $lastId = getLastInsertId($conn);
    closeStatement($stmt);
    return $lastId;
}
?> 