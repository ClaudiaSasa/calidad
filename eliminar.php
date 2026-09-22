<?php
session_start();

// SOLO PUEDE ELIMINAR UN USUARIO QUE YA INICIÓ SESIÓN.
if (!isset($_SESSION['usuario'])) {
    header('Location: index.php');
    exit;
}

// LA ELIMINACIÓN DEBE LLEGAR DESDE UN FORMULARIO.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: clientes.php');
    exit;
}

include 'conexion.php';

// RECIBIR EL IDENTIFICADOR DEL CLIENTE.
$id = (int) ($_POST['id'] ?? 0);

// ELIMINAR EL REGISTRO SELECCIONADO.
$consulta = mysqli_prepare($conexion, 'DELETE FROM clientes WHERE id = ?');
mysqli_stmt_bind_param($consulta, 'i', $id);
mysqli_stmt_execute($consulta);

// REGRESAR AL LISTADO.
header('Location: clientes.php?mensaje=Cliente+eliminado+correctamente');
exit;
