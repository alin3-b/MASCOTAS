<?php
require 'db.php';

// Reiniciar el contador a 0 (para que el próximo id sea 1)
$resultado = $database->contadores->updateOne(
    ['_id' => 'mascota_id'],
    ['$set' => ['seq' => 0]]
);

echo "Contador 'mascota_id' reiniciado. El próximo ID será 1.\n";
