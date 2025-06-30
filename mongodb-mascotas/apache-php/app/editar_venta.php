<?php
require 'db.php';
use MongoDB\BSON\UTCDateTime;

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$venta = $ventas->findOne(['id' => $id]);

if (!$venta) {
    die("⚠️ Venta no encontrada.");
}

$mensajeError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idMascota = (int)($_POST['id_mascota'] ?? 0);
    $idCliente = (int)($_POST['id_cliente'] ?? 0);

    $mascota = $mascotas->findOne(['id' => $idMascota]);
    $cliente = $clientes->findOne(['id' => $idCliente]);

    if (!$mascota || !$cliente) {
        $mensajeError = "⚠️ Mascota o cliente no válidos.";
    } else {
        $ventas->updateOne(
            ['id' => $id],
            ['$set' => [
                'id_mascota' => $idMascota,
                'id_cliente' => $idCliente,
                'fecha_venta' => new UTCDateTime() // actualiza fecha
            ]]
        );

        // No cambiamos el estado de vendido aquí porque la mascota ya está vendida
        header("Location: lista_ventas.php");
        exit;
    }
}

// Obtener datos para mostrar en el formulario
$clientesLista = $clientes->find();

$mascotaVenta = $mascotas->findOne(['id' => $venta['id_mascota']]);
$nombreMascota = $mascotaVenta ? $mascotaVenta['nombre'] : '';
$especieMascota = $mascotaVenta ? $mascotaVenta['especie'] : '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Editar Venta</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="container mt-4">
  <h1>Editar Venta</h1>

  <?php if ($mensajeError): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($mensajeError) ?></div>
  <?php endif; ?>

  <form method="POST" novalidate>
    <div class="mb-3">
      <label>Mascota</label>
      <input type="text" class="form-control" disabled
             value="<?= htmlspecialchars($nombreMascota . " (" . $especieMascota . ")") ?>">
      <input type="hidden" name="id_mascota" value="<?= htmlspecialchars($venta['id_mascota']) ?>">
    </div>

    <div class="mb-3">
      <label>Cliente</label>
      <select name="id_cliente" class="form-select" required>
        <?php foreach ($clientesLista as $c): ?>
          <option value="<?= htmlspecialchars($c['id']) ?>" <?= $venta['id_cliente'] == $c['id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($c['nombre']) ?> (CP: <?= htmlspecialchars($c['codigo_postal']) ?>)
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <button type="submit" class="btn btn-success">Actualizar Venta</button>
    <a href="lista_ventas.php" class="btn btn-secondary">Cancelar</a>
  </form>
</body>
</html>
