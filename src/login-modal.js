function openLoginModal() {
    document.getElementById('loginModal').classList.add('active');
    document.body.classList.add('login-modal-open');
}

// Cerrar modal
function closeLoginModal() {
    document.getElementById('loginModal').classList.remove('active');
    document.body.classList.remove('login-modal-open');
}

// Cerrar al hacer clic fuera del modal
document.getElementById('loginModal')?.addEventListener('click', function (e) {
    if (e.target === this) {
        closeLoginModal();
    }
});

// Cerrar formulario con ESC
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        closeLoginModal();
    }
});

// Mostrar/ocultar contraseña
function togglePassword() {
    const passwordInput = document.getElementById('passwordInput');
    const eyeIcon = document.getElementById('eyeIcon');

    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.textContent = '👁️‍🗨️';
    } else {
        passwordInput.type = 'password';
        eyeIcon.textContent = '👁️';
    }
}

// Envío del formulario
function handleLogin(event) {
    event.preventDefault();

    const email = document.getElementById('emailInput').value;
    const password = document.getElementById('passwordInput').value;
    const rememberMe = document.getElementById('rememberMe').checked;

    // AQUÍ SE CONECTARÁ EL BACK
    console.log('Datos de login:', { email, password, rememberMe });

    // Ejemplo de lo que se hará después con fetch:
    /*
    fetch('/api/login', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ email, password, rememberMe })
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        // Redirigir o guardar sesión
        window.location.href = '/dashboard';
      } else {
        alert('Credenciales incorrectas');
      }
    });
    */

    // Por ahora solo mostramos un alert
    alert('Login enviado. Conectar con backend próximamente.');
}

// Login con Google
function loginWithGoogle() {
    // AQUÍ SE INTEGRARÁ GOOGLE OAUTH
    console.log('Login con Google');
    alert('Integración con Google OAuth pendiente');
}