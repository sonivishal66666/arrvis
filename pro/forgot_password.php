<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../conn.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer files (ensure paths are correct)
require 'vendor/autoload.php';

$message = ""; // Variable to hold the message

if (isset($_POST['email'])) {
    $email = $_POST['email'];

    // Check if the email exists in the database
    $stmt = $conn->prepare("SELECT * FROM passenger WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $token = bin2hex(random_bytes(50)); // Generate a unique token

        // Store the token in the database with an expiry (1 hour)
        $stmt = $conn->prepare("UPDATE passenger SET reset_token = ?, token_expiry = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE email = ?");
        $stmt->bind_param("ss", $token, $email);
        $stmt->execute();

        // Create the reset link
        $resetLink = "http://localhost:3000/train/pro/reset_password.php?token=$token";

        // Set up PHPMailer
        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Use Gmail SMTP server
            $mail->SMTPAuth = true;
            $mail->Username = 'dcsoni6350@gmail.com'; // Your Gmail address
            $mail->Password = 'jpzt oaqy iiar ydow'; // Your Gmail App Password
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            // Recipients
            $mail->setFrom('dcsoni6350@gmail.com', 'ARVIS');
            $mail->addAddress($email); // Add recipient

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
            $mail->Body    = "Click the following link to reset your password: <a href='$resetLink'>$resetLink</a>";
            $mail->AltBody = "Click the following link to reset your password: $resetLink";

            // Send the email
            $mail->send();
            $message = "Password reset link sent to your email.";
        } catch (Exception $e) {
            // Handle error
            $message = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        $message = "No account found with this email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #4e4e4e, #1a1a1a);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #fff;
            overflow: hidden;
            position: relative;
        }

        .landing-screen {
            position: absolute;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            opacity: 1;
            transition: opacity 1s ease;
            z-index: 1;
        }

        .landing-screen.hidden {
            opacity: 0;
            pointer-events: none;
        }

        .forgot-password-container {
            background: rgba(255, 255, 255, 0.9);
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            z-index: 2;
            width: 100%;
            max-width: 400px;
            text-align: center;
            animation: slideIn 0.8s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        h2 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
        }

        label {
            display: block;
            margin-bottom: 10px;
            color: #333;
            text-align: left;
        }

        input[type="email"] {
            padding: 12px;
            width: 100%;
            border: 1px solid #ccc;
            border-radius: 4px;
            outline: none;
            transition: border-color 0.3s;
            background-color: #fff;
            color: #333;
        }

        input[type="email"]:focus {
            border-color: #5a67d8;
        }

        button {
            background-color: #5a67d8;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s, transform 0.2s;
            width: 100%;
        }

        button:hover {
            background-color: #4c51bf;
            transform: translateY(-2px);
        }

        button:active {
            background-color: #434190;
            transform: translateY(0);
        }

        .message-container {
            display: flex;
            align-items: center;
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
            margin-top: 15px;
            opacity: 1;
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .verify-logo {
            width: 30px;
            height: 30px;
            margin-right: 10px;
            animation: bounce 1s infinite alternate;
        }

        @keyframes bounce {
            from {
                transform: translateY(0);
            }
            to {
                transform: translateY(-5px);
            }
        }
    </style>
</head>
<body>
    <!-- Landing Screen -->
    <div class="landing-screen" id="landing-screen">
        <h1 style="color: white; font-size: 36px;">Welcome to Forgot Password</h1>
    </div>

    <div class="forgot-password-container">
        <h2>Forgot Password</h2>
        <form method="post" action="">
            <label for="email">Enter your email address:</label>
            <input type="email" name="email" required>
            <button type="submit">Send Reset Link</button>
        </form>

        <!-- Display message -->
        <?php if ($message): ?>
            <div class="message-container">
                <img src="images/verify-logo.gif" alt="Verify Logo" class="verify-logo">
                <span><?php echo $message; ?></span>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Fade out the landing screen after 2 seconds
        setTimeout(function() {
            document.getElementById('landing-screen').classList.add('hidden');
        }, 2000);
    </script>
</body>
</html>
