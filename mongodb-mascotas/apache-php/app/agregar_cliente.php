<?php
require 'db.php';
use MongoDB\Operation\FindOneAndUpdate;

$mensajeError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $codigoPostal = $_POST['codigo_postal'] ?? '';

    if ($nombre === '' || $email === '' || $codigoPostal === '') {
        $mensajeError = "⚠️ Todos los campos son obligatorios.";
    } elseif (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u", $nombre)) {
        $mensajeError = "⚠️ El nombre solo puede contener letras y espacios.";
    } elseif (strlen($nombre) > 100) {
        $mensajeError = "⚠️ El nombre no debe tener más de 100 caracteres.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensajeError = "⚠️ Email no válido.";
    } elseif (!preg_match('/^\d{5}$/', $codigoPostal)) {
        $mensajeError = "⚠️ El código postal debe tener exactamente 5 dígitos.";
    } else {
        // Verificar unicidad del email
        $existe = $clientes->findOne(['email' => $email]);
        if ($existe) {
            $mensajeError = "⚠️ Ya existe un cliente con ese correo electrónico.";
        } else {
            // Obtener nuevo ID autoincremental
            $contador = $database->contadores->findOneAndUpdate(
                ['_id' => 'cliente_id'],
                ['$inc' => ['seq' => 1]],
                [
                    'upsert' => true,
                    'returnDocument' => FindOneAndUpdate::RETURN_DOCUMENT_AFTER
                ]
            );
            $idCliente = $contador->seq;

            // Insertar nuevo cliente
            $clientes->insertOne([
                'id' => $idCliente,
                'nombre' => $nombre,
                'email' => $email,
                'codigo_postal' => (int)$codigoPostal
            ]);

            header('Location: clientes.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Agregar Cliente</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">
  <h1>Agregar nuevo cliente</h1>

  <?php if ($mensajeError !== ''): ?>
    <div class="alert alert-danger"><?= $mensajeError ?></div>
  <?php endif; ?>

  <form method="POST" class="mt-3">
    <div class="mb-3">
      <label>Nombre</label>
      <input type="text" name="nombre" class="form-control" required maxlength="100" value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>">
    </div>
    <div class="mb-3">
      <label>Email</label>
      <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
    </div>
    <div class="mb-3">
      <label>Código Postal</label>
      <input type="number" name="codigo_postal" class="form-control" required min="10000" max="99999" value="<?= htmlspecialchars($_POST['codigo_postal'] ?? '') ?>">
    </div>
    <button type="submit" class="btn btn-success">Guardar</button>
    <a href="clientes.php" class="btn btn-secondary">Cancelar</a>
  </form>
</body>
</html>
