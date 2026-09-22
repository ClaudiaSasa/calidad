<?php
// INICIAR LA SESIÓN PARA RECORDAR AL USUARIO.
session_start();

// SI YA INICIÓ SESIÓN, MOSTRAR DIRECTAMENTE LOS CLIENTES.
if (isset($_SESSION['usuario'])) {
    header('Location: clientes.php');
    exit;
}

// CONECTAR CON LA BASE DE DATOS.
include 'conexion.php';

$mensaje = '';

// COMPROBAR SI SE ENVIÓ EL FORMULARIO.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario']);
    $contrasena = $_POST['contrasena'];

    // BUSCAR EL USUARIO SIN PEGAR DIRECTAMENTE LOS DATOS EN EL SQL.
    $consulta = mysqli_prepare(
        $conexion,
        'SELECT id, nombre, contrasena FROM usuarios WHERE usuario = ?'
    );

    mysqli_stmt_bind_param($consulta, 's', $usuario);
    mysqli_stmt_execute($consulta);
    mysqli_stmt_bind_result($consulta, $id, $nombre, $contrasena_guardada);

    // VERIFICAR SI EL USUARIO EXISTE Y SI LA CONTRASEÑA ES CORRECTA.
    if (mysqli_stmt_fetch($consulta) && password_verify($contrasena, $contrasena_guardada)) {
        session_regenerate_id(true);
        $_SESSION['usuario'] = $nombre;
        header('Location: clientes.php');
        exit;
    }

    $mensaje = 'El usuario o la contraseña son incorrectos.';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body class="pagina-acceso">

    <main class="tarjeta tarjeta-acceso">
        <div class="logo">GC</div>
        <h1>Gestión de clientes</h1>
        <p class="descripcion">Ingresa con tu usuario y contraseña.</p>

        <?php if ($mensaje !== ''): ?>
            <p class="mensaje mensaje-error"><?= htmlspecialchars($mensaje) ?></p>
        <?php endif; ?>

        <?php if (isset($_GET['registro'])): ?>
            <p class="mensaje mensaje-exito">Usuario creado. Ahora puedes iniciar sesión.</p>
        <?php endif; ?>

        <form method="POST">
            <label for="usuario">Usuario</label>
            <input id="usuario" name="usuario" type="text" required>

            <label for="contrasena">Contraseña</label>
            <input id="contrasena" name="contrasena" type="password" required>

            <button class="boton" type="submit">Iniciar sesión</button>
        </form>

        <p class="enlace-final">¿No tienes cuenta? <a href="registro.php">Crear usuario</a></p>
    </main>

</body>
</html>
