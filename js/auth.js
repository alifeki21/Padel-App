document.addEventListener('DOMContentLoaded', function() {
    fetch('../php/get_user.php')
    .then(response => response.json())
    .then(result => {
        const navList = document.querySelector('.navbar-nav');
        if (!navList) return;

        const isLoginPage = window.location.pathname.includes('login.html');
        const isSignupPage = window.location.pathname.includes('sign_up.html');

        if (result.status === 'success') {
            // Logged in
            if (isLoginPage || isSignupPage) {
                window.location.href = 'acceuil.html';
                return;
            }

            // Replace Login and Signup with User Menu
            const loginLink = navList.querySelector('a[href="login.html"]');
            const signupLink = navList.querySelector('a[href="sign_up.html"]');
            if (loginLink) loginLink.parentElement.remove();
            if (signupLink) signupLink.parentElement.remove();

            const userItem = document.createElement('li');
            userItem.className = 'nav-item dropdown custom-user-nav';
            userItem.innerHTML = `
                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-user-circle"></i> ${result.user.name}
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0" aria-labelledby="userDropdown">
                    <li class="px-3 py-2 text-muted small border-bottom">Connecté en tant que<br><strong class="text-dark">${result.user.email}</strong></li>
                    <li><a class="dropdown-item mt-1" href="profile.html"><i class="fas fa-id-card me-2"></i> Mon Profil</a></li>
                    <li><a class="dropdown-item" href="reservations_list.html"><i class="fas fa-calendar-check me-2"></i> Mes Réservations</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="../php/logout.php"><i class="fas fa-sign-out-alt me-2"></i> Déconnexion</a></li>
                </ul>
            `;
            navList.appendChild(userItem);
        }
    })
    .catch(error => console.error('Auth check error:', error));
});
