<?php
session_start();
require_once '../conn.php';
require_once '../constants.php';
require 'send_otp.php';  // Include the OTP sending script
$class = "reg";
?>

<?php
$cur_page = 'signup';
include 'includes/inc-header.php';
include 'includes/inc-nav.php';

if (isset($_POST['name'])) {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $file = 'file';
    $address = $_POST['address'];
    $cpassword = $_POST['cpassword'];
    $password = $_POST['password'];

    if (empty($name) || empty($address) || empty($phone) || empty($email) || empty($password) || empty($cpassword) || ($password != $cpassword)) { ?>
        <script>
            alert("Ensure you fill the form properly.");
        </script>
    <?php
    } else {
        $check_email = $conn->prepare("SELECT id FROM passenger WHERE email = ? OR phone = ?");
        $check_email->bind_param("ss", $email, $phone);
        $check_email->execute();
        $check_email->store_result();

        if ($check_email->num_rows > 0) { ?>
            <script>
                alert("Email or Phone already exists!");
            </script>
        <?php
        } elseif ($cpassword != $password) { ?>
            <script>
                alert("Passwords do not match.");
            </script>
        <?php
        } else {
            $password = md5($password);
            $loc = uploadFile('file');

            $stmt = $conn->prepare("INSERT INTO passenger (name, email, password, phone, address, loc) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $name, $email, $password, $phone, $address, $loc);
            if ($stmt->execute()) {
                $otp = rand(100000, 999999);
                $_SESSION['otp'] = $otp;
                $_SESSION['email'] = $email;

                // Fetch and store user data in session
                $user_id = $conn->insert_id;  // Get the last inserted ID
                $_SESSION['user_id'] = $user_id;
                
                sendOTP($email, $otp);

                header("Location: verify_otp.php");
                exit();
            } else { ?>
                <script>
                    alert("We could not register you!");
                </script>
            <?php
            }
        }

        $check_email->free_result();
        $check_email->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup - Modern</title>
    
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #1f1c2c, #928DAB);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            color: #fff;
        }

        .form-container {
            background: #2D2A3B;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 750px;
            text-align: center;
        }

        h2 {
            color: #fff;
            font-weight: bold;
            margin-bottom: 15px;
        }

        /* Use flexbox for two-column layout */
        .form-row {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }

        .input-container {
            position: relative;
            margin-bottom: 15px;
            width: 48%; /* Each field takes up 48% of width */
        }

        label {
            position: absolute;
            top: 12px;
            left: 12px;
            color: #aaa;
            font-size: 14px;
            pointer-events: none;
            transition: all 0.3s ease; /* Smooth transition effect */
        }

        input[type="text"], input[type="email"], input[type="password"], input[type="file"], select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
            background-color: #3D3B4D;
            color: #fff;
        }

        input[type="file"] {
            padding: 8px;
        }

        /* Transition label when field is focused or filled */
        input:focus + label, select:focus + label, input:not(:placeholder-shown) + label {
            top: -8px;
            left: 12px;
            font-size: 12px;
            color: #5a67d8;
        }

        select {
            background-color: #3D3B4D;
            color: #fff;
            padding: 10px;
        }

        button {
            background: #5a67d8;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            font-size: 15px;
        }

        button:hover {
            background: #434190;
        }

        /* Divider and Google Sign-in */
        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 20px 0;
        }

        .divider::before, .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #ccc;
        }

        .divider:not(:empty)::before {
            margin-right: .5em;
        }

        .divider:not(:empty)::after {
            margin-left: .5em;
        }

        /* Google Sign-in button */
        .g-signin2 {
            margin-top: 10px;
        }

    </style>

    <!-- Load the Google API client library -->
    <script src="https://accounts.google.com/gsi/client" async defer></script>
</head>
<body>
    <div class="form-container">
        <h2>Create Account</h2>

        <form class="login-form" method="post" role="form" enctype="multipart/form-data" id="signup-form" autocomplete="off">
            <div class="form-row">
                <div class="input-container">
                    <input type="text" required minlength="10" name="name" placeholder=" " id="name">
                    <label for="name">Full Name</label>
                </div>

                <div class="input-container">
                    <input type="text" minlength="10" pattern="[0-9]{10}" required name="phone" placeholder=" " id="phone">
                    <label for="phone">Contact Number</label>
                </div>

                <div class="input-container">
                    <input type="email" required name="email" placeholder=" " id="email">
                    <label for="email">Email Address</label>
                </div>

                <div class="input-container">
                    <input type="file" name="file" id="file">
                    <label for="file" style="top: -14px;">Upload File</label>
                </div>

                <div class="input-container">
                    <input type="text" name="address" placeholder=" " required id="address">
                    <label for="address">Address</label>
                </div>

                <div class="input-container">
                    <input type="password" name="password" placeholder=" " id="password">
                    <label for="password">Password</label>
                </div>

                <div class="input-container">
                    <input type="password" name="cpassword" placeholder=" " id="cpassword">
                    <label for="cpassword">Confirm Password</label>
                </div>

                <!-- Gender Section -->
                <div class="input-container">
                    <select name="gender" required id="gender">
                        <option value="" disabled selected></option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                    <label for="gender">Gender</label>
                </div>
            </div>

            <button type="submit" id="btn-signup">CREATE ACCOUNT</button>
        </form>

        <!-- Divider and Google Sign-in -->
        <div class="divider">or</div>

        <!-- Google Sign-in Button -->
        <div id="g_id_onload"
            data-client_id="YOUR_GOOGLE_CLIENT_ID"
            data-callback="handleCredentialResponse">
        </div>
        <div class="g_id_signin" data-type="standard"></div>
    </div>

    <script>
        function handleCredentialResponse(response) {
            // Parse the ID token from Google and send it to the backend for validation
            const id_token = response.credential;

            // Post the ID token to the server via AJAX
            const form = new FormData();
            form.append('id_token', id_token);

            fetch('signup.php', {
                method: 'POST',
                body: form
            })
            .then(response => response.text())
            .then(data => {
                if (data.includes('Invalid Google ID Token')) {
                    alert('Invalid ID Token');
                } else {
                    window.location.href = 'dashboard.php';
                }
            })
            .catch(error => console.error('Error:', error));
        }

        // Load the Google API library
        window.onload = function() {
            google.accounts.id.initialize({
                client_id: 'YOUR_GOOGLE_CLIENT_ID',
                callback: handleCredentialResponse
            });

            google.accounts.id.renderButton(
                document.querySelector('.g_id_signin'),
                { theme: 'outline', size: 'large' }
            );
        };
    </script>
</body>
</html>
