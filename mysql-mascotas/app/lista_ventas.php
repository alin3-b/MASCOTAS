<?php
require 'db.php';  // Aquí debes tener tu conexión PDO en $pdo

// Obtener todas las ventas con datos de mascotas y clientes
$stmt = $pdo->query("
    SELECT 
        ventas.id,
        ventas.fecha_venta,
        mascotas.nombre AS nombre_mascota,
        mascotas.especie AS especie,
        clientes.nombre AS nombre_cliente
    FROM ventas
    JOIN mascotas ON ventas.id_mascota = mascotas.id
    JOIN clientes ON ventas.id_cliente = clientes.id
    ORDER BY ventas.id ASC
");
$listaVentas = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Ventas Realizadas</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
      <a class="navbar-brand" href="index.php">Tienda Mascotas</a>
      <div class="collapse navbar-collapse" id="navbarMenu">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a class="nav-link" href="mascotas.php">Mascotas</a>
          </li>
          <li class="nav-item">
            <a class="nav-link active" href="lista_ventas.php">Ventas</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="clientes.php">Clientes</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Contenido -->
  <main class="container mt-4">
    <h1 class="mb-4">Ventas Realizadas</h1>
    <a href="registrar_venta.php" class="btn btn-primary mb-3">Registrar Nueva Venta</a>

    <table class="table table-bordered table-striped">
      <thead class="table-dark">
        <tr>
          <th>ID Venta</th>
          <th>Mascota</th>
          <th>Cliente</th>
          <th>Fecha de Venta</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($listaVentas as $v): ?>
          <tr>
            <td><?= htmlspecialchars($v['id']) ?></td>
            <td><?= htmlspecialchars($v['nombre_mascota'] . " ({$v['especie']})") ?></td>
            <td><?= htmlspecialchars($v['nombre_cliente']) ?></td>
            <td><?= htmlspecialchars($v['fecha_venta']) ?></td>
            <td>
              <a href="editar_venta.php?id=<?= $v['id'] ?>" class="btn btn-warning btn-sm">Editar</a>
              <a href="eliminar_venta.php?id=<?= $v['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar esta venta?')">Eliminar</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
