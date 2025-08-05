
<?php
include 'db.php';
header('Content-Type: application/json');

$result = $conn->query("SELECT * FROM productos WHERE stock <= restock");
$alertas = [];
while ($row = $result->fetch_assoc()) {
    $alertas[] = $row;
}
echo json_encode($alertas);
?>
