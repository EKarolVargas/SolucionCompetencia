<?php
require_once 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre']
    $correo = $_POST['correo'];
    $contrasela = $_POST['contraseña'];
    $cargo = $_POST['cargo']

    $stmt = $pdo->prepare("SELECT u.*, r.nombre_rol FROM usuarios u JOIN roles r ON u.id_rol = r.id_rol WHERE u.email = ?");
    $stmt->execute([$correo]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id_usuario'];
        $_SESSION['user_name'] = $user['nombre'];
        $_SESSION['rol'] = $user['nombre_rol'];
        
        // Actualizar última conexión
        $update = $pdo->prepare("UPDATE usuarios SET ultima_conexion = NOW() WHERE id_usuario = ?");
        $update->execute([$user['id_usuario']]);

        header('Location: dashboard.php');
        exit;
    } else {
        header('Location: index.html?error=1');
        exit;
    }
} else {
    header('Location: index.html');
    exit;
}
?>|
