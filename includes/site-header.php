<?php
$connectionPath = __DIR__ . '/../conection.php';
if (file_exists($connectionPath)) {
    require_once $connectionPath;
}

$pageTitle = $pageTitle ?? 'DMAN Logistica';
$pageDescription = $pageDescription ?? 'Sistema de fletes y mudanzas';
$activePage = $activePage ?? 'inicio';
$showLoginButton = $showLoginButton ?? true;
$bodyClass = $bodyClass ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?php echo htmlspecialchars($pageDescription); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&family=Oswald:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat+Underline:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="img/icon_web.png">
    <link rel="stylesheet" href="Estilos.css">
    <link rel="stylesheet" href="login-modal.css">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
</head>
<body class="<?php echo htmlspecialchars($bodyClass); ?>">
<header>
    <div class="DMANLogistica">
        <a href="Inicio.php" aria-label="Ir al inicio">
            <img class="img_titulo" src="img/icon_web.png" alt="DMAN Logistica">
        </a>
        <h1>DMAN Logistica</h1>
        <?php if ($showLoginButton): ?>
            <a class="login-wrapper" href="#loginModal" onclick="openLoginModal(); return false;">
                <button class="btnIniciarSesion" type="button">Iniciar sesión</button>
            </a>
        <?php endif; ?>
    </div>
    <nav class="secciones" aria-label="Navegación principal">
        <a class="<?php echo $activePage === 'inicio' ? 'btn-actual' : 'btn'; ?>" href="Inicio.php">Inicio</a>
        <a class="<?php echo $activePage === 'servicios' ? 'btn-actual' : 'btn'; ?>" href="Servicios.php">Servicios</a>
        <a class="<?php echo $activePage === 'calendario' ? 'btn-actual' : 'btn'; ?>" href="Calendario.php">Calendario</a>
        <a class="<?php echo $activePage === 'acerca' ? 'btn-actual' : 'btn'; ?>" href="Acerca_de_nosotros.php">Acerca de nosotros</a>
        <a class="<?php echo $activePage === 'contacto' ? 'btn-actual' : 'btn'; ?>" href="Contacto.php">Contacto</a>
        <a class="<?php echo $activePage === 'faq' ? 'btn-actual' : 'btn'; ?>" href="Preguntas_frecuentes.php">Preguntas frecuentes</a>
    </nav>
</header>
