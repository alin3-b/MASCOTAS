<?php
require 'db.php';
use MongoDB\BSON\UTCDateTime;

$mensajeError = '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    die("ID inválido");
}

$mascota = $mascotas->findOne(['id' => $id]);

if (!$mascota) {
    die("Mascota no encontrada.");
}

// Procesar actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $especie = $_POST['especie'] ?? '';
    $fechaNacInput = $_POST['fecha_nac'] ?? '';
    $precioInput = $_POST['precio'] ?? '';

    // Validaciones
    if ($nombre === '' || $especie === '' || $fechaNacInput === '' || $precioInput === '') {
        $mensajeError = "⚠️ Todos los campos son obligatorios.";
    } elseif (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u", $nombre)) {
        $mensajeError = "⚠️ El nombre solo puede contener letras y espacios.";
    } elseif (strlen($nombre) > 50) {
        $mensajeError = "⚠️ El nombre no debe tener más de 50 caracteres.";
    } elseif (!is_numeric($precioInput) || $precioInput < 0) {
        $mensajeError = "⚠️ El precio debe ser un número positivo.";
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
                    // Verificar que no exista otra mascota igual (excluyendo la actual)
                    $existe = $mascotas->findOne([
                        'nombre' => $nombre,
                        'especie' => $especie,
                        'id' => ['$ne' => $id]
                    ]);

                    if ($existe) {
                        $mensajeError = "⚠️ Ya existe otra mascota con ese nombre y especie.";
                    } else {
                        $fechaNacUTC = new UTCDateTime($fechaNac->getTimestamp() * 1000);
                        $edad = $hoy->diff($fechaNac)->y;
                        $precio = (float) $precioInput;

                        $mascotas->updateOne(
                            ['id' => $id],
                            ['$set' => [
                                'nombre' => $nombre,
                                'especie' => $especie,
                                'edad' => $edad,
                                'fecha_nac' => $fechaNacUTC,
                                'precio' => $precio
                            ]]
                        );

                       
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

// Preparar valores para el formulario, prioridad POST para mantener datos si hay error
$fecha_nac = isset($mascota['fecha_nac']) ? $mascota['fecha_nac']->toDateTime()->format('Y-m-d') : '';
$precioVal = $_POST['precio'] ?? (isset($mascota['precio']) ? $mascota['precio'] : '');

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

  <?php if ($mensajeError !== '') : ?>
    <div class="alert alert-danger"><?= $mensajeError ?></div>
  <?php endif; ?>

  <form method="POST" class="mt-3" novalidate>
    <div class="mb-3">
      <label>Nombre</label>
      <input type="text" name="nombre" class="form-control" required maxlength="50" value="<?= htmlspecialchars($_POST['nombre'] ?? $mascota['nombre']) ?>">
    </div>
    <div class="mb-3">
      <label>Especie</label>
      <select name="especie" class="form-select" required>
        <option value="" disabled>Selecciona una especie</option>
        <?php
        $especiesValidas = ["Perro", "Gato", "Pez", "Conejo", "Tortuga", "Hamster"];
        $especieActual = $_POST['especie'] ?? $mascota['especie'];
        foreach ($especiesValidas as $esp) {
            $selected = ($esp === $especieActual) ? 'selected' : '';
            echo "<option value=\"$esp\" $selected>$esp</option>";
        }
        ?>
      </select>
    </div>
    <div class="mb-3">
      <label>Fecha de Nacimiento</label>
      <input type="date" name="fecha_nac" class="form-control" required max="<?= date('Y-m-d') ?>" value="<?= htmlspecialchars($_POST['fecha_nac'] ?? $fecha_nac) ?>">
    </div>
    <div class="mb-3">
      <label>Precio (MXN)</label>
      <input type="number" step="0.01" min="0" name="precio" class="form-control" required value="<?= htmlspecialchars($precioVal) ?>">
    </div>
    <button type="submit" class="btn btn-primary">Actualizar</button>
    <a href="index.php" class="btn btn-secondary">Cancelar</a>
  </form>
</body>
</html>
