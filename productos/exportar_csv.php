
<?php
include 'db.php';
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="productos.csv"');

$output = fopen("php://output", "w");
fputcsv($output, ["ID", "Nombre", "Stock", "Restock", "Precio", "Costo", "Palabras clave"]);

$result = $conn->query("SELECT id, nombre, stock, restock, precio, costo, palabras_clave FROM productos");
while ($row = $result->fetch_assoc()) {
    fputcsv($output, $row);
}
fclose($output);
exit();
?>
