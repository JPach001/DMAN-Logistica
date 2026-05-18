<div id="loginModal" class="login-modal-overlay" aria-hidden="true">
    <div class="login-modal-container">
        <button class="login-modal-close" type="button" onclick="closeLoginModal()">&times;</button>
        <div class="login-modal-content">
            <h2 class="login-modal-title">Iniciar sesión</h2>
            <p class="login-modal-subtitle">Ingresa tu correo electrónico y tu contraseña para continuar.</p>
            <form id="loginForm" onsubmit="handleLogin(event)">
                <div class="login-form-group">
                    <label for="emailInput">Email</label>
                    <div class="login-input-wrapper">
                        <span class="login-input-icon" aria-hidden="true">📧</span>
                        <input type="email" id="emailInput" placeholder="email@address.com" required>
                    </div>
                </div>
                <div class="login-form-group">
                    <label for="passwordInput">Contraseña</label>
                    <div class="login-input-wrapper">
                        <span class="login-input-icon" aria-hidden="true">🔒</span>
                        <input type="password" id="passwordInput" placeholder="••••••••••" required>
                        <button type="button" class="login-password-toggle" onclick="togglePassword()" aria-label="Mostrar u ocultar contraseña">
                            <span id="eyeIcon">👁️</span>
                        </button>
                    </div>
                </div>
                <div class="login-forgot-password">
                    <a href="#" onclick="return false;">¿Olvidaste tu contraseña?</a>
                </div>
                <button type="submit" class="login-btn-login">Iniciar sesión</button>
                <div class="login-remember-me">
                    <input type="checkbox" id="rememberMe">
                    <label for="rememberMe">Recordarme</label>
                </div>
                <div class="login-divider">
                    <span>o</span>
                </div>
                <button type="button" class="login-btn-google" onclick="loginWithGoogle()">
                    <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath fill='%234285F4' d='M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z'/%3E%3Cpath fill='%2334A853' d='M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z'/%3E%3Cpath fill='%23FBBC05' d='M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z'/%3E%3Cpath fill='%23EA4335' d='M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z'/%3E%3C/svg%3E"
                         alt="Google" width="20">
                    Iniciar sesión con Google
                </button>
                <p class="login-register-link">
                    ¿Aún no tienes cuenta? <a href="#" onclick="openRegisterModal(); return false;">Registrarse</a>
                </p>
            </form>
        </div>
    </div>
</div>
