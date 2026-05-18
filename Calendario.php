<?php
require __DIR__ . '/conection.php';

$pageTitle = 'DMAN Logistica | Calendario';
$pageDescription = 'Consulta disponibilidad del transportista antes de solicitar un servicio.';
$activePage = 'calendario';
$pageScripts = ['login-modal.js'];
require __DIR__ . '/includes/site-header.php';

$selectedDate = $_GET['fecha'] ?? date('Y-m-d');
$selectedTransportista = (int) ($_GET['transportista'] ?? 0);

$sampleTransportistas = [
    ['id' => 1, 'nombre' => 'Carlos Ramirez', 'unidad' => 'Camioneta 3.5 t', 'turno' => 'Matutino', 'estado' => 'Disponible'],
    ['id' => 2, 'nombre' => 'Ana Lopez', 'unidad' => 'Camioneta cerrada', 'turno' => 'Vespertino', 'estado' => 'Ocupado'],
    ['id' => 3, 'nombre' => 'Jorge Morales', 'unidad' => 'Camion de redilas', 'turno' => 'Matutino', 'estado' => 'Disponible'],
    ['id' => 4, 'nombre' => 'Luis Herrera', 'unidad' => 'Camion de carga', 'turno' => 'Mixto', 'estado' => 'Parcial'],
];

$sampleSlots = [
    1 => [
        ['hora' => '08:00', 'estado' => 'Libre', 'servicio' => '-', 'detalle' => 'Sin viaje asignado'],
        ['hora' => '10:00', 'estado' => 'Ocupado', 'servicio' => 'Mudanza residencial', 'detalle' => 'Col. Satelite -> Centro'],
        ['hora' => '13:00', 'estado' => 'Libre', 'servicio' => '-', 'detalle' => 'Disponible para cotizacion'],
        ['hora' => '16:00', 'estado' => 'Parcial', 'servicio' => 'Flete local', 'detalle' => 'Regreso estimado 17:30'],
    ],
    2 => [
        ['hora' => '09:00', 'estado' => 'Ocupado', 'servicio' => 'Mudanza de oficina', 'detalle' => 'Reserva confirmada'],
        ['hora' => '12:00', 'estado' => 'Ocupado', 'servicio' => 'Traslado de muebles', 'detalle' => 'Ruta estatal'],
        ['hora' => '15:00', 'estado' => 'Libre', 'servicio' => '-', 'detalle' => 'Disponible por la tarde'],
    ],
    3 => [
        ['hora' => '08:30', 'estado' => 'Libre', 'servicio' => '-', 'detalle' => 'Disponible'],
        ['hora' => '11:30', 'estado' => 'Parcial', 'servicio' => 'Entrega programada', 'detalle' => 'Espacio limitado'],
        ['hora' => '14:30', 'estado' => 'Libre', 'servicio' => '-', 'detalle' => 'Disponible'],
    ],
    4 => [
        ['hora' => '07:30', 'estado' => 'Ocupado', 'servicio' => 'Flete nacional', 'detalle' => 'Carretera libre'],
        ['hora' => '13:30', 'estado' => 'Parcial', 'servicio' => 'Retorno', 'detalle' => 'En camino'],
        ['hora' => '18:00', 'estado' => 'Libre', 'servicio' => '-', 'detalle' => 'Disponible al cierre'],
    ],
];

$transportistas = [];
if (dman_db_ready()) {
    $transportistas = dman_fetch_all(
        "SELECT t.id_transportista,
                CONCAT(u.nombre, ' ', u.apellidos) AS nombre,
                t.unidad,
                t.turno,
                COALESCE(t.estado, 'Disponible') AS estado
         FROM TRANSPORTISTAS t
         INNER JOIN USUARIOS u ON t.id_usuario = u.id_usuario
         ORDER BY u.apellidos, u.nombre"
    );
}

if (!$transportistas) {
    $transportistas = $sampleTransportistas;
}

if ($selectedTransportista <= 0) {
    $selectedTransportista = (int) ($transportistas[0]['id_transportista'] ?? $transportistas[0]['id'] ?? 0);
}

$selectedProfile = null;
foreach ($transportistas as $item) {
    $itemId = (int) ($item['id_transportista'] ?? $item['id'] ?? 0);
    if ($itemId === $selectedTransportista) {
        $selectedProfile = $item;
        break;
    }
}

if (!$selectedProfile) {
    $selectedProfile = $transportistas[0] ?? null;
}

$selectedSlots = $sampleSlots[$selectedTransportista] ?? [];
$busyCount = 0;
$freeCount = 0;
foreach ($selectedSlots as $slot) {
    $state = $slot['estado'] ?? '';
    if ($state === 'Libre') {
        $freeCount++;
    } else {
        $busyCount++;
    }
}

$availableTransportistas = 0;
foreach ($transportistas as $item) {
    $state = strtolower((string) ($item['estado'] ?? ''));
    if ($state === 'disponible' || $state === 'libre') {
        $availableTransportistas++;
    }
}

?>

<main class="page-single">
    <div class="calendar-grid">
        <section class="calendar-hero">
            <div class="hero-badge">Calendario de disponibilidad</div>
            <h1 class="hero-title" style="font-family:'Montserrat',sans-serif;">Revisa si el transportista esta libre antes de solicitar tu servicio.</h1>
            <p class="hero-text">
                Esta vista funciona como la agenda de disponibilidad que hicieron en el proyecto clinico: eliges fecha,
                seleccionas transportista y visualizas sus espacios ocupados o libres para mudanza o flete.
            </p>
        </section>

        <section class="stats-grid">
            <div class="stat-card accent-green">
                <div class="stat-label">Transportistas</div>
                <div class="stat-value"><?php echo count($transportistas); ?></div>
                <div class="stat-note">Registro visible en esta vista</div>
            </div>
            <div class="stat-card accent-blue">
                <div class="stat-label">Disponibles</div>
                <div class="stat-value"><?php echo $availableTransportistas; ?></div>
                <div class="stat-note">Transportistas listos para asignar</div>
            </div>
            <div class="stat-card accent-sand">
                <div class="stat-label">Turnos del dia</div>
                <div class="stat-value"><?php echo count($selectedSlots); ?></div>
                <div class="stat-note">Agenda del transportista seleccionado</div>
            </div>
        </section>

        <section class="panel-grid">
            <aside class="availability-panel">
                <form method="get">
                    <div>
                        <label for="fecha">Fecha</label>
                        <input type="date" id="fecha" name="fecha" value="<?php echo htmlspecialchars($selectedDate); ?>">
                    </div>

                    <div>
                        <label for="transportista">Transportista</label>
                        <select id="transportista" name="transportista">
                            <?php foreach ($transportistas as $transportista): ?>
                                <?php $itemId = (int) ($transportista['id_transportista'] ?? $transportista['id'] ?? 0); ?>
                                <option value="<?php echo $itemId; ?>" <?php echo $itemId === $selectedTransportista ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars(($transportista['nombre'] ?? 'Sin nombre') . ' - ' . ($transportista['unidad'] ?? 'Unidad')); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <button class="btnVerde" type="submit" style="width:100%;">Ver disponibilidad</button>
                </form>

                <?php if ($selectedProfile): ?>
                    <div class="mini-card">
                        <strong class="d-block mb-1"><?php echo htmlspecialchars((string) ($selectedProfile['nombre'] ?? 'Transportista')); ?></strong>
                        <div class="muted"><?php echo htmlspecialchars((string) ($selectedProfile['unidad'] ?? 'Unidad')); ?></div>
                        <div style="margin-top:10px;">
                            <span class="chip chip-sand"><?php echo htmlspecialchars((string) ($selectedProfile['turno'] ?? 'Sin turno')); ?></span>
                            <span class="chip chip-green" style="margin-left:6px;"><?php echo htmlspecialchars((string) ($selectedProfile['estado'] ?? 'Disponible')); ?></span>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="mini-card">
                    <strong class="d-block mb-2">Leyenda</strong>
                    <div class="legend">
                        <span class="chip chip-green">Libre</span>
                        <span class="chip chip-amber">Ocupado</span>
                        <span class="chip chip-blue">Parcial</span>
                    </div>
                </div>
            </aside>

            <section class="page-card">
                <h2 class="section-title" style="margin-bottom:6px; font-family:'Montserrat',sans-serif;">Agenda del dia</h2>
                <p class="muted" style="margin-bottom:18px;">El siguiente bloque resume la disponibilidad del transportista seleccionado para la fecha elegida.</p>

                <div class="timeline">
                    <?php if ($selectedSlots): ?>
                        <?php foreach ($selectedSlots as $slot): ?>
                            <?php
                            $state = (string) ($slot['estado'] ?? 'Libre');
                            $rowClass = 'free';
                            if ($state === 'Ocupado') {
                                $rowClass = 'busy';
                            } elseif ($state === 'Parcial') {
                                $rowClass = 'partial';
                            }
                            ?>
                            <div class="time-row <?php echo $rowClass; ?>">
                                <div class="slot-label"><?php echo htmlspecialchars((string) ($slot['hora'] ?? '')); ?></div>
                                <div>
                                    <strong><?php echo htmlspecialchars((string) ($slot['servicio'] ?? '-')); ?></strong>
                                    <div class="muted"><?php echo htmlspecialchars((string) ($slot['detalle'] ?? '')); ?></div>
                                </div>
                                <div class="chip <?php echo $state === 'Libre' ? 'chip-green' : ($state === 'Parcial' ? 'chip-blue' : 'chip-amber'); ?>">
                                    <?php echo htmlspecialchars($state); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="mini-card">No hay registros para esta fecha.</div>
                    <?php endif; ?>
                </div>

                <div class="cta-strip">
                    <a class="btnCotizar" href="formulario_cotizar.php">Solicitar cotizacion</a>
                    <a class="btnVerde" href="Contacto.php">Hablar con soporte</a>
                </div>
            </section>
        </section>
    </div>
</main>

<?php require __DIR__ . '/includes/site-login-modal.php'; ?>
<?php require __DIR__ . '/includes/site-footer.php'; ?>
