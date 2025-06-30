<?php
require 'db.php';

$mensajeError = '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    die("ID inválido.");
}

// Buscar la mascota
$stmt = $pdo->prepare("SELECT * FROM mascotas WHERE id = ?");
$stmt->execute([$id]);
$mascota = $stmt->fetch();

if (!$mascota) {
    die("Mascota no encontrada.");
}

// Procesar actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $especie = $_POST['especie'] ?? '';
    $fechaNacInput = $_POST['fecha_nac'] ?? '';
    $precioInput = $_POST['precio'] ?? '';

    if ($nombre === '' || $especie === '' || $fechaNacInput === '' || $precioInput === '') {
        $mensajeError = "⚠️ Todos los campos son obligatorios.";
    } elseif (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u", $nombre)) {
        $mensajeError = "⚠️ El nombre solo debe contener letras y espacios.";
    } elseif (strlen($nombre) > 50) {
        $mensajeError = "⚠️ El nombre no debe tener más de 50 caracteres.";
    } elseif (!is_numeric($precioInput) || $precioInput < 300) {
        $mensajeError = "⚠️ El precio debe ser un número igual o mayor a 300.";
    } else {
        $especiesValidas = ["Perro", "Gato", "Pez", "Conejo", "Tortuga", "Hamster"];
        if (!in_array($especie, $especiesValidas)) {
            $mensajeError = "⚠️ Especie no válida.";
        } else {
            try {
                $fechaNac = new DateTime($fechaNacInput);
                $hoy = new DateTime();

                if ($fechaNac > $hoy) {
                    $mensajeError = "⚠️ La fecha de nacimiento no puede ser posterior a hoy.";
                } else {
                    // Verificar duplicado
                    $verificar = $pdo->prepare("SELECT COUNT(*) FROM mascotas WHERE nombre = ? AND especie = ? AND id != ?");
                    $verificar->execute([$nombre, $especie, $id]);
                    if ($verificar->fetchColumn() > 0) {
                        $mensajeError = "⚠️ Ya existe otra mascota con ese nombre y especie.";
                    } else {
                        $edad = $hoy->diff($fechaNac)->y;
                        $stmtUpdate = $pdo->prepare("UPDATE mascotas SET nombre = ?, especie = ?, edad = ?, fecha_nac = ?, precio = ? WHERE id = ?");
                        $stmtUpdate->execute([$nombre, $especie, $edad, $fechaNac->format('Y-m-d'), $precioInput, $id]);

                        header("Location: mascotas.php");
                        exit;
                    }
                }
            } catch (Exception $e) {
                $mensajeError = "⚠️ Fecha inválida.";
            }
        }
    }
}

// Valores para el formulario
$nombreVal = $_POST['nombre'] ?? $mascota['nombre'];
$especieVal = $_POST['especie'] ?? $mascota['especie'];
$fechaNacVal = $_POST['fecha_nac'] ?? $mascota['fecha_nac'];
$precioVal = $_POST['precio'] ?? $mascota['precio'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar Mascota</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">
  <h1>Editar Mascota</h1>

  <?php if ($mensajeError !== ''): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($mensajeError) ?></div>
  <?php endif; ?>

  <form method="POST" class="mt-3" novalidate>
    <div class="mb-3">
      <label>Nombre</label>
      <input type="text" name="nombre" class="form-control" required maxlength="50" value="<?= htmlspecialchars($nombreVal) ?>">
    </div>
    <div class="mb-3">
      <label>Especie</label>
      <select name="especie" class="form-select" required>
        <option value="" disabled>Selecciona una especie</option>
        <?php
        foreach (["Perro", "Gato", "Pez", "Conejo", "Tortuga", "Hamster"] as $esp) {
            $selected = ($esp === $especieVal) ? 'selected' : '';
            echo "<option value=\"$esp\" $selected>$esp</option>";
        }
        ?>
      </select>
    </div>
    <div class="mb-3">
      <label>Fecha de Nacimiento</label>
      <input type="date" name="fecha_nac" class="form-control" required max="<?= date('Y-m-d') ?>" value="<?= htmlspecialchars($fechaNacVal) ?>">
    </div>
    <div class="mb-3">
      <label>Precio (MXN) - mínimo 300</label>
      <input type="number" step="0.01" min="300" name="precio" class="form-control" required value="<?= htmlspecialchars($precioVal) ?>">
    </div>
    <button type="submit" class="btn btn-primary">Actualizar</button>
    <a href="mascotas.php" class="btn btn-secondary">Cancelar</a>
  </form>
</body>
</html>
