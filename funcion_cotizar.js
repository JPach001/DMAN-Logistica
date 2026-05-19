const cotizador = document.getElementById('cotizador');
const origenInput = document.getElementById('origen');
const destinoInput = document.getElementById('destino');
const distanciaTexto = document.getElementById('distancia-texto');
const statusTexto = document.getElementById('cotizador-status');
const totalTexto = document.getElementById('total');

let lastDistanceKm = 0;
let distanceTimer = null;
let requestToken = 0;

if (cotizador) {
    cotizador.querySelectorAll('input, select').forEach((el) => {
        el.addEventListener('input', () => {
            if (el === origenInput || el === destinoInput) {
                scheduleDistanceLookup();
            } else {
                recalcularTotal();
            }
        });
    });
}

function scheduleDistanceLookup() {
    if (distanceTimer) {
        window.clearTimeout(distanceTimer);
    }
    distanceTimer = window.setTimeout(() => {
        void calcularDistancia();
    }, 500);
}

async function calcularDistancia() {
    const origen = origenInput?.value.trim() ?? '';
    const destino = destinoInput?.value.trim() ?? '';

    if (!origen || !destino) {
        lastDistanceKm = 0;
        setDistanceState('Distancia: -- km', 'Escribe origen y destino para calcular la ruta.');
        recalcularTotal();
        return;
    }

    const currentToken = ++requestToken;
    setDistanceState('Calculando distancia...', 'Consultando la ruta mas adecuada.');

    try {
        const response = await fetch('api/distancia.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ origen, destino }),
        });

        const data = await response.json();

        if (currentToken !== requestToken) {
            return;
        }

        if (!response.ok || !data.success) {
            throw new Error(data.message || 'No se pudo calcular la distancia.');
        }

        lastDistanceKm = Number(data.distance_km) || 0;
        const sourceLabel = data.source ? ` (${data.source})` : '';
        setDistanceState(`Distancia: ${lastDistanceKm.toFixed(2)} km${sourceLabel}`, data.message || 'Ruta calculada correctamente.');
        recalcularTotal();
    } catch (error) {
        if (currentToken !== requestToken) {
            return;
        }

        console.error(error);
        lastDistanceKm = 0;
        setDistanceState(
            'Distancia: -- km',
            'No fue posible calcular la ruta. Revisa las direcciones o intenta de nuevo.'
        );
        recalcularTotal();
    }
}

function setDistanceState(distanceText, statusText) {
    if (distanciaTexto) {
        distanciaTexto.innerText = distanceText;
    }
    if (statusTexto) {
        statusTexto.innerText = statusText;
    }
}

function recalcularTotal() {
    const costoKm = 15;
    const costoPiso = 50;
    const costoCargador = 200;
    const costoEmplaye = 30;

    let total = lastDistanceKm * costoKm;
    const pisosOrigen = parseInt(document.getElementById('pisos_origen')?.value, 10) || 0;
    const pisosDestino = parseInt(document.getElementById('pisos_destino')?.value, 10) || 0;
    total += (pisosOrigen + pisosDestino) * costoPiso;
    total += (parseInt(document.getElementById('cargadores')?.value, 10) || 0) * costoCargador;
    total += (parseInt(document.getElementById('emplaye')?.value, 10) || 0) * costoEmplaye;

    const volumen = document.getElementById('volumen')?.value;
    if (volumen === 'poco') total += 400;
    else if (volumen === 'medio') total += 900;
    else if (volumen === 'mucho') total += 1800;

    if (totalTexto) {
        totalTexto.innerText = `$${total.toFixed(2)} MXN`;
    }
}

window.calcularDistancia = calcularDistancia;
window.recalcularTotal = recalcularTotal;
