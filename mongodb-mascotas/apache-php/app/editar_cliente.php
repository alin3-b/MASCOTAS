<?php
require 'db.php';

use MongoDB\BSON\ObjectId;

$mensajeError = '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    die("ID inválido.");
}

// Buscar cliente actual
$cliente = $clientes->findOne(['id' => $id]);

if (!$cliente) {
    die("Cliente no encontrado.");
}

// Procesar actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $codigoPostal = $_POST['codigo_postal'] ?? '';

    // Validaciones
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
        // Validar que no haya otro cliente con el mismo email
        $existe = $clientes->findOne([
            'email' => $email,
            'id' => ['$ne' => $id]
        ]);

        if ($existe) {
            $mensajeError = "⚠️ Ya existe otro cliente con ese correo electrónico.";
        } else {
            // Actualizar
            $clientes->updateOne(
                ['id' => $id],
                ['$set' => [
                    'nombre' => $nombre,
                    'email' => $email,
                    'codigo_postal' => (int)$codigoPostal
                ]]
            );

            header("Location: clientes.php");
            exit;
        }
    }
}

// Valores actuales para mostrar en el formulario
$nombreVal = $_POST['nombre'] ?? $cliente['nombre'];
$emailVal = $_POST['email'] ?? $cliente['email'];
$cpVal = $_POST['codigo_postal'] ?? $cliente['codigo_postal'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar Cliente</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">
  <h1>Editar Cliente</h1>

  <?php if ($mensajeError !== ''): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($mensajeError) ?></div>
  <?php endif; ?>

  <form method="POST" class="mt-3">
    <div class="mb-3">
      <label>Nombre</label>
      <input type="text" name="nombre" class="form-control" required maxlength="100" value="<?= htmlspecialchars($nombreVal) ?>">
    </div>
    <div class="mb-3">
      <label>Email</label>
      <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($emailVal) ?>">
    </div>
    <div class="mb-3">
      <label>Código Postal</label>
      <input type="number" name="codigo_postal" class="form-control" required min="10000" max="99999" value="<?= htmlspecialchars($cpVal) ?>">
    </div>
    <button type="submit" class="btn btn-primary">Actualizar</button>
    <a href="clientes.php" class="btn btn-secondary">Cancelar</a>
  </form>
</body>
</html>
