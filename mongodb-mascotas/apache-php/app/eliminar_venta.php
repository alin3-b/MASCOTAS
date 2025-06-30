<?php
require 'db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    die("ID inválido.");
}

$venta = $ventas->findOne(['id' => $id]);

if ($venta) {
    // Marcar mascota como no vendida
    $mascotas->updateOne(
        ['id' => $venta['id_mascota']],
        ['$set' => ['vendido' => false]]
    );

    // Eliminar la venta
    $ventas->deleteOne(['id' => $id]);
}

header("Location: lista_ventas.php");
exit;
