<?php
$pageTitle = 'DMAN Logistica | Cotizacion';
$pageDescription = 'Calcula un estimado de mudanza o flete en linea.';
$activePage = 'inicio';
$pageScripts = ['login-modal.js', 'funcion_cotizar.js'];
require __DIR__ . '/includes/site-header.php';
?>

<main class="page-single">
    <div class="calendar-grid">
        <section class="hero-banner">
            <div class="hero-badge">Cotizacion en linea</div>
            <h1 class="hero-title" style="font-family:'Montserrat',sans-serif;">Calcula el estimado de tu mudanza en minutos.</h1>
            <p class="hero-text">La calculadora suma distancia, pisos, cargadores, emplaye y volumen para darte una referencia inmediata antes de guardar la solicitud.</p>
        </section>

        <section class="panel-grid">
            <aside class="availability-panel">
                <h2 class="section-title">Como se calcula</h2>
                <div class="mini-card">
                    <strong class="d-block mb-1">1. Distancia</strong>
                    <div class="muted">Se consulta la ruta entre origen y destino.</div>
                </div>
                <div class="mini-card">
                    <strong class="d-block mb-1">2. Carga adicional</strong>
                    <div class="muted">Pisos, cargadores extra y emplaye se suman al total.</div>
                </div>
                <div class="mini-card">
                    <strong class="d-block mb-1">3. Volumen</strong>
                    <div class="muted">El volumen define el rango del vehiculo y el costo base.</div>
                </div>
            </aside>

            <section class="page-card">
                <form id="cotizador" class="cotizador-form">
                    <h2 class="section-title mb-3">Direcciones</h2>

                    <label for="origen">Origen</label>
                    <input type="text" id="origen" placeholder="Ej: Av. Juarez 23, Queretaro">

                    <label for="destino">Destino</label>
                    <input type="text" id="destino" placeholder="Ej: Blvrd Bernardo Quintana 502">

                    <button type="button" class="btnVerde" onclick="calcularDistancia()">Calcular distancia</button>
                    <p id="distancia-texto" class="muted" style="margin-top:12px;">Distancia: -- km</p>

                    <h2 class="section-title" style="margin-top:24px;">Detalles del servicio</h2>

                    <label for="pisos_origen">Pisos a bajar (origen)</label>
                    <input type="number" id="pisos_origen" placeholder="0" min="0" max="10">

                    <label for="pisos_destino">Pisos a subir (destino)</label>
                    <input type="number" id="pisos_destino" placeholder="0" min="0" max="10">

                    <label for="cargadores">Cargadores extra</label>
                    <input type="number" id="cargadores" placeholder="0" min="0" max="2">

                    <label for="emplaye">Muebles a emplayar</label>
                    <input type="number" id="emplaye" placeholder="0" min="0">

                    <label for="volumen">Volumen aproximado</label>
                    <select id="volumen">
                        <option value="poco">Poco (1-5 m3)</option>
                        <option value="medio">Medio (6-12 m3)</option>
                        <option value="mucho">Mucho (13+ m3)</option>
                    </select>

                    <div class="mini-card" style="margin-top:18px;">
                        <h2 class="section-title mb-1">Total estimado</h2>
                        <p id="total" style="font-size:2rem; font-weight:800; margin:0;">$0 MXN</p>
                    </div>
                </form>
            </section>
        </section>
    </div>
</main>

<?php require __DIR__ . '/includes/site-login-modal.php'; ?>
<?php require __DIR__ . '/includes/site-footer.php'; ?>
