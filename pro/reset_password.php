<?php
require_once '../conn.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Check if the token is valid and not expired
    $stmt = $conn->prepare("SELECT * FROM passenger WHERE reset_token = ? AND token_expiry > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        if (isset($_POST['password'])) {
            $password = md5($_POST['password']); // Hash the password
            $row = $result->fetch_assoc();
            $email = $row['email'];

            // Update the password and clear the token
            $stmt = $conn->prepare("UPDATE passenger SET password = ?, reset_token = NULL, token_expiry = NULL WHERE email = ?");
            $stmt->bind_param("ss", $password, $email);
            $stmt->execute();

            echo "<script>alert('Password reset successfully.'); window.location = 'signin.php';</script>";
        }
    } else {
        echo "<script>alert('Invalid or expired token.'); window.location = 'signin.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
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

        .reset-password-container {
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

        input[type="password"] {
            padding: 12px;
            width: 100%;
            border: 1px solid #ccc;
            border-radius: 4px;
            outline: none;
            transition: border-color 0.3s;
            background-color: #fff;
            color: #333;
        }

        input[type="password"]:focus {
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

        .message {
            margin-top: 15px;
            color: #555;
        }
    </style>
</head>
<body>
    <!-- Landing Screen -->
    <div class="landing-screen" id="landing-screen">
        <h1 style="color: white; font-size: 36px;">Welcome to Password Reset</h1>
    </div>

    <div class="reset-password-container">
        <h2>Reset Password</h2>
        <form method="post" action="">
            <label for="password">Enter new password:</label>
            <input type="password" name="password" required>
            <button type="submit">Reset Password</button>
        </form>
    </div>

    <script>
        // Fade out the landing screen after 2 seconds
        setTimeout(function() {
            document.getElementById('landing-screen').classList.add('hidden');
        }, 2000);
    </script>
</body>
</html>
