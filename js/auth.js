document.addEventListener('DOMContentLoaded', function() {
    console.log('Auth script loaded');
    fetch('../php/get_user.php')
    .then(response => response.json())
    .then(result => {
        console.log('User status:', result.status);
        const navList = document.querySelector('.navbar-nav');
        if (!navList) return;

        if (result.status === 'success') {
            // Remove existing Login/Signup links
            const loginLink = navList.querySelector('a[href="login.html"]');
            const signupLink = navList.querySelector('a[href="sign_up.html"]');
            if (loginLink && loginLink.parentElement) loginLink.parentElement.remove();
            if (signupLink && signupLink.parentElement) signupLink.parentElement.remove();

            // Create User Dropdown
            const userItem = document.createElement('li');
            userItem.className = 'nav-item custom-user-nav';
            userItem.style.listStyle = 'none';
            userItem.innerHTML = `
                <a class="nav-link dropdown-toggle" href="javascript:void(0)" id="userDropdown" style="background-color: #3e1bbf !important; color: white !important; border-radius: 12px; padding: 0.6rem 1.5rem; display: flex; align-items: center; gap: 8px; text-decoration: none;">
                    <i class="fas fa-user-circle"></i> ${result.user.name}
                </a>
                <div class="dropdown-menu" id="userMenu" style="position: absolute; top: 100%; right: 0; min-width: 240px; background: white; border-radius: 12px; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); padding: 10px 0; margin-top: 10px; list-style: none !important; display: none; z-index: 1001; border: 1px solid rgba(0,0,0,0.05);">
                    <div style="padding: 12px 20px; border-bottom: 1px solid rgba(0,0,0,0.05); margin-bottom: 5px;">
                        <span style="display: block; font-size: 0.8rem; color: #64748b;">Connecté en tant que</span>
                        <strong style="display: block; font-size: 0.9rem; color: #0f172a; word-break: break-all;">${result.user.email}</strong>
                    </div>
                    <a class="dropdown-item" href="profile.html" style="display: flex; align-items: center; padding: 10px 20px; color: #0f172a; text-decoration: none; font-size: 0.95rem;">
                        <i class="fas fa-id-card" style="width: 20px; margin-right: 10px; color: #3e1bbf;"></i> Mon Profil
                    </a>
                    <a class="dropdown-item" href="reservations_list.html" style="display: flex; align-items: center; padding: 10px 20px; color: #0f172a; text-decoration: none; font-size: 0.95rem;">
                        <i class="fas fa-calendar-check" style="width: 20px; margin-right: 10px; color: #3e1bbf;"></i> Mes Réservations
                    </a>
                    <div style="height: 1px; background: rgba(0,0,0,0.05); margin: 5px 0;"></div>
                    <a class="dropdown-item" href="../php/logout.php" style="display: flex; align-items: center; padding: 10px 20px; color: #ef4444; text-decoration: none; font-size: 0.95rem;">
                        <i class="fas fa-sign-out-alt" style="width: 20px; margin-right: 10px; color: #ef4444;"></i> Déconnexion
                    </a>
                </div>
            `;
            navList.appendChild(userItem);

            // Manual Dropdown Logic
            const dropdownBtn = userItem.querySelector('#userDropdown');
            const dropdownMenu = userItem.querySelector('#userMenu');

            dropdownBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                const isShown = dropdownMenu.style.display === 'block';
                dropdownMenu.style.display = isShown ? 'none' : 'block';
            });

            document.addEventListener('click', () => {
                dropdownMenu.style.display = 'none';
            });

            // Hover effects for menu items (manual JS style injection)
            const items = dropdownMenu.querySelectorAll('.dropdown-item');
            items.forEach(item => {
                item.addEventListener('mouseenter', () => {
                    if (!item.classList.contains('text-danger')) {
                        item.style.backgroundColor = 'rgba(62, 27, 191, 0.05)';
                    }
                });
                item.addEventListener('mouseleave', () => {
                    item.style.backgroundColor = 'transparent';
                });
            });
        }
    })
    .catch(error => console.error('Auth check error:', error));
});


