<?php
session_start();
require_once "config.php";

$token   = $_GET['token'] ?? '';
$error   = '';
$success = '';
$valid   = false;

// Validate token
// if ($token) {
//     $stmt = $conn->prepare("SELECT pr.user_id, pr.expiry, u.email 
//                             FROM password_resets pr 
//                             JOIN users u ON u.id = pr.user_id 
// //                             WHERE pr.token = ? AND pr.expiry > NOW()");
//     $stmt->bind_param("s", $token);
//     $stmt->execute();
//     $result = $stmt->get_result();
//     if ($result->num_rows > 0) {
//         $valid = true;
//         $row   = $result->fetch_assoc();
//     } else {
//         $error = "This reset link is invalid or has expired. Please request a new one.";
//     }
  $stmt = $conn->prepare("
    SELECT user_id, expiry 
    FROM password_resets 
    WHERE token = ?
");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    // NOW CHECK EXPIRY IN PHP
    if (strtotime($row['expiry']) < time()) {
        $error = "This reset link is expired.";
    } else {
        $valid = true;
    }
} else {
    $error = "This reset link is invalid or has expired. Please request a new one.";
}  





















// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $valid) {
    $new_password = $_POST['new_password'];
    $confirm      = $_POST['confirm_password'];

    if (strlen($new_password) < 6) {
        $error = "Password must be at least 6 characters.";
    } elseif ($new_password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hashed, $row['user_id']);
        $stmt->execute();

        // Delete used token
        $stmt = $conn->prepare("DELETE FROM password_resets WHERE token = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();

        $success = "Password reset successfully! You can now log in.";
        $valid   = false; // hide the form
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password — Ruhh</title>
    <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<a href="landing.html" class="logo">
    <h1>Ruhh<span>.</span></h1>
</a>

<div class="background">
<div class="container">

    <?php if (!empty($success)): ?>
        <h2>Password Reset</h2>
        <p class="success-message" style="text-align:center; color:#2d8659; background:rgba(144,238,144,0.15); border:2px solid rgba(144,238,144,0.4); border-radius:10px; padding:15px; margin-bottom:20px;">
            <?php echo htmlspecialchars($success); ?>
        </p>
        <p class="message" style="text-align:center; margin-top:15px;">
            <a href="login.php">Back to Login</a>
        </p>

    <?php elseif ($valid): ?>
        <h2>New Password</h2>
        <p class="subtitle">Choose a strong new password for your account.</p>

        <?php if ($error): ?>
            <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <form action="reset_password.php?token=<?php echo htmlspecialchars($token); ?>" method="post">
            <div class="password-field">
                <input type="password" name="new_password" id="new-password" placeholder="New Password" required>
                <i class="fa-solid fa-eye" id="toggle-new-password"></i>
            </div>
            <div class="password-field" style="margin-top:15px;">
                <input type="password" name="confirm_password" id="confirm-password" placeholder="Confirm Password" required>
                <i class="fa-solid fa-eye" id="toggle-confirm-password"></i>
            </div>
            <button type="submit" style="margin-top:20px;">Reset Password</button>
        </form>
        <p class="message" style="margin-top:15px;"><a href="login.php">Back to Login</a></p>

    <?php else: ?>
        <h2>Invalid Link</h2>
        <p class="error-message"><?php echo htmlspecialchars($error ?: "No token provided."); ?></p>
        <p class="message" style="text-align:center; margin-top:15px;">
            <a href="login.php" onclick="showform('forgot-form'); return false;">Request a new reset link</a>
        </p>
    <?php endif; ?>

</div>
</div>

<script>
function togglePasswordVisibility(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon  = document.getElementById(iconId);
    if (input.type === "password") {
        input.type = "text";
        icon.classList.replace("fa-eye", "fa-eye-slash");
    } else {
        input.type = "password";
        icon.classList.replace("fa-eye-slash", "fa-eye");
    }
}
document.getElementById("toggle-new-password")?.addEventListener("click", () =>
    togglePasswordVisibility("new-password", "toggle-new-password"));
document.getElementById("toggle-confirm-password")?.addEventListener("click", () =>
    togglePasswordVisibility("confirm-password", "toggle-confirm-password"));
</script>
</body>
</html>