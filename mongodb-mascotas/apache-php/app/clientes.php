<?php 
require 'db.php';

// 1. Asignar ID a documentos que no tienen 'id'
$ultimo = $clientes->find(['id' => ['$exists' => true]], ['sort' => ['id' => -1], 'limit' => 1])->toArray();
$contador = (count($ultimo) > 0) ? $ultimo[0]['id'] + 1 : 1;

$sinId = $clientes->find(['id' => ['$exists' => false]]);
foreach ($sinId as $doc) {
    $clientes->updateOne(
        ['_id' => $doc['_id']],
        ['$set' => ['id' => $contador]]
    );
    $contador++;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Clientes - Tienda Mascotas</title>
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
          <li class="nav-item"><a class="nav-link" href="mascotas.php">Mascotas</a></li>
          <li class="nav-item"><a class="nav-link" href="lista_ventas.php">Ventas</a></li>
          <li class="nav-item"><a class="nav-link active" href="clientes.php">Clientes</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Contenido -->
  <main class="container mt-4">
    <h1 class="mb-4">Clientes Registrados</h1>
    <a href="agregar_cliente.php" class="btn btn-primary mb-3">Agregar nuevo cliente</a>

    <table class="table table-bordered table-striped">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Nombre</th>
          <th>Email</th>
          <th>Código Postal</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $cursor = $clientes->find([], ['sort' => ['id' => 1]]);
        foreach ($cursor as $cliente) {
            // Sanitiza datos para evitar inyección o problemas de HTML
            $id = htmlspecialchars($cliente['id'] ?? '');
            $nombre = htmlspecialchars($cliente['nombre'] ?? '');
            $email = htmlspecialchars($cliente['email'] ?? '');
            $codigo_postal = htmlspecialchars($cliente['codigo_postal'] ?? '');

            echo "<tr>
                    <td>{$id}</td>
                    <td>{$nombre}</td>
                    <td>{$email}</td>
                    <td>{$codigo_postal}</td>
                    <td>
                      <a href='editar_cliente.php?id={$id}' class='btn btn-warning btn-sm'>Editar</a>
                      <a href='eliminar_cliente.php?id={$id}' class='btn btn-danger btn-sm' onclick=\"return confirm('¿Eliminar este cliente?')\">Eliminar</a>
                    </td>
                  </tr>";
        }
        ?>
      </tbody>
    </table>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
