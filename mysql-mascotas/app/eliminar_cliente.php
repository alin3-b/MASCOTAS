<?php
require 'db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    die("ID inválido.");
}

try {
    // Iniciar transacción para mantener integridad
    $pdo->beginTransaction();

    // 1. Buscar todas las ventas asociadas al cliente
    $stmtVentas = $pdo->prepare("SELECT id, id_mascota FROM ventas WHERE id_cliente = ?");
    $stmtVentas->execute([$id]);
    $ventas = $stmtVentas->fetchAll();

    // 2. Por cada venta, actualizar la mascota como no vendida y eliminar la venta
    $stmtUpdateMascota = $pdo->prepare("UPDATE mascotas SET vendido = FALSE WHERE id = ?");
    $stmtDeleteVenta = $pdo->prepare("DELETE FROM ventas WHERE id = ?");

    foreach ($ventas as $venta) {
        // Actualizar mascota vendido = FALSE
        $stmtUpdateMascota->execute([$venta['id_mascota']]);

        // Eliminar venta
        $stmtDeleteVenta->execute([$venta['id']]);
    }

    // 3. Finalmente eliminar el cliente
    $stmtDeleteCliente = $pdo->prepare("DELETE FROM clientes WHERE id = ?");
    $stmtDeleteCliente->execute([$id]);

    // Confirmar transacción
    $pdo->commit();

    header("Location: clientes.php");
    exit;

} catch (PDOException $e) {
    // En caso de error, revertir cambios
    $pdo->rollBack();
    die("Error al eliminar cliente: " . $e->getMessage());
}
?>
