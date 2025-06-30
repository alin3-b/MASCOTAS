<?php
require 'db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    die("ID inválido.");
}

$cliente = $clientes->findOne(['id' => $id]);

if (!$cliente) {
    die("Cliente no encontrado.");
}

// Buscar ventas asociadas al cliente
$ventasCliente = $ventas->find(['id_cliente' => $id]);

foreach ($ventasCliente as $venta) {
    // Marcar mascota como no vendida
    $mascotas->updateOne(
        ['id' => $venta['id_mascota']],
        ['$set' => ['vendido' => false]]
    );

    // Eliminar la venta
    $ventas->deleteOne(['id' => $venta['id']]);
}

// Finalmente eliminar el cliente
$clientes->deleteOne(['id' => $id]);

header("Location: clientes.php");
exit;
?>
