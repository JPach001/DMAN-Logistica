function handleContacto(event) {
    event.preventDefault();
    // Lógica para enviar al backend
    // Por ahora solo mostramos el modal de confirmación
    const nombre = document.getElementById('Nombre_contactar').value;
    const email = document.getElementById('Email_contactar').value;
    const tipo = document.getElementById('Tipo_flete').value;
    const detalles = document.getElementById('detalles_contacto').value;
    console.log('Formulario enviado:', { nombre, email, tipo, detalles });
    openConfirmacionModal();
    // Limpiar el formulario
    document.getElementById('contactForm').reset();
    
    // CUANDO SE TENGA BACKEND usar:
    /*
    fetch('/api/contacto', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ nombre, email, tipo, detalles })
    })
    .then(response => response.json())
    .then(data => {
        openConfirmacionModal();
        document.getElementById('contactForm').reset();
    })
    .catch(error => {
        alert('Error al enviar el mensaje');
    });
    */
}
// Abrir modal
function openConfirmacionModal() {
    document.getElementById('confirmacionModal').classList.add('active');
    document.body.classList.add('login-modal-open');
}
// Cerrar modal
function closeConfirmacionModal() {
    document.getElementById('confirmacionModal').classList.remove('active');
    document.body.classList.remove('login-modal-open');
}