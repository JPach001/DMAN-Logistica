<?php
$pageTitle = 'DMAN Logistica | Acerca de nosotros';
$pageDescription = 'Conoce la historia, mision y enfoque de DMAN Logistica.';
$activePage = 'acerca';
$pageScripts = ['login-modal.js'];
require __DIR__ . '/includes/site-header.php';
?>

<main>
    <section class="columna">
        <img class="img_acerca_nosotros" src="img/img_acerca_nosostros.jpg" alt="Equipo DMAN Logistica">
    </section>
    <section class="columna">
        <div class="hero-badge">Nosotros</div>
        <h1 class="hero-title" style="font-family:'Montserrat',sans-serif; margin-top:12px;">Tu mudanza, nuestra prioridad.</h1>
        <p class="hero-text">
            En DMAN Logistica somos una empresa queretana y creemos que cada mudanza es el comienzo de una nueva etapa.
            Por eso, desde hace 10 anos nos hemos comprometido a trasladar muebles, cajas o mercancia con el maximo
            cuidado y eficiencia, tanto en rutas locales como foraneas.
        </p>
        <p>
            Contamos con un equipo capacitado y procesos pensados para que tu experiencia sea sencilla:
            embalaje profesional opcional, carga y descarga seguras, y atencion personalizada desde la cotizacion hasta la entrega.
        </p>
        <p>
            Nuestra meta es que cualquier visitante pueda revisar disponibilidad, pedir cotizacion y confirmar con confianza
            si hay transportista libre para su servicio.
        </p>
    </section>
</main>

<?php require __DIR__ . '/includes/site-login-modal.php'; ?>
<?php require __DIR__ . '/includes/site-footer.php'; ?>
