<?php
require 'db.php';
use MongoDB\BSON\UTCDateTime;
use MongoDB\Operation\FindOneAndUpdate;

$mensajeError = '';
$mensajeExito = '';

// Obtener mascotas no vendidas y clientes
$mascotasDisponibles = $mascotas->find(['vendido' => ['$ne' => true]]);
$clientesRegistrados = $clientes->find();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_mascota = isset($_POST['id_mascota']) ? (int)$_POST['id_mascota'] : 0;
    $id_cliente = isset($_POST['id_cliente']) ? (int)$_POST['id_cliente'] : 0;

    if (!$id_mascota || !$id_cliente) {
        $mensajeError = "⚠️ Debes seleccionar una mascota y un cliente.";
    } else {
        $mascota = $mascotas->findOne(['id' => $id_mascota]);
        $cliente = $clientes->findOne(['id' => $id_cliente]);

        if (!$mascota || !$cliente) {
            $mensajeError = "⚠️ Mascota o cliente no válidos.";
        } elseif (isset($mascota['vendido']) && $mascota['vendido']) {
            $mensajeError = "⚠️ Esta mascota ya ha sido vendida.";
        } else {
            // Generar ID autoincremental para venta
            $contador = $database->contadores->findOneAndUpdate(
                ['_id' => 'venta_id'],
                ['$inc' => ['seq' => 1]],
                ['upsert' => true, 'returnDocument' => FindOneAndUpdate::RETURN_DOCUMENT_AFTER]
            );
            $idVenta = $contador->seq;

            // Insertar venta
            $ventas->insertOne([
                'id' => $idVenta,
                'id_mascota' => $id_mascota,
                'id_cliente' => $id_cliente,
                'fecha_venta' => new UTCDateTime()
            ]);

            // Marcar mascota como vendida
            $mascotas->updateOne(
                ['id' => $id_mascota],
                ['$set' => ['vendido' => true]]
            );

            header("Location: lista_ventas.php");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Registrar Venta</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="container mt-4">
  <h1>Registrar Nueva Venta</h1>

  <?php if ($mensajeError): ?>
    <div class="alert alert-danger"><?= $mensajeError ?></div>
  <?php endif; ?>

  <form method="POST" class="mt-3">
    <div class="mb-3">
      <label>Mascota</label>
      <select name="id_mascota" class="form-select" required>
        <option value="" disabled selected>Selecciona una mascota</option>
        <?php foreach ($mascotasDisponibles as $m): ?>
          <option value="<?= $m['id'] ?>">
            <?= "{$m['nombre']} ({$m['especie']}) - $".number_format($m['precio'], 2) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="mb-3">
      <label>Cliente</label>
      <select name="id_cliente" class="form-select" required>
        <option value="" disabled selected>Selecciona un cliente</option>
        <?php foreach ($clientesRegistrados as $c): ?>
          <option value="<?= $c['id'] ?>"><?= $c['nombre'] ?> (CP: <?= $c['codigo_postal'] ?>)</option>
        <?php endforeach; ?>
      </select>
    </div>
    <button type="submit" class="btn btn-success">Registrar Venta</button>
    <a href="lista_ventas.php" class="btn btn-secondary">Cancelar</a>
  </form>
</body>
</html>
