<?php
require 'db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    die("ID inválido");
}

// Verificar si la mascota existe
$stmt = $pdo->prepare("SELECT * FROM mascotas WHERE id = ?");
$stmt->execute([$id]);
$mascota = $stmt->fetch();

if (!$mascota) {
    die("Mascota no encontrada.");
}

// Eliminar ventas relacionadas
$stmtVentas = $pdo->prepare("DELETE FROM ventas WHERE id_mascota = ?");
$stmtVentas->execute([$id]);

// Eliminar mascota
$stmtEliminar = $pdo->prepare("DELETE FROM mascotas WHERE id = ?");
$stmtEliminar->execute([$id]);

header("Location: mascotas.php");
exit;
