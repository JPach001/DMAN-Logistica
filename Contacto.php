<?php
$pageTitle = 'DMAN Logistica | Contacto';
$pageDescription = 'Formulario de contacto para cotizaciones, dudas y seguimiento.';
$activePage = 'contacto';
$pageScripts = ['modal-confirmacion-contacto.js', 'login-modal.js'];
require __DIR__ . '/includes/site-header.php';
?>

<main>
    <section class="columna">
        <h3 class="titulo1">CONTACTANOS</h3>
        <h2 class="titulo2">Hablemos de tu siguiente mudanza.</h2>
        <p class="muted">Cotizaciones rapidas. Atencion por WhatsApp, llamada o correo.</p>

        <div class="info-contacto">
            <div class="icono-contacto">
                <img width="34" src="img/icon_mail.png" alt="Email">
            </div>
            <div class="texto-contacto">
                <p class="label-contacto">Email</p>
                <p class="valor-contacto">dmanlogistica@gmail.com</p>
            </div>
        </div>

        <div class="info-contacto">
            <div class="icono-contacto">
                <img width="28" src="img/icon_telefono.png" alt="Telefono">
            </div>
            <div class="texto-contacto">
                <p class="label-contacto">Telefono</p>
                <p class="valor-contacto">(442) 567-0416</p>
            </div>
        </div>

        <p class="label-contacto" style="margin-top:20px;">Siguenos en</p>
        <a href="https://www.facebook.com/profile.php?id=61579422893176" target="_blank" rel="noreferrer">
            <img width="30" src="img/img_facebook_oscuro.png" alt="Facebook">
        </a>
        <a href="https://wa.me/4425670416" target="_blank" rel="noreferrer">
            <img width="30" src="img/img_whats_oscuro.png" alt="WhatsApp">
        </a>
    </section>

    <section class="columna">
        <form id="contactForm" class="contact-form" onsubmit="handleContacto(event)">
            <label for="Nombre_contactar">Nombre completo</label>
            <input type="text" id="Nombre_contactar" placeholder="Nombre" required>

            <label for="Email_contactar">Email</label>
            <input type="email" id="Email_contactar" placeholder="email@address.com" required>

            <label for="Tipo_flete">Tipo de flete</label>
            <select id="Tipo_flete">
                <option value="local">Local</option>
                <option value="estado">Estatal</option>
                <option value="nacional">Nacional</option>
            </select>

            <label for="detalles_contacto">Detalles</label>
            <textarea id="detalles_contacto" placeholder="Escribe tu mensaje aqui..." required></textarea>

            <div class="cta-strip">
                <button type="submit" class="btnVerde">Enviar mensaje</button>
                <a class="btnCotizar" href="formulario_cotizar.php">Ir a cotizacion</a>
            </div>
        </form>
    </section>
</main>

<?php require __DIR__ . '/includes/contact-confirmation-modal.php'; ?>
<?php require __DIR__ . '/includes/site-login-modal.php'; ?>
<?php require __DIR__ . '/includes/site-footer.php'; ?>
