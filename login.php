<?php
session_start();
$errors = [
 'login' => $_SESSION['login_error'] ?? '',
 'register' => $_SESSION['register_error'] ?? '',
 'forgot' => $_SESSION['forgot_error'] ?? ''
];
$success_message = $_SESSION['success_message'] ?? '';
$active_form = $_SESSION['active_form'] ?? 'login'; 
session_unset();

function showerror($error) {
    return !empty($error) ? "<p class='error-message'>$error</p>" : '';
}

function isactiveform($formname, $active_form) {
    return $formname === $active_form ? 'active' : '';
}


?>





























<!DOCTYPE html>
<html lang="en">    

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
  <title>Login Page</title>
  <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body> 

<a href="landing.html" class="logo" >
  <h1>Ruhh<span>.</span></h1>
</a>

<div class="background">
<div class="container"> 

    <!-- LOGIN FORM -->
    <div class="form-box <?php echo isactiveform('login', $active_form); ?>" id="login-form">
        <form action="login_register.php" method = "post"> 
            <h2>Login</h2> 
            <?php echo showerror($errors['login']); ?>
            
<?php if (!empty($success_message)): ?>
    <p class="success-message">
        <?php echo $success_message; ?>
    </p>
<?php endif; ?>
            <input type="email" name="email" placeholder="Email" required>
            
            <div class="password-field">
                <input type="password" name="password" id="login-password" placeholder="Password" required>
                <i class="fa-solid fa-eye" id="toggle-login-password"></i>
            </div>
            
            <button type="submit" name="login"> Login </button>
            
            <p class="message">
                <a href="#" onclick="showform('forgot-form'); return false;">Forgot Password?</a> | 
                Don't have an account? <a href="#" onclick="showform('register-form'); return false;">Register</a>
            </p>
        </form>
    </div>

    <!-- FORGOT PASSWORD FORM -->
    <div class="form-box <?php echo isactiveform('forgot', $active_form); ?>" id="forgot-form">
        <form action="login_register.php" method = "post"> 
            <h2>Reset Password</h2> 
            <?php echo showerror($errors['forgot'] ?? ''); ?>
            
            <p class="subtitle">Enter your email to receive a reset link</p>
            
            <input type="email" name="reset_email" placeholder="Email" required>
            
            <button type="submit" name="forgot"> Send Reset Link </button>

            <p class="message">Back to <a href="#" onclick="showform('login-form'); return false;">Login</a></p>
        </form>
    </div>
<!-- REGISTER FORM -->
    <div class="form-box <?php echo isactiveform('register', $active_form); ?>" id="register-form">
        <form action="login_register.php" method = "post"> 
            <h2>Register</h2> 
            <?php echo showerror($errors['register']); ?>
        
            <input type="text" name="name" placeholder="Name" required>
            <input type="email" name="email" placeholder="Email" required>
            
            <div class="password-field">
                <input type="password" name="password" id="register-password" placeholder="Password" required>
                <i class="fa-solid fa-eye" id="toggle-register-password"></i>
            </div>
            
            <button type="submit" name="register"> Register </button>

            <p class="message">Already have an account? <a href="#" onclick="showform('login-form'); return false;">Login</a></p>
        </form>
    </div>
</div>
</div>




<script src="login_script.js"></script>
</body>