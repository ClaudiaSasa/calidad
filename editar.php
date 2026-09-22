<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: index.php');
    exit;
}

include 'conexion.php';

// RECIBIR EL IDENTIFICADOR DEL CLIENTE.
$id = (int) ($_GET['id'] ?? 0);
$mensaje = '';

// GUARDAR LOS CAMBIOS CUANDO SE ENVÍA EL FORMULARIO.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $documento = trim($_POST['documento']);
    $nombre = trim($_POST['nombre']);
    $telefono = trim($_POST['telefono']);
    $correo = trim($_POST['correo']);

    if ($documento === '' || $nombre === '') {
        $mensaje = 'El documento y el nombre son obligatorios.';
    } else {
        // REVISAR QUE EL DOCUMENTO NO PERTENEZCA A OTRO CLIENTE.
        $repetido = mysqli_prepare(
            $conexion,
            'SELECT id FROM clientes WHERE documento = ? AND id != ?'
        );
        mysqli_stmt_bind_param($repetido, 'si', $documento, $id);
        mysqli_stmt_execute($repetido);
        mysqli_stmt_store_result($repetido);

        if (mysqli_stmt_num_rows($repetido) > 0) {
            $mensaje = 'Otro cliente ya tiene ese documento.';
        } else {
            // ACTUALIZAR EL CLIENTE.
            $consulta = mysqli_prepare(
                $conexion,
                'UPDATE clientes SET documento = ?, nombre = ?, telefono = ?, correo = ? WHERE id = ?'
            );

            mysqli_stmt_bind_param($consulta, 'ssssi', $documento, $nombre, $telefono, $correo, $id);

            if (mysqli_stmt_execute($consulta)) {
                header('Location: clientes.php?mensaje=Cliente+actualizado+correctamente');
                exit;
            }

            $mensaje = 'No fue posible actualizar el cliente.';
        }
    }
}

// CONSULTAR LA INFORMACIÓN ACTUAL DEL CLIENTE.
$consulta = mysqli_prepare(
    $conexion,
    'SELECT documento, nombre, telefono, correo FROM clientes WHERE id = ?'
);

mysqli_stmt_bind_param($consulta, 'i', $id);
mysqli_stmt_execute($consulta);
mysqli_stmt_bind_result($consulta, $documento, $nombre, $telefono, $correo);

if (!mysqli_stmt_fetch($consulta)) {
    header('Location: clientes.php?mensaje=El+cliente+no+existe');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar cliente</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>

    <main class="contenedor contenedor-formulario">
        <section class="tarjeta">
            <h1>Editar cliente</h1>
            <p class="descripcion">Modifica la información y guarda los cambios.</p>

            <?php if ($mensaje !== ''): ?>
                <p class="mensaje mensaje-error"><?= htmlspecialchars($mensaje) ?></p>
            <?php endif; ?>

            <form method="POST">
                <label for="documento">Documento</label>
                <input id="documento" name="documento" type="text" maxlength="20"
                       value="<?= htmlspecialchars($documento) ?>" required>

                <label for="nombre">Nombre completo</label>
                <input id="nombre" name="nombre" type="text" maxlength="100"
                       value="<?= htmlspecialchars($nombre) ?>" required>

                <label for="telefono">Teléfono</label>
                <input id="telefono" name="telefono" type="text" maxlength="20"
                       value="<?= htmlspecialchars($telefono ?? '') ?>">

                <label for="correo">Correo electrónico</label>
                <input id="correo" name="correo" type="email" maxlength="100"
                       value="<?= htmlspecialchars($correo ?? '') ?>">

                <button class="boton" type="submit">Actualizar cliente</button>
            </form>

            <p class="enlace-final"><a href="clientes.php">Volver al listado</a></p>
        </section>
    </main>

</body>
</html>
