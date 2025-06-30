<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Tienda de Mascotas - Inicio</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

  <style>
    main {
      position: relative;
      z-index: 0;
    }

    main::before {
      content: "";
      position: absolute;
      inset: 0;
      background-image: url('fondo.jpg'); 
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      
      z-index: -1;
    }
  </style>
</head>
<body class="d-flex flex-column min-vh-100">

  <header class="bg-primary text-white p-4 mb-5">
    <div class="container">
      <h1 class="display-4">Bienvenido a la Tienda de Mascotas</h1>
      <p class="lead">Compra mascotas de forma segura y fácil.</p>
    </div>
  </header>

  <main class="container text-center flex-grow-1 d-flex flex-column justify-content-end py-5">
  <div class="mt-auto">
    <a href="mascotas.php" class="btn btn-lg btn-success mx-2">Ver Mascotas</a>
    <a href="clientes.php" class="btn btn-lg btn-primary mx-2">Ver Clientes</a>
    <a href="lista_ventas.php" class="btn btn-lg btn-info mx-2">Lista de Ventas</a>
  </div>
</main>


  <footer class="bg-dark text-white text-center p-3 mt-auto">
    &copy; <?= date('Y') ?> Tienda de Mascotas.
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
