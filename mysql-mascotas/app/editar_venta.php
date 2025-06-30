<?php
require 'db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    die("⚠️ ID de venta inválido.");
}

// Obtener la venta actual
$stmt = $pdo->prepare("SELECT * FROM ventas WHERE id = ?");
$stmt->execute([$id]);
$venta = $stmt->fetch();

if (!$venta) {
    die("⚠️ Venta no encontrada.");
}

$mensajeError = '';

// Procesar actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idMascota = (int)($_POST['id_mascota'] ?? 0);  // No editable, solo enviado como hidden
    $idCliente = (int)($_POST['id_cliente'] ?? 0);

    // Verificar existencia de mascota y cliente
    $stmtM = $pdo->prepare("SELECT * FROM mascotas WHERE id = ?");
    $stmtM->execute([$idMascota]);
    $mascota = $stmtM->fetch();

    $stmtC = $pdo->prepare("SELECT * FROM clientes WHERE id = ?");
    $stmtC->execute([$idCliente]);
    $cliente = $stmtC->fetch();

    if (!$mascota || !$cliente) {
        $mensajeError = "⚠️ Mascota o cliente no válidos.";
    } else {
        $stmt = $pdo->prepare("UPDATE ventas SET id_mascota = ?, id_cliente = ?, fecha_venta = NOW() WHERE id = ?");
        $stmt->execute([$idMascota, $idCliente, $id]);

        header("Location: lista_ventas.php");
        exit;
    }
}

// Obtener lista de clientes para el <select>
$stmtClientes = $pdo->query("SELECT id, nombre, codigo_postal FROM clientes");
$clientesLista = $stmtClientes->fetchAll();

// Obtener datos de la mascota actual
$stmtMascota = $pdo->prepare("SELECT nombre, especie FROM mascotas WHERE id = ?");
$stmtMascota->execute([$venta['id_mascota']]);
$mascotaActual = $stmtMascota->fetch();
$nombreMascota = $mascotaActual['nombre'] ?? '';
$especieMascota = $mascotaActual['especie'] ?? '';
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
             value="<?= htmlspecialchars("{$nombreMascota} ({$especieMascota})") ?>">
      <input type="hidden" name="id_mascota" value="<?= htmlspecialchars($venta['id_mascota']) ?>">
    </div>

    <div class="mb-3">
      <label>Cliente</label>
      <select name="id_cliente" class="form-select" required>
        <?php foreach ($clientesLista as $c): ?>
          <option value="<?= $c['id'] ?>" <?= $venta['id_cliente'] == $c['id'] ? 'selected' : '' ?>>
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
