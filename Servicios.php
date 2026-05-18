<?php
$pageTitle = 'DMAN Logistica | Servicios';
$pageDescription = 'Servicios de mudanzas residenciales, oficinas y fletes nacionales.';
$activePage = 'servicios';
$pageScripts = ['login-modal.js'];
require __DIR__ . '/includes/site-header.php';
?>

<main class="main_servicios page-stack">
    <section class="hero-banner" style="grid-column:1/-1;">
        <div class="hero-badge">Servicios</div>
        <h1 class="hero-title" style="font-family:'Montserrat',sans-serif;">Soluciones pensadas para mover, proteger y entregar a tiempo.</h1>
        <p class="hero-text">Cada servicio fue adaptado para que el cliente entienda rapido que incluye, como se agenda y donde puede ver disponibilidad.</p>
    </section>

    <article class="columna">
        <h2>Mudanzas residenciales</h2>
        <p>Traslado de casas y departamentos, incluyendo empaque, carga, descarga y acomodo basico.</p>
        <img class="img_servicios" src="img/img_servicios_1.jpg" alt="Mudanzas residenciales">
        <p><a class="service-link" href="formulario_cotizar.php?servicio=residencial">Cotizar este servicio</a></p>
    </article>

    <article class="columna">
        <h2>Mudanzas de oficinas</h2>
        <p>Transporte de mobiliario, equipos y documentos con cuidado especial para operaciones empresariales.</p>
        <img class="img_servicios" src="img/img_servicios_2.jpg" alt="Mudanzas de oficinas">
        <p><a class="service-link" href="formulario_cotizar.php?servicio=oficina">Cotizar este servicio</a></p>
    </article>

    <article class="columna">
        <h2>Fletes nacionales</h2>
        <p>Transporte de carga a nivel nacional, desde pequeñas mercancias hasta grandes volumenes.</p>
        <img class="img_servicios" src="img/img_acerca_nosostros.jpg" alt="Fletes nacionales">
        <p><a class="service-link" href="formulario_cotizar.php?servicio=flete">Cotizar este servicio</a></p>
    </article>
</main>

<?php require __DIR__ . '/includes/site-login-modal.php'; ?>
<?php require __DIR__ . '/includes/site-footer.php'; ?>
