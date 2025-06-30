<?php
require 'db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    die("ID inválido.");
}

// Buscar la venta y obtener el id de la mascota
$stmt = $pdo->prepare("SELECT id_mascota FROM ventas WHERE id = ?");
$stmt->execute([$id]);
$venta = $stmt->fetch(PDO::FETCH_ASSOC);

if ($venta) {
    // Marcar mascota como no vendida
    $stmtUpdate = $pdo->prepare("UPDATE mascotas SET vendido = 0 WHERE id = ?");
    $stmtUpdate->execute([$venta['id_mascota']]);

    // Eliminar la venta
    $stmtDelete = $pdo->prepare("DELETE FROM ventas WHERE id = ?");
    $stmtDelete->execute([$id]);
}

header("Location: lista_ventas.php");
exit;
