<?php
require_once 'config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 'academico') {
    header('Location: dashboard.php');
    exit;
}

// Reportar ausencia
if (isset($_POST['reportar_ausencia'])) {
    $espacio_id = $_POST['espacio_id'];
    $desc = "Ausencia de docente: " . $_POST['descripcion'];
    $usuario_id = $_SESSION['user_id'];
    $stmt = $pdo->prepare("INSERT INTO incidencias (tipo, descripcion, espacio_id, usuario_id) VALUES ('ausencia_docente', ?, ?, ?)");
    $stmt->execute([$desc, $espacio_id, $usuario_id]);
    $mensaje = "Incidencia registrada";
}

$espacios = $pdo->query("SELECT * FROM espacios")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Académico</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        .card { border: 1px solid #ccc; padding: 10px; margin: 10px; width: 250px; display: inline-block; }
    </style>
</head>
<body>
    <h1>Panel Académico</h1>
    <a href="dashboard.php">Volver</a>
    <?php if (isset($mensaje)) echo "<p style='color:green'>$mensaje</p>"; ?>

    <h2>Reportar ausencia de docente en un espacio</h2>
    <?php foreach ($espacios as $e): ?>
        <div class="card">
            <strong><?= htmlspecialchars($e['nombre']) ?></strong><br>
            Estado: <?= $e['estado'] ?><br>
            <form method="post">
                <input type="hidden" name="espacio_id" value="<?= $e['id'] ?>">
                <textarea name="descripcion" rows="2" cols="20" placeholder="Ej: Docente no llegó a la hora"></textarea><br>
                <button type="submit" name="reportar_ausencia">Reportar ausencia</button>
            </form>
        </div>
    <?php endforeach; ?>
</body>
</html>

