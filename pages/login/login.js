// Form validation
const loginForm = document.getElementById('loginForm');
const emailInput = document.getElementById('email');
const passwordInput = document.getElementById('password');
const emailGroup = document.getElementById('emailGroup');
const passwordGroup = document.getElementById('passwordGroup');

// Email validation regex
const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

// Validate email
function validateEmail() {
    const email = emailInput.value.trim();
    if (email === '') {
        emailGroup.classList.remove('success');
        emailGroup.classList.add('error');
        return false;
    } else if (!emailRegex.test(email)) {
        emailGroup.classList.remove('success');
        emailGroup.classList.add('error');
        return false;
    } else {
        emailGroup.classList.remove('error');
        emailGroup.classList.add('success');
        return true;
    }
}

// Validate password
function validatePassword() {
    const password = passwordInput.value.trim();
    if (password === '' || password.length < 6) {
        passwordGroup.classList.remove('success');
        passwordGroup.classList.add('error');
        return false;
    } else {
        passwordGroup.classList.remove('error');
        passwordGroup.classList.add('success');
        return true;
    }
}

// Real-time validation
emailInput.addEventListener('input', validateEmail);
passwordInput.addEventListener('input', validatePassword);

// Form submission
loginForm.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const isEmailValid = validateEmail();
    const isPasswordValid = validatePassword();
    
    if (isEmailValid && isPasswordValid) {
        // Form is valid - simulate login
        const submitBtn = document.querySelector('.submit-btn');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Signing In...';
        submitBtn.disabled = true;
        
        // Simulate API call
        setTimeout(() => {
            alert('Login successful! Welcome back.');
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
            
            // Reset form
            loginForm.reset();
            emailGroup.classList.remove('success');
            passwordGroup.classList.remove('success');
        }, 1500);
    } else {
        // Form is invalid
        if (!isEmailValid) validateEmail();
        if (!isPasswordValid) validatePassword();
    }
});

// Social login buttons
document.querySelectorAll('.social-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const platform = this.classList.contains('google') ? 'Google' : 
                       this.classList.contains('facebook') ? 'Facebook' : 'Twitter';
        alert(`Redirecting to ${platform} login...`);
    });
});

// Forgot password link
document.querySelector('.forgot-password').addEventListener('click', function(e) {
    e.preventDefault();
    const email = prompt('Please enter your email to reset password:');
    if (email && emailRegex.test(email)) {
        alert(`Password reset link has been sent to ${email}`);
    } else if (email) {
        alert('Please enter a valid email address');
    }
});