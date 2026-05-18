<?php
$pageTitle = 'DMAN Logistica | Preguntas frecuentes';
$pageDescription = 'Resuelve dudas comunes sobre cotizacion, disponibilidad y tipos de servicio.';
$activePage = 'faq';
$pageScripts = ['login-modal.js'];
require __DIR__ . '/includes/site-header.php';
?>

<main class="page-single">
    <section class="hero-banner">
        <div class="hero-badge">FAQ</div>
        <h1 class="hero-title" style="font-family:'Montserrat',sans-serif;">Preguntas frecuentes</h1>
        <p class="hero-text">Aqui reunimos las dudas que mas ayudan antes de cotizar una mudanza o un flete.</p>
    </section>

    <section class="faq-list">
        <details class="faq-item" open>
            <summary>Como veo si hay transportista disponible?</summary>
            <div class="faq-answer">
                En la seccion <strong>Calendario</strong> puedes elegir fecha y revisar si el transportista esta libre,
                ocupado o con disponibilidad parcial.
            </div>
        </details>
        <details class="faq-item">
            <summary>La cotizacion es inmediata?</summary>
            <div class="faq-answer">
                El formulario calcula un estimado basado en distancia, pisos, cargadores, emplaye y volumen.
                Cuando conectemos la base de datos, el estimado se guardara como solicitud.
            </div>
        </details>
        <details class="faq-item">
            <summary>Que tipo de servicios manejan?</summary>
            <div class="faq-answer">
                Mudanzas residenciales, mudanzas de oficina y fletes nacionales. Tambien podemos contemplar servicios
                especiales si la carga requiere atencion extra.
            </div>
        </details>
        <details class="faq-item">
            <summary>Puedo solicitar un servicio para hoy?</summary>
            <div class="faq-answer">
                Si el calendario muestra un transportista libre en la fecha seleccionada, el usuario puede avanzar con la cotizacion
                o el contacto inmediato.
            </div>
        </details>
    </section>
</main>

<?php require __DIR__ . '/includes/site-login-modal.php'; ?>
<?php require __DIR__ . '/includes/site-footer.php'; ?>
