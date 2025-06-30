<?php
require 'db.php'; // Aquí debe haber un $pdo con conexión PDO MySQL

$mensajeError = '';
$mensajeExito = '';

try {
    // Obtener mascotas disponibles (no vendidas)
    $stmtMascotas = $pdo->query("SELECT * FROM mascotas WHERE vendido = 0");
    $mascotasDisponibles = $stmtMascotas->fetchAll();

    // Obtener clientes ordenados alfabéticamente
    $stmtClientes = $pdo->query("SELECT * FROM clientes ORDER BY nombre ASC");
    $clientesRegistrados = $stmtClientes->fetchAll();

} catch (PDOException $e) {
    die("Error al cargar datos: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_mascota = isset($_POST['id_mascota']) ? (int)$_POST['id_mascota'] : 0;
    $id_cliente = isset($_POST['id_cliente']) ? (int)$_POST['id_cliente'] : 0;

    if (!$id_mascota || !$id_cliente) {
        $mensajeError = "⚠️ Debes seleccionar una mascota y un cliente.";
    } else {
        try {
            // Validar mascota y cliente
            $stmtM = $pdo->prepare("SELECT * FROM mascotas WHERE id = ?");
            $stmtM->execute([$id_mascota]);
            $mascota = $stmtM->fetch();

            $stmtC = $pdo->prepare("SELECT * FROM clientes WHERE id = ?");
            $stmtC->execute([$id_cliente]);
            $cliente = $stmtC->fetch();

            if (!$mascota || !$cliente) {
                $mensajeError = "⚠️ Mascota o cliente no válidos.";
            } elseif ($mascota['vendido']) {
                $mensajeError = "⚠️ Esta mascota ya ha sido vendida.";
            } else {
                // Iniciar transacción para asegurar integridad
                $pdo->beginTransaction();

                // Insertar venta (fecha_venta usa CURRENT_TIMESTAMP por defecto)
                $stmtVenta = $pdo->prepare("INSERT INTO ventas (id_mascota, id_cliente) VALUES (?, ?)");
                $stmtVenta->execute([$id_mascota, $id_cliente]);

                // Marcar mascota como vendida
                $stmtUpdate = $pdo->prepare("UPDATE mascotas SET vendido = 1 WHERE id = ?");
                $stmtUpdate->execute([$id_mascota]);

                $pdo->commit();

                header("Location: lista_ventas.php");
                exit;
            }
        } catch (PDOException $e) {
            $pdo->rollBack();
            $mensajeError = "❌ Error en la transacción: " . $e->getMessage();
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
    <div class="alert alert-danger"><?= htmlspecialchars($mensajeError) ?></div>
  <?php endif; ?>

  <form method="POST" class="mt-3">
    <div class="mb-3">
      <label>Mascota</label>
      <select name="id_mascota" class="form-select" required>
        <option value="" disabled selected>Selecciona una mascota</option>
        <?php foreach ($mascotasDisponibles as $m): ?>
          <option value="<?= $m['id'] ?>">
            <?= htmlspecialchars("{$m['nombre']} ({$m['especie']}) - $" . number_format($m['precio'], 2)) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="mb-3">
      <label>Cliente</label>
      <select name="id_cliente" class="form-select" required>
        <option value="" disabled selected>Selecciona un cliente</option>
        <?php foreach ($clientesRegistrados as $c): ?>
          <option value="<?= $c['id'] ?>">
            <?= htmlspecialchars($c['nombre'] . " (CP: {$c['codigo_postal']} )") ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <button type="submit" class="btn btn-success">Registrar Venta</button>
    <a href="lista_ventas.php" class="btn btn-secondary">Cancelar</a>
  </form>
</body>
</html>
