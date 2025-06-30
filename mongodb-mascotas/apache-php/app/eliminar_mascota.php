<?php
require 'db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    die("ID inválido");
}

$mascota = $mascotas->findOne(['id' => $id]);

if (!$mascota) {
    die("Mascota no encontrada.");
}

// Eliminar las ventas relacionadas a esta mascota
$database->ventas->deleteMany(['id_mascota' => $id]);

// Eliminar la mascota
$mascotas->deleteOne(['id' => $id]);

header("Location: mascotas.php");
exit;
