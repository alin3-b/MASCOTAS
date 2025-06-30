<?php
require 'db.php';

$mensajeError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $especie = $_POST['especie'] ?? '';
    $fechaNacInput = $_POST['fecha_nac'] ?? '';
    $precioInput = $_POST['precio'] ?? '';

    if ($nombre === '' || $especie === '' || $fechaNacInput === '' || $precioInput === '') {
        $mensajeError = "⚠️ Todos los campos son obligatorios.";
    } elseif (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u", $nombre)) {
        $mensajeError = "⚠️ El nombre solo puede contener letras y espacios.";
    } elseif (strlen($nombre) > 50) {
        $mensajeError = "⚠️ El nombre no debe tener más de 50 caracteres.";
    } elseif (!is_numeric($precioInput) || $precioInput < 300) {
        $mensajeError = "⚠️ El precio debe ser un número igual o mayor a 300 pesos.";
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
                    $check = $pdo->prepare("SELECT COUNT(*) FROM mascotas WHERE nombre = ? AND especie = ?");
                    $check->execute([$nombre, $especie]);
                    if ($check->fetchColumn() > 0) {
                        $mensajeError = "⚠️ Ya existe una mascota registrada con ese nombre y especie.";
                    } else {
                        $edad = $hoy->diff($fechaNac)->y;
                        $stmt = $pdo->prepare("INSERT INTO mascotas (nombre, especie, edad, fecha_nac, precio, vendido)
                                               VALUES (?, ?, ?, ?, ?, FALSE)");
                        $stmt->execute([$nombre, $especie, $edad, $fechaNac->format('Y-m-d'), $precioInput]);

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

$precioVal = $_POST['precio'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Agregar Mascota</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="container mt-4">
  <h1>Agregar nueva mascota</h1>

  <?php if ($mensajeError !== ''): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($mensajeError) ?></div>
  <?php endif; ?>

  <form method="POST" class="mt-3" novalidate>
    <div class="mb-3">
      <label>Nombre</label>
      <input type="text" name="nombre" class="form-control" required maxlength="50" value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>">
    </div>
    <div class="mb-3">
      <label>Especie</label>
      <select name="especie" class="form-select" required>
        <option value="" disabled <?= empty($_POST['especie']) ? 'selected' : '' ?>>Selecciona una especie</option>
        <?php
        $especiesValidas = ["Perro", "Gato", "Pez", "Conejo", "Tortuga", "Hamster"];
        foreach ($especiesValidas as $esp) {
            $selected = (($_POST['especie'] ?? '') === $esp) ? 'selected' : '';
            echo "<option value=\"$esp\" $selected>$esp</option>";
        }
        ?>
      </select>
    </div>
    <div class="mb-3">
      <label>Fecha de Nacimiento</label>
      <input type="date" name="fecha_nac" class="form-control" required max="<?= date('Y-m-d') ?>" value="<?= htmlspecialchars($_POST['fecha_nac'] ?? '') ?>">
    </div>
    <div class="mb-3">
      <label>Precio (MXN) - mínimo 300</label>
      <input type="number" step="0.01" min="300" name="precio" class="form-control" required value="<?= htmlspecialchars($precioVal) ?>">
    </div>
    <button type="submit" class="btn btn-success">Guardar</button>
    <a href="mascotas.php" class="btn btn-secondary">Cancelar</a>
  </form>
</body>
</html>

