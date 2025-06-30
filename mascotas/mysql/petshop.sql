-- Crear base de datos
CREATE DATABASE IF NOT EXISTS petshop;
USE petshop;

-- Tabla de clientes
CREATE TABLE clientes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  codigo_postal INT NOT NULL CHECK (codigo_postal BETWEEN 10000 AND 99999)
);

-- Tabla de mascotas
CREATE TABLE mascotas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(50) NOT NULL,
  especie ENUM('Perro', 'Gato', 'Pez', 'Conejo', 'Tortuga', 'Hamster') NOT NULL,
  edad INT NOT NULL,
  fecha_nac DATE NOT NULL,
  precio DECIMAL(10,2) NOT NULL CHECK (precio >= 300),
  vendido BOOLEAN DEFAULT FALSE
);

-- Tabla de ventas
CREATE TABLE ventas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_mascota INT NOT NULL,
  id_cliente INT NOT NULL,
  fecha_venta DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_mascota) REFERENCES mascotas(id)
      ON DELETE CASCADE,
  FOREIGN KEY (id_cliente) REFERENCES clientes(id)
      ON DELETE CASCADE
);

