<?php
header("Content-Type: application/json");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$raw = file_get_contents("php://input");
$data = json_decode($raw, true);
$email = trim($data["email"] ?? "");

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["success" => false, "error" => "Invalid email"]);
    exit;
}

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
    $mail->addAddress('006ayeshaarshad@gmail.com');

    $mail->Subject = 'New Ruhh Subscriber!';
    $mail->Body    = "Someone just subscribed to Ruhh 💜\n\nEmail: " . $email . "\nTime: " . date("Y-m-d H:i:s");

    $mail->send();
    echo json_encode(["success" => true]);

} catch (Exception $e) {
    echo json_encode(["success" => false, "error" => $mail->ErrorInfo]);
}
?>