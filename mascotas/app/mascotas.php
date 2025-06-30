<?php require 'db.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Panel de Control - Mascotas</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
      <a class="navbar-brand" href="index.php">Tienda Mascotas</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu"
        aria-controls="navbarMenu" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarMenu">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a class="nav-link active" href="mascotas.php">Mascotas</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="lista_ventas.php">Ventas</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="clientes.php">Clientes</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Contenido principal -->
  <main class="container mt-4">
    <h1 class="mb-4">Mascotas registradas</h1>
    <a href="agregar_mascota.php" class="btn btn-primary mb-3">Agregar nueva mascota</a>

    <?php
    try {
        $stmt = $pdo->query("SELECT * FROM mascotas ORDER BY id ASC");
        $datos = $stmt->fetchAll();
    } catch (PDOException $e) {
        echo '<div class="alert alert-danger">Error al obtener las mascotas: ' . htmlspecialchars($e->getMessage()) . '</div>';
        $datos = [];
    }

    if (count($datos) === 0): ?>
      <div class="alert alert-info">No hay mascotas registradas.</div>
    <?php else: ?>
      <table class="table table-bordered table-striped">
        <thead class="table-dark">
          <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Especie</th>
            <th>Edad</th>
            <th>Fecha Nacimiento</th>
            <th>Precio (MXN)</th>
            <th>Vendido</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($datos as $m): 
            $fechaNac = !empty($m['fecha_nac']) ? $m['fecha_nac'] : 'N/A';
            $vendido = !empty($m['vendido']) ? ($m['vendido'] ? 'Sí' : 'No') : 'No';
            $precio = isset($m['precio']) ? number_format($m['precio'], 2) : 'N/A';
          ?>
            <tr>
              <td><?= htmlspecialchars($m['id']) ?></td>
              <td><?= htmlspecialchars($m['nombre']) ?></td>
              <td><?= htmlspecialchars($m['especie']) ?></td>
              <td><?= htmlspecialchars($m['edad']) ?></td>
              <td><?= htmlspecialchars($fechaNac) ?></td>
              <td>$ <?= htmlspecialchars($precio) ?></td>
              <td><?= htmlspecialchars($vendido) ?></td>
              <td>
                <a href="editar_mascota.php?id=<?= urlencode($m['id']) ?>" class="btn btn-warning btn-sm">Editar</a>
                <a href="eliminar_mascota.php?id=<?= urlencode($m['id']) ?>" class="btn btn-danger btn-sm"
                   onclick="return confirm('¿Seguro que quieres eliminar esta mascota?');">Eliminar</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
