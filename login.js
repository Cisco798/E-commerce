// Login form validation and submission
class LoginValidator {
    constructor() {
        this.form = document.getElementById('loginForm');
        this.emailField = document.getElementById('email');
        this.passwordField = document.getElementById('password');
        this.submitBtn = document.getElementById('submitBtn');
        this.messageContainer = document.getElementById('messageContainer');
        
        // Regular expressions for validation
        this.emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        this.passwordRegex = /^.{6,}$/; // At least 6 characters
        
        this.init();
    }
    
    init() {
        if (this.form) {
            this.form.addEventListener('submit', (e) => this.handleSubmit(e));
            
            // Real-time validation
            if (this.emailField) {
                this.emailField.addEventListener('blur', () => this.validateEmail());
                this.emailField.addEventListener('input', () => this.clearError('email'));
            }
            
            if (this.passwordField) {
                this.passwordField.addEventListener('blur', () => this.validatePassword());
                this.passwordField.addEventListener('input', () => this.clearError('password'));
            }
        }
    }
    
    validateEmail() {
        const email = this.emailField.value.trim();
        
        if (!email) {
            this.showFieldError('email', 'Email is required');
            return false;
        }
        
        if (!this.emailRegex.test(email)) {
            this.showFieldError('email', 'Please enter a valid email address');
            return false;
        }
        
        this.clearFieldError('email');
        return true;
    }
    
    validatePassword() {
        const password = this.passwordField.value;
        
        if (!password) {
            this.showFieldError('password', 'Password is required');
            return false;
        }
        
        if (!this.passwordRegex.test(password)) {
            this.showFieldError('password', 'Password must be at least 6 characters long');
            return false;
        }
        
        this.clearFieldError('password');
        return true;
    }
    
    validateForm() {
        const isEmailValid = this.validateEmail();
        const isPasswordValid = this.validatePassword();
        
        return isEmailValid && isPasswordValid;
    }
    
    showFieldError(fieldName, message) {
        const field = document.getElementById(fieldName);
        const errorElement = document.getElementById(`${fieldName}Error`);
        
        if (field) {
            field.classList.add('error', 'border-red-500');
        }
        
        if (errorElement) {
            errorElement.textContent = message;
            errorElement.style.display = 'block';
        }
    }
    
    clearFieldError(fieldName) {
        const field = document.getElementById(fieldName);
        const errorElement = document.getElementById(`${fieldName}Error`);
        
        if (field) {
            field.classList.remove('error', 'border-red-500');
        }
        
        if (errorElement) {
            errorElement.textContent = '';
            errorElement.style.display = 'none';
        }
    }
    
    clearError(fieldName) {
        this.clearFieldError(fieldName);
    }
    
    showMessage(message, type = 'error') {
        if (this.messageContainer) {
            this.messageContainer.innerHTML = `
                <div class="alert alert-${type} p-3 mb-4 rounded ${type === 'success' ? 'bg-green-100 text-green-800 border-green-300' : 'bg-red-100 text-red-800 border-red-300'} border">
                    ${message}
                </div>
            `;
        }
    }
    
    clearMessages() {
        if (this.messageContainer) {
            this.messageContainer.innerHTML = '';
        }
    }
    
    setLoading(isLoading) {
        if (this.submitBtn) {
            if (isLoading) {
                this.submitBtn.disabled = true;
                this.submitBtn.innerHTML = `
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    Logging in...
                `;
            } else {
                this.submitBtn.disabled = false;
                this.submitBtn.innerHTML = 'Login';
            }
        }
    }
    
// Login form validation and submission
class LoginValidator {
    constructor() {
        this.form = document.getElementById('loginForm');
        this.emailField = document.getElementById('email');
        this.passwordField = document.getElementById('password');
        this.submitBtn = document.getElementById('submitBtn');
        this.messageContainer = document.getElementById('messageContainer');
        
        // Regular expressions for validation
        this.emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        this.passwordRegex = /^.{6,}$/; // At least 6 characters
        
        this.init();
    }
    
    init() {
        if (this.form) {
            this.form.addEventListener('submit', (e) => this.handleSubmit(e));
            
            // Real-time validation
            if (this.emailField) {
                this.emailField.addEventListener('blur', () => this.validateEmail());
                this.emailField.addEventListener('input', () => this.clearError('email'));
            }
            
            if (this.passwordField) {
                this.passwordField.addEventListener('blur', () => this.validatePassword());
                this.passwordField.addEventListener('input', () => this.clearError('password'));
            }
        }
    }
    
    validateEmail() {
        const email = this.emailField.value.trim();
        
        if (!email) {
            this.showFieldError('email', 'Email is required');
            return false;
        }
        
        if (!this.emailRegex.test(email)) {
            this.showFieldError('email', 'Please enter a valid email address');
            return false;
        }
        
        this.clearFieldError('email');
        return true;
    }
    
    validatePassword() {
        const password = this.passwordField.value;
        
        if (!password) {
            this.showFieldError('password', 'Password is required');
            return false;
        }
        
        if (!this.passwordRegex.test(password)) {
            this.showFieldError('password', 'Password must be at least 6 characters long');
            return false;
        }
        
        this.clearFieldError('password');
        return true;
    }
    
    validateForm() {
        const isEmailValid = this.validateEmail();
        const isPasswordValid = this.validatePassword();
        
        return isEmailValid && isPasswordValid;
    }
    
    showFieldError(fieldName, message) {
        const field = document.getElementById(fieldName);
        const errorElement = document.getElementById(`${fieldName}Error`);
        
        if (field) {
            field.classList.add('error', 'border-red-500');
        }
        
        if (errorElement) {
            errorElement.textContent = message;
            errorElement.style.display = 'block';
        }
    }
    
    clearFieldError(fieldName) {
        const field = document.getElementById(fieldName);
        const errorElement = document.getElementById(`${fieldName}Error`);
        
        if (field) {
            field.classList.remove('error', 'border-red-500');
        }
        
        if (errorElement) {
            errorElement.textContent = '';
            errorElement.style.display = 'none';
        }
    }
    
    clearError(fieldName) {
        this.clearFieldError(fieldName);
    }
    
    showMessage(message, type = 'error') {
        if (this.messageContainer) {
            const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
            this.messageContainer.innerHTML = `
                <div class="alert ${alertClass} p-3 mb-4 rounded">
                    ${message}
                </div>
            `;
        }
    }
    
    clearMessages() {
        if (this.messageContainer) {
            this.messageContainer.innerHTML = '';
        }
    }
    
    setLoading(isLoading) {
        if (this.submitBtn) {
            if (isLoading) {
                this.submitBtn.disabled = true;
                this.submitBtn.innerHTML = 'Logging in...';
            } else {
                this.submitBtn.disabled = false;
                this.submitBtn.innerHTML = 'Login';
            }
        }
    }
    
    async handleSubmit(e) {
        e.preventDefault();
        
        this.clearMessages();
        
        // Validate form
        if (!this.validateForm()) {
            return;
        }
        
        // Prepare form data using FormData (to match register pattern)
        const formData = new FormData();
        formData.append('email', this.emailField.value.trim());
        formData.append('password', this.passwordField.value);
        
        this.setLoading(true);
        
        try {
            // Asynchronously invoke login_user_action.php
            const response = await fetch('actions/login_user_action.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.status === 'success') {
                this.showMessage(result.message, 'success');
                
                // Redirect to home page after successful login
                setTimeout(() => {
                    window.location.href = 'index.php';
                }, 1500);
                
            } else {
                this.showMessage(result.message);
            }
            
        } catch (error) {
            console.error('Login error:', error);
            this.showMessage('An error occurred. Please try again later.');
        } finally {
            this.setLoading(false);
        }
    }
}

// Initialize login validator when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    new LoginValidator();
});

// Alternative initialization for immediate execution
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        new LoginValidator();
    });
} else {
    new LoginValidator();
}
}

// Initialize login validator when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    new LoginValidator();
});

// Alternative initialization for immediate execution
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        new LoginValidator();
    });
} else {
    new LoginValidator();
}
