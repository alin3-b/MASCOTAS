<?php
require 'vendor/autoload.php';

$client = new MongoDB\Client("mongodb://mongo:27017");
$database = $client->petshop;

// Colecciones
$mascotas = $database->mascotas;
$clientes = $database->clientes;
$ventas   = $database->ventas;
$contadores = $database->contadores;
?>
