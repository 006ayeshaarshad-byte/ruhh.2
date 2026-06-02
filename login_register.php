<?php
session_start();
require_once "config.php"; // connection kei liye jo databse tak access deta hai
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
// ======================
 //REGISTER
 //======================
if (isset($_POST['register'])) {
   $name     = trim($_POST['name']);
   $email    = trim($_POST['email']);
   $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
   $role     = 'user'; // always user — admin accounts are set manually in DB

  //  Check if email already exists
    $stmt = $conn->prepare("SELECT email FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
       $_SESSION['register_error'] = "Email already exists.";
       $_SESSION['active_form'] = "register";
        header("Location: login.php");
        exit();
   } else {
       // Insert new user
       $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
       $stmt->bind_param("ssss", $name, $email, $password, $role);
       if ($stmt->execute()) {
           $_SESSION['success_message'] = "Registration successful! You can now log in.";
           header("Location: login.php");
           exit();
       } else {
           $_SESSION['register_error'] = "Error during registration. Please try again. " . $stmt->error;
           $_SESSION['active_form'] = "register";
           header("Location: login.php");
           exit();
       }
   }
}
?> 


 


















<?php

// ======================
 //LOGIN
 //======================
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            // Successful login
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] === 'admin') {
                header("Location: admin.php");
            } else {
                header("Location: dashboard.php");
            }
            exit();
        } else {
            $_SESSION['login_error'] = "Incorrect password.";
            $_SESSION['active_form'] = "login";
            header("Location: login.php");
            exit();
        }
    } else {
        $_SESSION['login_error'] = "No account found with this email.";
        $_SESSION['active_form'] = "login";
        header("Location: login.php");
        exit();
    }
}











// ======================
// FORGOT PASSWORD
// ======================
if (isset($_POST['forgot'])) {
    $reset_email = $_POST['reset_email'];

    $stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
    $stmt->bind_param("s", $reset_email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // DELETE old tokens FIRST
    $stmt = $conn->prepare("DELETE FROM password_resets WHERE user_id = ?");
    $stmt->bind_param("i", $user['id']);
    $stmt->execute();
        // Generate a random token
        $token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        // Store token in database
        $stmt = $conn->prepare("INSERT INTO password_resets (user_id, token, expiry) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user['id'], $token, $expiry);
        $stmt->execute();
        
        // Send email with reset link
        $reset_link = "http://localhost/ruhh.2/reset_password.php?token=$token";
        $mail = new PHPMailer(true);

try {

    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;

    $mail->Username   = '006ayeshaarshad@gmail.com';

    
    $mail->Password   = 'gflquzcsndrdrezf';

    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->setFrom('006ayeshaarshad@gmail.com', 'Ruhh');

    // Send to user requesting reset
    $mail->addAddress($reset_email);

    $mail->isHTML(true);

    $reset_link = "http://localhost/ruhh.2/reset_password.php?token=$token";

    $mail->Subject = 'Password Reset - Ruhh';

    $mail->Body = "
        <h2>Password Reset</h2>

        <p>Click the button below to reset your password:</p>

        <p>
            <a href='$reset_link'
               style='padding:12px 20px;
               background:#c2b2ed;
               color:white;
               text-decoration:none;
               border-radius:8px;'>
               Reset Password
            </a>
        </p>

        <p>Or copy this link:</p>

        <p>$reset_link</p>

        <p>This link expires in 1 hour.</p>
    ";

       $mail->send();

    $_SESSION['success_message'] = "Password reset link sent!";
    $_SESSION['active_form'] = "login";

} catch (Exception $e) {

    $_SESSION['forgot_error'] = "Mailer Error: " . $mail->ErrorInfo;
    $_SESSION['active_form'] = "forgot";
}

} else {
    $_SESSION['forgot_error'] = "No account found with this email.";
    $_SESSION['active_form'] = "forgot";
}

header("Location: login.php");
exit();
} 

?>