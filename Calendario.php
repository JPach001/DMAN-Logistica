<?php
require __DIR__ . '/conection.php';

$pageTitle = 'DMAN Logistica | Calendario';
$pageDescription = 'Calendario mensual de disponibilidad y viajes por cliente.';
$activePage = 'calendario';
$pageScripts = ['login-modal.js'];
$bodyClass = 'calendar-page';

function dman_calendar_month_name(int $month): string
{
    static $months = [
        1 => 'Enero',
        2 => 'Febrero',
        3 => 'Marzo',
        4 => 'Abril',
        5 => 'Mayo',
        6 => 'Junio',
        7 => 'Julio',
        8 => 'Agosto',
        9 => 'Septiembre',
        10 => 'Octubre',
        11 => 'Noviembre',
        12 => 'Diciembre',
    ];

    return $months[$month] ?? 'Mes';
}

function dman_calendar_density_class(int $count): string
{
    if ($count > 10) {
        return 'calendar-day--high';
    }

    if ($count >= 5) {
        return 'calendar-day--medium';
    }

    return 'calendar-day--low';
}

function dman_calendar_datetime_label(?string $value): string
{
    if (!$value) {
        return 'Sin fecha';
    }

    $timestamp = strtotime($value);
    return $timestamp ? date('d/m/Y H:i', $timestamp) : $value;
}

$today = new DateTimeImmutable('today');
$currentYear = (int) $today->format('Y');
$currentMonth = (int) $today->format('n');

$year = isset($_GET['year']) ? (int) $_GET['year'] : $currentYear;
$month = isset($_GET['month']) ? (int) $_GET['month'] : $currentMonth;

if (!checkdate($month, 1, $year)) {
    $year = $currentYear;
    $month = $currentMonth;
}

$monthStart = DateTimeImmutable::createFromFormat('Y-n-j', $year . '-' . $month . '-1') ?: $today;
$monthEnd = $monthStart->modify('last day of this month');
$daysInMonth = (int) $monthStart->format('t');
$firstWeekday = (int) $monthStart->format('N');
$prevMonth = $monthStart->modify('-1 month');
$nextMonth = $monthStart->modify('+1 month');

$dayParam = isset($_GET['day']) ? (int) $_GET['day'] : null;
$selectedDate = null;

if ($dayParam !== null && checkdate($month, $dayParam, $year)) {
    $selectedDate = sprintf('%04d-%02d-%02d', $year, $month, $dayParam);
} elseif ($year === $currentYear && $month === $currentMonth) {
    $selectedDate = $today->format('Y-m-d');
}

$isLoggedIn = !empty($_SESSION['id_usuario']);
$currentUserId = (int) ($_SESSION['id_usuario'] ?? 0);

$tripCounts = [];
$monthTrips = 0;
$daysWithTrips = 0;
$lowDays = 0;
$mediumDays = 0;
$highDays = 0;

if (dman_db_ready()) {
    $countRows = dman_fetch_all(
        'SELECT DATE(fecha_hora_inicio) AS fecha, COUNT(*) AS total
         FROM VIAJES
         WHERE fecha_hora_inicio BETWEEN ? AND ?
         GROUP BY DATE(fecha_hora_inicio)',
        'ss',
        [$monthStart->format('Y-m-d 00:00:00'), $monthEnd->format('Y-m-d 23:59:59')]
    );

    foreach ($countRows as $row) {
        $date = (string) ($row['fecha'] ?? '');
        $count = (int) ($row['total'] ?? 0);
        if ($date === '') {
            continue;
        }

        $tripCounts[$date] = $count;
        $monthTrips += $count;
        $daysWithTrips++;

        if ($count > 10) {
            $highDays++;
        } elseif ($count >= 5) {
            $mediumDays++;
        } else {
            $lowDays++;
        }
    }
}

$selectedTrips = [];
if ($isLoggedIn && $selectedDate && dman_db_ready()) {
    $selectedTrips = dman_fetch_all(
        'SELECT
            v.id_viaje,
            v.origen,
            v.destino,
            v.fecha_hora_inicio,
            v.estado,
            v.volumen_articulos,
            v.distancia_km,
            v.costo_total_estimado,
            COALESCE((
                SELECT GROUP_CONCAT(DISTINCT CONCAT(eu.nombre, " ", eu.apellidos, " (", ve.rol_en_este_viaje, ")") ORDER BY eu.apellidos SEPARATOR " · ")
                FROM VIAJE_EMPLEADOS ve
                INNER JOIN PERFIL_EMPLEADO pe ON ve.id_empleado = pe.id_usuario
                INNER JOIN USUARIOS eu ON pe.id_usuario = eu.id_usuario
                WHERE ve.id_viaje = v.id_viaje
            ), "Sin conductor asignado") AS conductores,
            COALESCE((
                SELECT GROUP_CONCAT(DISTINCT CONCAT(c.marca, " ", c.modelo, " / ", c.placa) ORDER BY c.marca SEPARATOR " · ")
                FROM VIAJE_CAMIONETAS vc
                INNER JOIN CAMIONETAS c ON vc.id_camioneta = c.id_camioneta
                WHERE vc.id_viaje = v.id_viaje
            ), "Sin camioneta asignada") AS camionetas
         FROM VIAJES v
         WHERE v.id_cliente = ? AND DATE(v.fecha_hora_inicio) = ?
         ORDER BY v.fecha_hora_inicio ASC',
        'is',
        [$currentUserId, $selectedDate]
    );
}

require __DIR__ . '/includes/site-header.php';
?>

<main class="page-single">
    <section class="calendar-hero">
        <div class="hero-badge">Vista 1 / Clientes</div>
        <h1 class="hero-title">Calendario mensual de viajes</h1>
        <p class="hero-text">
            Verde: menos de 5 viajes. Amarillo: de 5 a 10. Rojo: más de 10.
            Los domingos quedan cerrados.
        </p>
        <div class="calendar-hero-stats">
            <div class="calendar-hero-stat">
                <span>Viajes del mes</span>
                <strong><?php echo (int) $monthTrips; ?></strong>
            </div>
            <div class="calendar-hero-stat">
                <span>Días con viajes</span>
                <strong><?php echo (int) $daysWithTrips; ?></strong>
            </div>
            <div class="calendar-hero-stat">
                <span>Mes</span>
                <strong><?php echo htmlspecialchars(dman_calendar_month_name($month) . ' ' . $year); ?></strong>
            </div>
        </div>
    </section>

    <section class="calendar-layout">
        <article class="calendar-board page-card">
            <div class="calendar-toolbar">
                <div>
                    <p class="calendar-kicker">Calendario general</p>
                    <h2 class="section-title"><?php echo htmlspecialchars(dman_calendar_month_name($month) . ' ' . $year); ?></h2>
                </div>

                <div class="calendar-nav">
                    <a class="btnVerde" href="Calendario.php?year=<?php echo (int) $prevMonth->format('Y'); ?>&month=<?php echo (int) $prevMonth->format('n'); ?>">
                        Mes anterior
                    </a>
                    <a class="btnVerde" href="Calendario.php?year=<?php echo (int) $nextMonth->format('Y'); ?>&month=<?php echo (int) $nextMonth->format('n'); ?>">
                        Mes siguiente
                    </a>
                </div>
            </div>

            <div class="calendar-legend">
                <span class="chip chip-green">0-4 viajes</span>
                <span class="chip chip-amber">5-10 viajes</span>
                <span class="chip chip-red">+10 viajes</span>
                <span class="chip chip-sand">Domingo cerrado</span>
            </div>

            <div class="calendar-weekdays" aria-hidden="true">
                <span>Lun</span>
                <span>Mar</span>
                <span>Mié</span>
                <span>Jue</span>
                <span>Vie</span>
                <span>Sáb</span>
                <span>Dom</span>
            </div>

            <div class="calendar-grid-days">
                <?php for ($i = 1; $i < $firstWeekday; $i++): ?>
                    <div class="calendar-day calendar-day--empty" aria-hidden="true"></div>
                <?php endfor; ?>

                <?php for ($day = 1; $day <= $daysInMonth; $day++): ?>
                    <?php
                    $dateValue = sprintf('%04d-%02d-%02d', $year, $month, $day);
                    $weekday = (int) date('N', strtotime($dateValue));
                    $count = (int) ($tripCounts[$dateValue] ?? 0);
                    $selected = $selectedDate === $dateValue;
                    $classes = ['calendar-day'];
                    $label = 'No hay viajes';
                    $clickHref = '#';
                    $clickAttrs = '';

                    if ($weekday === 7) {
                        $classes[] = 'calendar-day--rest';
                        $label = 'Descanso';
                    } else {
                        $classes[] = dman_calendar_density_class($count);
                        $label = $count === 1 ? '1 viaje' : $count . ' viajes';
                        if ($isLoggedIn) {
                            $clickHref = 'Calendario.php?year=' . (int) $year . '&month=' . (int) $month . '&day=' . (int) $day;
                        } else {
                            $clickHref = '#loginModal';
                            $clickAttrs = ' onclick="openLoginModal(); return false;"';
                        }
                    }

                    if ($selected) {
                        $classes[] = 'calendar-day--selected';
                    }
                    ?>

                    <?php if ($weekday === 7): ?>
                        <div class="<?php echo htmlspecialchars(implode(' ', $classes)); ?>">
                            <span class="calendar-day__number"><?php echo (int) $day; ?></span>
                            <span class="calendar-day__label"><?php echo htmlspecialchars($label); ?></span>
                        </div>
                    <?php else: ?>
                        <a class="<?php echo htmlspecialchars(implode(' ', $classes)); ?>" href="<?php echo htmlspecialchars($clickHref); ?>"<?php echo $clickAttrs; ?> aria-label="Ver <?php echo htmlspecialchars($day . ' de ' . dman_calendar_month_name($month) . ' de ' . $year); ?>">
                            <span class="calendar-day__number"><?php echo (int) $day; ?></span>
                            <span class="calendar-day__label"><?php echo htmlspecialchars($label); ?></span>
                            <span class="calendar-day__meta"><?php echo (int) $count; ?></span>
                        </a>
                    <?php endif; ?>
                <?php endfor; ?>
            </div>
        </article>

        <aside class="calendar-side">
            <div class="page-card calendar-detail-card">
                <?php if ($isLoggedIn): ?>
                    <div class="calendar-detail-head">
                        <div>
                            <p class="calendar-kicker">Mis viajes</p>
                            <h3 class="section-title">
                                <?php echo $selectedDate ? htmlspecialchars(dman_calendar_datetime_label($selectedDate . ' 00:00:00')) : 'Selecciona un día'; ?>
                            </h3>
                        </div>
                        <span class="chip chip-blue"><?php echo count($selectedTrips); ?> viaje(s)</span>
                    </div>

                    <p class="muted">
                        Solo ves tus propios viajes. También aparecen los cotizados.
                    </p>

                    <?php if ($selectedDate && $selectedTrips): ?>
                        <div class="calendar-trips-list">
                            <?php foreach ($selectedTrips as $trip): ?>
                                <article class="calendar-trip">
                                    <div class="calendar-trip__top">
                                        <strong>Viaje #<?php echo (int) $trip['id_viaje']; ?></strong>
                                        <span class="chip <?php echo htmlspecialchars($trip['estado'] === 'Cotizado' ? 'chip-sand' : ($trip['estado'] === 'Confirmado' ? 'chip-green' : 'chip-amber')); ?>">
                                            <?php echo htmlspecialchars((string) $trip['estado']); ?>
                                        </span>
                                    </div>

                                    <div class="calendar-trip__info">
                                        <div><span>Origen</span><strong><?php echo htmlspecialchars((string) $trip['origen']); ?></strong></div>
                                        <div><span>Destino</span><strong><?php echo htmlspecialchars((string) $trip['destino']); ?></strong></div>
                                        <div><span>Inicio</span><strong><?php echo htmlspecialchars(dman_calendar_datetime_label((string) $trip['fecha_hora_inicio'])); ?></strong></div>
                                        <div><span>Distancia</span><strong><?php echo number_format((float) $trip['distancia_km'], 2); ?> km</strong></div>
                                        <div><span>Volumen</span><strong><?php echo htmlspecialchars((string) $trip['volumen_articulos']); ?></strong></div>
                                        <div><span>Costo</span><strong>$<?php echo number_format((float) $trip['costo_total_estimado'], 2); ?></strong></div>
                                        <div><span>Conductor</span><strong><?php echo htmlspecialchars((string) $trip['conductores']); ?></strong></div>
                                        <div><span>Camioneta</span><strong><?php echo htmlspecialchars((string) $trip['camionetas']); ?></strong></div>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    <?php elseif ($selectedDate): ?>
                        <div class="calendar-empty">
                            No tienes viajes para esta fecha.
                        </div>
                    <?php else: ?>
                        <div class="calendar-empty">
                            Elige un día para ver tus viajes.
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="calendar-login-box">
                        <p class="calendar-kicker">Acceso cliente</p>
                        <h3 class="section-title">Inicia sesión para ver tus viajes</h3>
                        <p class="muted">
                            Si tocas cualquier día, te mandamos al login para registrar o entrar a tu cuenta.
                        </p>
                        <button class="btnVerde" type="button" onclick="openLoginModal();">Iniciar sesión</button>
                    </div>
                <?php endif; ?>
            </div>
        </aside>
    </section>
</main>

<?php require __DIR__ . '/includes/site-login-modal.php'; ?>
<?php require __DIR__ . '/includes/site-footer.php'; ?>
