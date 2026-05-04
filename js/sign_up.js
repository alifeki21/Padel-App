document.addEventListener('DOMContentLoaded', function() {
    const signupForm = document.getElementById('signupForm');
    const patterns = {
        email: /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/,
        phone: /^[\+]?[1-9][\d]{0,15}$/, 
        password: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/
    };
    
    const fields = [
        {
            id: 'firstName',
            groupId: 'firstNameGroup',
            validate: (value) => {
                if (!value.trim()) return 'First name is required';
                if (value.length < 2) return 'First name must be at least 2 characters';
                return '';
            }
        },
        {
            id: 'lastName',
            groupId: 'lastNameGroup',
            validate: (value) => {
                if (!value.trim()) return 'Last name is required';
                if (value.length < 2) return 'Last name must be at least 2 characters';
                return '';
            }
        },
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
            id: 'phone',
            groupId: 'phoneGroup',
            validate: (value) => {
                if (!value.trim()) return 'Phone number is required';
                if (!patterns.phone.test(value.replace(/[^\d+]/g, ''))) return 'Please enter a valid phone number';
                return '';
            }
        },
        {
            id: 'password',
            groupId: 'passwordGroup',
            validate: (value) => {
                if (!value) return 'Password is required';
                if (value.length < 8) return 'Password must be at least 8 characters';
                if (!patterns.password.test(value)) return 'Password must contain uppercase, lowercase, number and special character';
                return '';
            }
        },
        {
            id: 'confirmPassword',
            groupId: 'confirmPasswordGroup',
            validate: (value) => {
                const password = document.getElementById('password').value;
                if (!value) return 'Please confirm your password';
                if (value !== password) return 'Passwords do not match';
                return '';
            }
        },
        {
            id: 'level',
            groupId: 'levelGroup',
            validate: (value) => {
                if (!value) return 'Skill level is required';
                const level = parseFloat(value);
                if (isNaN(level) || level < 1 || level > 10) return 'Please enter a number between 1 and 10';
                return '';
            }
        },
        {
            id: 'position',
            groupId: 'positionGroup',
            validate: (value) => {
                if (!value) return 'Please select your preferred position';
                return '';
            }
        },
        {
            id: 'hand',
            groupId: 'handGroup',
            validate: (value) => {
                if (!value) return 'Please select your playing hand';
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
        if (input) {
            input.addEventListener('input', () => validateField(field));
            input.addEventListener('blur', () => validateField(field));
        }
    });
    function addPasswordToggle() {
        const input = document.getElementById('password');
        const group = document.getElementById('passwordGroup');

        const input2 = document.getElementById('confirmPassword');
        const group2 = document.getElementById('confirmPasswordGroup');
    
        const eyeIcon = document.createElement('i');
        eyeIcon.className = 'fas fa-eye password-toggle';

        const eyeIcon2 = document.createElement('i');
        eyeIcon2.className = 'fas fa-eye password-toggle';

        eyeIcon.addEventListener('click', function() {
            if (input.type === 'password') {
                input.type = 'text';
                eyeIcon.className = 'fas fa-eye-slash password-toggle';
            } else {
                input.type = 'password';
                eyeIcon.className = 'fas fa-eye password-toggle';
            }
        });

    eyeIcon2.addEventListener('click', function() {
            if (input2.type === 'password') {
                input2.type = 'text';
                eyeIcon2.className = 'fas fa-eye-slash password-toggle';
            } else {
                input2.type = 'password';
                eyeIcon2.className = 'fas fa-eye password-toggle';
            }
        });

    
    group.querySelector('.input-with-icon').appendChild(eyeIcon);

    group2.querySelector('.input-with-icon').appendChild(eyeIcon2);


    eyeIcon.style.position = 'absolute';
    eyeIcon.style.left = '300px';
    eyeIcon.style.transform = 'translateX(95%)';
    eyeIcon.style.top = '19px';
    eyeIcon.style.cursor = 'pointer';

    eyeIcon2.style.position = 'absolute';
    eyeIcon2.style.left = '300px';
    eyeIcon2.style.transform = 'translateX(95%)';
    eyeIcon2.style.top = '19px';
    eyeIcon2.style.cursor = 'pointer';

  

    }
    
    addPasswordToggle();

    // The form is submitted to php/sign_up.php (server-side handles
    // creation + redirect). We only do client-side validation and
    // block submission if it fails.
    signupForm.addEventListener('submit', function(e) {
        let isValid = true;
        fields.forEach(field => {
            if (!validateField(field)) {
                isValid = false;
            }
        });

        const termsCheckbox = document.getElementById('terms');
        const termsError    = document.getElementById('termsError');
        if (!termsCheckbox.checked) {
            termsError.style.display = 'block';
            isValid = false;
        } else {
            termsError.style.display = 'none';
        }

        if (!isValid) {
            e.preventDefault();
            const firstError = document.querySelector('.form-group.error');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            return;
        }

        // Show a spinner while the browser is posting to PHP.
        const submitBtn = document.querySelector('.submit-btn');
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating Account...';
        submitBtn.disabled  = true;
    });
    

});