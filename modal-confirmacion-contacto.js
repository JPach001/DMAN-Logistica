function handleContacto(event) {
    event.preventDefault();

    const nombre = document.getElementById('Nombre_contactar')?.value ?? '';
    const email = document.getElementById('Email_contactar')?.value ?? '';
    const tipo = document.getElementById('Tipo_flete')?.value ?? '';
    const detalles = document.getElementById('detalles_contacto')?.value ?? '';

    console.log('Formulario enviado:', { nombre, email, tipo, detalles });
    openConfirmacionModal();
    document.getElementById('contactForm')?.reset();
}

function openConfirmacionModal() {
    document.getElementById('confirmacionModal')?.classList.add('active');
    document.body.classList.add('login-modal-open');
}

function closeConfirmacionModal() {
    document.getElementById('confirmacionModal')?.classList.remove('active');
    document.body.classList.remove('login-modal-open');
}
