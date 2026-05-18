function openLoginModal() {
    document.getElementById('loginModal')?.classList.add('active');
    document.body.classList.add('login-modal-open');
}

function closeLoginModal() {
    document.getElementById('loginModal')?.classList.remove('active');
    document.body.classList.remove('login-modal-open');
}

document.getElementById('loginModal')?.addEventListener('click', function (e) {
    if (e.target === this) {
        closeLoginModal();
    }
});

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        closeLoginModal();
        if (typeof closeConfirmacionModal === 'function') {
            closeConfirmacionModal();
        }
    }
});

function togglePassword() {
    const passwordInput = document.getElementById('passwordInput');
    const eyeIcon = document.getElementById('eyeIcon');

    if (!passwordInput || !eyeIcon) {
        return;
    }

    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.textContent = '👁️‍🗨️';
    } else {
        passwordInput.type = 'password';
        eyeIcon.textContent = '👁️';
    }
}

function handleLogin(event) {
    event.preventDefault();

    const email = document.getElementById('emailInput')?.value ?? '';
    const rememberMe = document.getElementById('rememberMe')?.checked ?? false;
    console.log('Datos de login:', {
        email,
        rememberMe,
    });

    alert('El login se conectara al backend cuando la base de datos este lista.');
}

function loginWithGoogle() {
    alert('Integracion con Google OAuth pendiente.');
}

function openRegisterModal() {
    alert('El registro se conectara al backend en la siguiente etapa.');
}
