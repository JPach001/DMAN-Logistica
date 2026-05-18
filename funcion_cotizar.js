const cotizadorFields = document.querySelectorAll('#cotizador input, #cotizador select');

cotizadorFields.forEach((el) => {
    el.addEventListener('input', actualizarCotizacion);
});

async function actualizarCotizacion() {
    const origen = document.getElementById('origen')?.value ?? '';
    const destino = document.getElementById('destino')?.value ?? '';
    let km = 0;

    if (origen && destino) {
        const coordOrigen = await geocode(origen);
        const coordDestino = await geocode(destino);

        if (coordOrigen && coordDestino) {
            km = await distanciaOSRM(coordOrigen, coordDestino);
            document.getElementById('distancia-texto').innerText = `Distancia: ${km.toFixed(2)} km`;
        } else {
            document.getElementById('distancia-texto').innerText = 'Distancia: -- km';
        }
    } else {
        document.getElementById('distancia-texto').innerText = 'Distancia: -- km';
    }

    calcularTotal(km);
}

async function geocode(texto) {
    const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(texto)}`;
    try {
        const res = await fetch(url);
        const data = await res.json();
        return data[0] || null;
    } catch (err) {
        console.error(err);
        return null;
    }
}

async function distanciaOSRM(coord1, coord2) {
    try {
        const url = `https://router.project-osrm.org/route/v1/driving/${coord1.lon},${coord1.lat};${coord2.lon},${coord2.lat}?overview=false`;
        const res = await fetch(url);
        const data = await res.json();
        return (data.routes?.[0]?.distance ?? 0) / 1000;
    } catch (err) {
        console.error(err);
        return 0;
    }
}

function calcularTotal(km) {
    const costoKm = 15;
    const costoPiso = 50;
    const costoCargador = 200;
    const costoEmplaye = 30;

    let total = km * costoKm;
    const pisosOrigen = parseInt(document.getElementById('pisos_origen')?.value, 10) || 0;
    const pisosDestino = parseInt(document.getElementById('pisos_destino')?.value, 10) || 0;
    total += (pisosOrigen + pisosDestino) * costoPiso;
    total += (parseInt(document.getElementById('cargadores')?.value, 10) || 0) * costoCargador;
    total += (parseInt(document.getElementById('emplaye')?.value, 10) || 0) * costoEmplaye;

    const volumen = document.getElementById('volumen')?.value;
    if (volumen === 'poco') total += 400;
    else if (volumen === 'medio') total += 900;
    else if (volumen === 'mucho') total += 1800;

    const totalEl = document.getElementById('total');
    if (totalEl) {
        totalEl.innerText = `$${total.toFixed(2)} MXN`;
    }
}

function calcularDistancia() {
    actualizarCotizacion();
}
