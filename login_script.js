// Show/Hide Password Toggle
function togglePasswordVisibility(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    
    if (input.type === "password") {
        input.type = "text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
    } else {
        input.type = "password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
    }
}

// Login password toggle
const toggleLogin = document.getElementById("toggle-login-password");
if (toggleLogin) {
    toggleLogin.addEventListener("click", () => {
        togglePasswordVisibility("login-password", "toggle-login-password");
    });
}

// Register password toggle
const toggleRegister = document.getElementById("toggle-register-password");
if (toggleRegister) {
    toggleRegister.addEventListener("click", () => {
        togglePasswordVisibility("register-password", "toggle-register-password");
    });
}

// Show/Hide Forms
function showform(formId) {
    // Hide all forms
    const allForms = document.querySelectorAll(".form-box");
    allForms.forEach(form => form.classList.remove("active"));
    
    // Show selected form
    const selectedForm = document.getElementById(formId);
    if (selectedForm) {
        selectedForm.classList.add("active");
    }
    
    return false;
}



