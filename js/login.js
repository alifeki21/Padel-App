document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const patterns = {
        email: /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/
    };
    const fields = [
        {
            id: 'email',
            groupId: 'emailGroup',
            validate: (value) => {
                if (!value.trim()) return 'Email is required';
                if (!patterns.email.test(value)) return 'Please enter a valid email address';
                return '';
            }
        },
        {
            id: 'password',
            groupId: 'passwordGroup',
            validate: (value) => {
                if (!value) return 'Password is required';
                return '';
            }
        }
    ];

    function validateField(field) {
        const input = document.getElementById(field.id);
        const group = document.getElementById(field.groupId);
        const value = input.value;
        const errorMessage = field.validate(value);

        if (errorMessage) {
            group.classList.remove('success');
            group.classList.add('error');
            const errorElement = group.querySelector('.error-message');
            errorElement.textContent = errorMessage;
            return false;
        } else {
            group.classList.remove('error');
            group.classList.add('success');
            return true;
        }
    }

    fields.forEach(field => {
        const input = document.getElementById(field.id);
        input.addEventListener('input', () => validateField(field));
        input.addEventListener('blur', () => validateField(field));
    });

    function addPasswordToggle() {
        const input = document.getElementById('password');
        const group = document.getElementById('passwordGroup');
        const eyeIcon = document.createElement('i');
        eyeIcon.className = 'fas fa-eye password-toggle';
        eyeIcon.addEventListener('click', function() {
            if (input.type === 'password') {
                input.type = 'text';
                eyeIcon.className = 'fas fa-eye-slash password-toggle';
            } else {
                input.type = 'password';
                eyeIcon.className = 'fas fa-eye password-toggle';
            }
        });
        group.querySelector('.input-with-icon').appendChild(eyeIcon);
    }

    addPasswordToggle();

    function checkRememberMe() {
        const rememberedEmail = localStorage.getItem('rememberedEmail');
        const rememberedPassword = localStorage.getItem('rememberedPassword');
        const rememberCheckbox = document.getElementById('remember');
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        
        if (rememberedEmail) {
            emailInput.value = rememberedEmail;
            validateField(fields[0]);
            rememberCheckbox.checked = true;
        } else {
            emailInput.value = '';
        }
        
        if (rememberedPassword) {
            passwordInput.value = rememberedPassword;
            validateField(fields[1]);
            rememberCheckbox.checked = true;
        } else {
            passwordInput.value = '';
        }
    }

    checkRememberMe();

    // The form is submitted to php/login.php (server-side handles
    // authentication + redirect). We only do client-side validation
    // and "remember me" handling here.
    loginForm.addEventListener('submit', function(e) {
        let isValid = true;

        fields.forEach(field => {
            if (!validateField(field)) {
                isValid = false;
            }
        });

        if (!isValid) {
            e.preventDefault();
            const firstError = document.querySelector('.form-group.error');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            return;
        }

        const email          = document.getElementById('email').value;
        const password       = document.getElementById('password').value;
        const rememberMe     = document.getElementById('remember').checked;

        if (rememberMe) {
            localStorage.setItem('rememberedEmail', email);
            localStorage.setItem('rememberedPassword', password);
        } else {
            localStorage.removeItem('rememberedEmail');
            localStorage.removeItem('rememberedPassword');
        }

        const submitBtn = document.querySelector('.submit-btn');
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Logging In...';
        submitBtn.disabled  = true;
        // Do NOT preventDefault: let the browser POST to login.php.
    });

    const style = document.createElement('style');
    style.textContent = `
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }
    `;
    document.head.appendChild(style);
});