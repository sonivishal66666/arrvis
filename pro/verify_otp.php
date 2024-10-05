<?php
session_start();
require_once '../conn.php';

$otpVerified = false;
$errorMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entered_otp = $_POST['otp'];

    if ($entered_otp == $_SESSION['otp']) {
        $otpVerified = true;  // OTP is correct
        unset($_SESSION['otp']);  // Clear OTP session
    } else {
        $errorMsg = "Incorrect OTP. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #4b79a1, #283e51);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            color: #fff;
        }

        .landing-screen {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            z-index: 10;
            opacity: 1;
            animation: fadeOut 2s forwards 2s;
        }

        @keyframes fadeOut {
            to {
                opacity: 0;
                z-index: -1;
            }
        }

        .form-container {
            background: rgba(0, 0, 0, 0.7);
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 400px;
            text-align: center;
            position: relative;
            animation: slideIn 0.8s ease-out;
        }

        @keyframes slideIn {
            from {
                transform: translateY(-100vh);
            }
            to {
                transform: translateY(0);
            }
        }

        h2 {
            color: #fff;
            font-weight: bold;
            margin-bottom: 20px;
        }

        input[type="text"] {
            width: 100%;
            padding: 12px;
            margin: 12px 0;
            border: none;
            border-radius: 6px;
            box-sizing: border-box;
            background-color: #3D3B4D;
            color: #fff;
        }

        input::placeholder {
            color: #bbb;
        }

        button {
            background: #5a67d8;
            color: white;
            padding: 14px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            transition: all 0.3s ease;
        }

        button:hover {
            background-color: #434190;
            transform: scale(1.05);
        }

        button:focus {
            outline: none;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
        }

        /* Success/Error Message Styles */
        .message-container {
            background-color: #28a745;  /* Success green */
            color: #ffffff;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            font-size: 1.2rem;
            text-align: center;
        }

        .error-container {
            background-color: #dc3545;  /* Error red */
            color: #ffffff;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            font-size: 1.2rem;
            text-align: center;
        }

        /* Animation for success and error messages */
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
    </style>
</head>
<body>

<div class="landing-screen">
    Verifying OTP...
</div>

<div class="form-container">
    <h2>Enter OTP</h2>

    <?php if ($otpVerified) { ?>
        <div class="message-container" style="animation: fadeIn 1s;">
            OTP Verified! Registration complete.
            <script>
                setTimeout(function () {
                    window.location.href = 'signin.php';  // Redirect to login after delay
                }, 3000);  // Redirect after 3 seconds
            </script>
        </div>
    <?php } else { ?>

        <?php if ($errorMsg) { ?>
            <div class="error-container" style="animation: fadeIn 1s;">
                <?php echo $errorMsg; ?>
            </div>
        <?php } ?>

        <form method="post">
            <input type="text" name="otp" placeholder="Enter OTP" required>
            <button type="submit">Verify</button>
        </form>
    <?php } ?>
</div>

</body>
</html>
