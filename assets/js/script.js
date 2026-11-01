
// AJAX
//registration  
document.addEventListener('DOMContentLoaded', function() {
    const registerForm = document.getElementById('registerForm');
    if (!registerForm) return;

    const ajaxMessage = document.getElementById('ajax-message');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm_password');
    
    const togglePasswordBtn = document.getElementById('togglePassword');
    const eyeIcon1 = document.getElementById('eyeIcon1');
    
    const toggleConfirmPasswordBtn = document.getElementById('toggleConfirmPassword');
    const eyeIcon2 = document.getElementById('eyeIcon2');

    // show password eye
    if (togglePasswordBtn) {
        togglePasswordBtn.addEventListener('click', function() {
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';
            eyeIcon1.classList.toggle('bi-eye');
            eyeIcon1.classList.toggle('bi-eye-slash');
        });
    }

    // show confirm password eye
    if (toggleConfirmPasswordBtn) {
        toggleConfirmPasswordBtn.addEventListener('click', function() {
            const isPassword = confirmPasswordInput.type === 'password';
            confirmPasswordInput.type = isPassword ? 'text' : 'password';
            eyeIcon2.classList.toggle('bi-eye');
            eyeIcon2.classList.toggle('bi-eye-slash');
        });
    }

    // AJAX form submission
    registerForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const username = document.getElementById('username').value.trim();
        const email = document.getElementById('email').value.trim();
        const password = passwordInput.value;
        const confirmPassword = confirmPasswordInput.value;

        if (!username || !email || !password || !confirmPassword) {
            ajaxMessage.innerHTML = "<div class='alert alert-danger'>Please fill in all required fields.</div>";
            return;
        }

        if (password !== confirmPassword) {
            ajaxMessage.innerHTML = "<div class='alert alert-danger'>Passwords do not match.</div>";
            return;
        }

        const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;
        if (!passwordPattern.test(password)) {
            ajaxMessage.innerHTML = "<div class='alert alert-danger'>Password must be 8+ characters with uppercase, lowercase, digit & special character.</div>";
            return;
        }

        const formData = new FormData(registerForm);

        fetch('register.php', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                ajaxMessage.innerHTML = `<div class='alert alert-success'>${data.message}</div>`;
                registerForm.reset();
                setTimeout(() => {
                    window.location.href = 'login.html';
                }, 2000);
            } else {
                ajaxMessage.innerHTML = `<div class='alert alert-danger'>${data.message}</div>`;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            ajaxMessage.innerHTML = "<div class='alert alert-danger'>An unexpected error occurred. Please try again.</div>";
        });
    });
});

// log in 
const loginForm = document.getElementById('loginForm');
if (loginForm) {
    const loginAjaxMessage = document.getElementById('ajax-message');
    const loginPasswordInput = document.getElementById('password');
    const toggleLoginPasswordBtn = document.getElementById('togglePassword');
    const eyeIcon = document.getElementById('eyeIcon');

    // show password eye
    if (toggleLoginPasswordBtn) {
        toggleLoginPasswordBtn.addEventListener('click', function() {
            const isPassword = loginPasswordInput.type === 'password';
            loginPasswordInput.type = isPassword ? 'text' : 'password';
            eyeIcon.classList.toggle('bi-eye');
            eyeIcon.classList.toggle('bi-eye-slash');
        });
    }

    // submit
    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const username = document.getElementById('username').value.trim();
        const password = loginPasswordInput.value;

        if (!username || !password) {
            loginAjaxMessage.innerHTML = "<div class='alert alert-danger'>Please fill in all fields.</div>";
            return;
        }

        const formData = new FormData(loginForm);

        fetch('login.php', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                loginAjaxMessage.innerHTML = `<div class='alert alert-success'>${data.message}</div>`;
                loginForm.reset();
                setTimeout(() => {
                    window.location.href = '../index.php';
                }, 1500);
            } else {
                loginAjaxMessage.innerHTML = `<div class='alert alert-danger'>${data.message}</div>`;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            loginAjaxMessage.innerHTML = "<div class='alert alert-danger'>An unexpected error occurred. Please try again.</div>";
        });
    });
}

//add student form
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('addStudentForm');
    if (!form) return; 

    const ajaxMessage = document.getElementById('ajax-message');

    // input elements
    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');
    const phoneInput = document.getElementById('phone');
    const addressInput = document.getElementById('address');
    const fileInput = document.getElementById('profile_picture');

    // functions for desplaying inline errors
    function showError(input, errorElementId, message) {
        input.classList.add('is-invalid');
        input.classList.remove('is-valid');
        if (errorElementId && message) {
            document.getElementById(errorElementId).innerText = message;
        }
    }

    function showSuccess(input) {
        input.classList.remove('is-invalid');
        input.classList.add('is-valid');
    }

    function clearStatus(input) {
        input.classList.remove('is-invalid', 'is-valid');
    }

    // on bur validations
    // name
    nameInput.addEventListener('blur', function() {
        if (!this.value.trim()) {
            showError(this);
        } else {
            showSuccess(this);
        }
    });

    // email
    emailInput.addEventListener('blur', function() {
        const emailValue = this.value.trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!emailValue) {
            showError(this, 'email-error', 'Email address is required.');
        } else if (!emailRegex.test(emailValue)) {
            showError(this, 'email-error', 'Please enter a valid email address.');
        } else {
            showSuccess(this);
        }
    });

    // phone number (needs 10 - 15 digits)
    phoneInput.addEventListener('blur', function() {
        const phoneValue = this.value.trim();
        const phoneRegex = /^[0-9]{10,15}$/;

        if (!phoneValue) {
            showError(this, 'phone-error', 'Phone number is required.');
        } else if (!phoneRegex.test(phoneValue)) {
            showError(this, 'phone-error', 'Phone number must be digits only (10 to 15 digits).');
        } else {
            showSuccess(this);
        }
    });

    // address
    addressInput.addEventListener('blur', function() {
        if (!this.value.trim()) {
            showError(this);
        } else {
            showSuccess(this);
        }
    });

    // profile picture validation
    fileInput.addEventListener('change', function() {
        if (this.files.length > 0) {
            const fileName = this.files[0].name;
            const fileExtension = fileName.split('.').pop().toLowerCase();
            const allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

            if (!allowedExtensions.includes(fileExtension)) {
                showError(this, 'file-error', 'Invalid file type. Only JPG, JPEG, PNG, and GIF allowed.');
                this.value = ''; 
            } else {
                showSuccess(this);
            }
        } else {
            clearStatus(this);
        }
    });


    // on submit
    form.addEventListener('submit', function(e) {
        e.preventDefault(); 

        // blur events on all text inputs to validate before submitting
        nameInput.dispatchEvent(new Event('blur'));
        emailInput.dispatchEvent(new Event('blur'));
        phoneInput.dispatchEvent(new Event('blur'));
        addressInput.dispatchEvent(new Event('blur'));

        // Check if any fields carry 'is-invalid' class
        if (form.querySelectorAll('.is-invalid').length > 0) {
            ajaxMessage.innerHTML = "<div class='alert alert-danger'>Please fix the errors in the form.</div>";
            return;
        }

        const formData = new FormData(form);

        fetch('add_student.php', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                ajaxMessage.innerHTML = `<div class='alert alert-success'>${data.message} <a href='view_students.php'>View Students</a></div>`;
                form.reset(); 
                
                // Reset input validation styling states
                [nameInput, emailInput, phoneInput, addressInput, fileInput].forEach(clearStatus);
            } else {
                ajaxMessage.innerHTML = `<div class='alert alert-danger'>${data.message}</div>`;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            ajaxMessage.innerHTML = "<div class='alert alert-danger'>An unexpected error occurred. Please try again.</div>";
        });
    });
});


//update student
const updateStudentForm = document.getElementById('updateStudentForm');
if (updateStudentForm) {
    const updateAjaxMessage = document.getElementById('ajax-message');

    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');
    const phoneInput = document.getElementById('phone');
    const addressInput = document.getElementById('address');
    const fileInput = document.getElementById('profile_picture');

    function showError(input, errorElementId, message) {
        input.classList.add('is-invalid');
        input.classList.remove('is-valid');
        if (errorElementId && message) {
            document.getElementById(errorElementId).innerText = message;
        }
    }

    function showSuccess(input) {
        input.classList.remove('is-invalid');
        input.classList.add('is-valid');
    }

    function clearStatus(input) {
        input.classList.remove('is-invalid', 'is-valid');
    }

    // name
    nameInput.addEventListener('blur', function() {
        if (!this.value.trim()) {
            showError(this);
        } else {
            showSuccess(this);
        }
    });

    // email
    emailInput.addEventListener('blur', function() {
        const emailValue = this.value.trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!emailValue) {
            showError(this, 'email-error', 'Email address is required.');
        } else if (!emailRegex.test(emailValue)) {
            showError(this, 'email-error', 'Please enter a valid email address.');
        } else {
            showSuccess(this);
        }
    });

    // phone
    phoneInput.addEventListener('blur', function() {
        const phoneValue = this.value.trim();
        const phoneRegex = /^[0-9]{10,15}$/;

        if (!phoneValue) {
            showError(this, 'phone-error', 'Phone number is required.');
        } else if (!phoneRegex.test(phoneValue)) {
            showError(this, 'phone-error', 'Phone number must be digits only (10 to 15 digits).');
        } else {
            showSuccess(this);
        }
    });

    // address
    addressInput.addEventListener('blur', function() {
        if (!this.value.trim()) {
            showError(this);
        } else {
            showSuccess(this);
        }
    });

    // profile picture 
    fileInput.addEventListener('change', function() {
        if (this.files.length > 0) {
            const fileName = this.files[0].name;
            const fileExtension = fileName.split('.').pop().toLowerCase();
            const allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

            if (!allowedExtensions.includes(fileExtension)) {
                showError(this, 'file-error', 'Invalid file type. Only JPG, JPEG, PNG, and GIF allowed.');
                this.value = '';
            } else {
                showSuccess(this);
            }
        } else {
            clearStatus(this);
        }
    });

    // Submit handler
    updateStudentForm.addEventListener('submit', function(e) {
        e.preventDefault();

        // blur validations for inputs
        nameInput.dispatchEvent(new Event('blur'));
        emailInput.dispatchEvent(new Event('blur'));
        phoneInput.dispatchEvent(new Event('blur'));
        addressInput.dispatchEvent(new Event('blur'));

        if (updateStudentForm.querySelectorAll('.is-invalid').length > 0) {
            updateAjaxMessage.innerHTML = "<div class='alert alert-danger'>Please fix the errors in the form before submitting.</div>";
            return;
        }

        const formData = new FormData(updateStudentForm);
        const actionUrl = updateStudentForm.getAttribute('action');

        fetch(actionUrl, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                updateAjaxMessage.innerHTML = `<div class='alert alert-success'>${data.message}</div>`;
                setTimeout(() => {
                    window.location.href = 'view_students.php';
                }, 1500);
            } else {
                updateAjaxMessage.innerHTML = `<div class='alert alert-danger'>${data.message}</div>`;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            updateAjaxMessage.innerHTML = "<div class='alert alert-danger'>An unexpected error occurred. Please try again.</div>";
        });
    });
}