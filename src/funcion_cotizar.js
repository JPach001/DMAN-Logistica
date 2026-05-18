document.querySelectorAll('#cotizador input, #cotizador select').forEach(el => {
            el.addEventListener('input', actualizarCotizacion);
        });
        async function actualizarCotizacion() {
            const origen = document.getElementById("origen").value;
            const destino = document.getElementById("destino").value;
            let km = 0; // valor por defecto si no hay dirección
            if (origen && destino) {
                const coordOrigen = await geocode(origen);
                const coordDestino = await geocode(destino);
                if (coordOrigen && coordDestino) {
                    km = await distanciaOSRM(coordOrigen, coordDestino);
                    document.getElementById("distancia-texto").innerText = `Distancia: ${km.toFixed(2)} km`;
                } else {
                    document.getElementById("distancia-texto").innerText = "Distancia: -- km";
                }
            } else {
                document.getElementById("distancia-texto").innerText = "Distancia: -- km";
            }
            calcularTotal(km);
        }

        // Función para geocodificar usando Nominatim
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
        // Función para obtener distancia en km con OSRM
        async function distanciaOSRM(coord1, coord2) {
            try {
                const url = `https://router.project-osrm.org/route/v1/driving/${coord1.lon},${coord1.lat};${coord2.lon},${coord2.lat}?overview=false`;
                const res = await fetch(url);
                const data = await res.json();
                return data.routes[0].distance / 1000; // km
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
            let total = 0;
            // Distancia
            total += km * costoKm;
            // Pisos
            const pisosOrigen = parseInt(document.getElementById("pisos_origen").value) || 0;
            const pisosDestino = parseInt(document.getElementById("pisos_destino").value) || 0;
            total += (pisosOrigen + pisosDestino) * costoPiso;
            // Cargadores
            total += (parseInt(document.getElementById("cargadores").value) || 0) * costoCargador;
            // Emplaye
            total += (parseInt(document.getElementById("emplaye").value) || 0) * costoEmplaye;
            // Volumen
            const volumen = document.getElementById("volumen").value;
            if (volumen === "poco") total += 400;
            else if (volumen === "medio") total += 900;
            else if (volumen === "mucho") total += 1800;
            document.getElementById("total").innerText = `$${total.toFixed(2)} MXN`;
        }