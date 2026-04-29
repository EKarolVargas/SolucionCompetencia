<?php
require_once 'config.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: index.html');
    exit;
}
$rol = $_SESSION['rol'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        .menu a { display: inline-block; margin: 5px; padding: 8px 12px; background: #2c3e50; color: white; text-decoration: none; border-radius: 4px; }
        .espacios { display: flex; flex-wrap: wrap; margin-top: 20px; }
        .card { border: 1px solid #ccc; padding: 10px; margin: 10px; width: 200px; border-radius: 5px; }
        .disponible { background: #d4edda; }
        .ocupado { background: #f8d7da; }
        .mantenimiento { background: #fff3cd; }
    </style>
</head>
<body>
    <h1>Bienvenido, <?= htmlspecialchars($_SESSION['user_name']) ?> (<?= $rol ?>)</h1>
    <div class="menu">
        <?php if ($rol == 'admin'): ?>
            <a href="admin.php">Gestionar Espacios y Usuarios</a>
        <?php elseif ($rol == 'prefecto'): ?>
            <a href="prefecto.php">Cambiar Estado / Reportar Incidencias</a>
        <?php elseif ($rol == 'academico'): ?>
            <a href="academico.php">Reportar Ausencia de Docente</a>
        <?php endif; ?>
        <a href="estado.php">Ver Estado Actual de Espacios</a>
        <a href="logout.php">Cerrar Sesión</a>
    </div>

    <h2>Estado de espacios en tiempo real (actualiza cada 10s)</h2>
    <div id="lista-espacios" class="espacios"></div>

    <script>
        function cargarEstado() {
            fetch('estado.php?ajax=1')
                .then(res => res.json())
                .then(data => {
                    let html = '';
                    data.forEach(esp => {
                        let clase = esp.estado;
                        html += `<div class="card ${clase}">
                                    <strong>${esp.nombre}</strong><br>
                                    Tipo: ${esp.tipo}<br>
                                    Capacidad: ${esp.capacidad}<br>
                                    Estado: ${esp.estado}
                                </div>`;
                    });
                    document.getElementById('lista-espacios').innerHTML = html;
                });
        }
        cargarEstado();
        setInterval(cargarEstado, 10000);
    </script>
</body>
</html>