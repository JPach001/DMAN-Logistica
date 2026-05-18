<?php
$pageTitle = 'DMAN Logistica | Inicio';
$pageDescription = 'Servicio de fletes y mudanzas con cotizacion rapida y atencion personalizada.';
$activePage = 'inicio';
$pageScripts = ['login-modal.js'];
require __DIR__ . '/includes/site-header.php';
?>

<main>
    <section class="columna">
        <img class="img_inicio" src="img/camioneta_inicio.jpg" alt="Camioneta de mudanza">
    </section>
    <section class="columna">
        <div class="hero-badge">Fletes y mudanzas</div>
        <h1 class="hero-title" style="font-family:'Montserrat',sans-serif; margin-top:12px;">Servicio profesional para mover lo importante sin complicaciones.</h1>
        <p class="hero-text">
            Ofrecemos servicio profesional de fletes y mudanzas en todo el pais, adaptandonos a tus necesidades
            con atencion personalizada. Ya sea que requieras trasladar muebles, enseres, mercancia o equipo especial,
            garantizamos manejo cuidadoso, puntualidad y precios justos.
        </p>
        <ul>
            <li>Cobertura nacional</li>
            <li>Transporte seguro y confiable</li>
            <li>Personal capacitado en carga y descarga</li>
            <li>Opciones flexibles para mudanzas completas o parciales</li>
        </ul>
        <div class="cta-strip">
            <a class="btnCotizar" href="formulario_cotizar.php">Cotizar ahora</a>
            <a class="btnVerde" href="Calendario.php">Ver disponibilidad</a>
        </div>
    </section>
</main>

<?php require __DIR__ . '/includes/site-login-modal.php'; ?>
<?php require __DIR__ . '/includes/site-footer.php'; ?>
