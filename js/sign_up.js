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
})