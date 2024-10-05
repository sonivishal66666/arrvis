<?php
session_start();
require_once '../conn.php';
$class = "signin";

$cur_page = 'signup';
include 'includes/inc-header.php';
include 'includes/inc-nav.php';

if (isset($_POST['email'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    if (!isset($email, $password)) {
?>
<script>
alert("Ensure you fill the form properly.");
</script>
<?php
    } else {
        $password = md5($password);
        $check = $conn->prepare("SELECT * FROM passenger WHERE email = ? AND password = ?");
        $check->bind_param("ss", $email, $password);
        if (!$check->execute()) die("Form Filled With Error");
        $res = $check->get_result();
        $no_rows = $res->num_rows;
        if ($no_rows ==  1) {
            $row = $res->fetch_assoc();
            $id = $row['id'];
            $status = $row['status'];
            if ($status != 1) {
?>
<script>
alert("Account Deactivated!\nContact The System Administrator!");
window.location = "signin.php";
</script>
<?php
                exit;
            }
            session_regenerate_id(true);
            $_SESSION['user_id'] = $id;
            $_SESSION['email'] = $email;
?>
<!-- Enhanced Access Granted Message -->
<div id="successMessage" class="overlay-message success">
    <h2>Access Granted!</h2>
    <p>Welcome back! You will be redirected shortly...</p>
    <div class="loader"></div>
</div>

<script>
document.getElementById('successMessage').style.display = 'block';
setTimeout(function() {
    window.location = "individual.php";
}, 3000); // Redirect after 3 seconds
</script>

<?php
            exit;
        } else { ?>
<!-- Error Message -->
<div id="errorMessage" class="overlay-message error">
    Access Denied. Please check your email and password.
</div>
<script>
document.getElementById('errorMessage').style.display = 'block';
setTimeout(function() {
    document.getElementById('errorMessage').style.display = 'none';
}, 2000);
</script>
<?php
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #1a1a1a, #4e4e4e);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #fff;
        }

        .signup-container {
            width: 100%;
            max-width: 350px;
            background: rgba(255, 255, 255, 0.9);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            text-align: center;
            position: relative;
        }

        h2 {
            margin-bottom: 20px;
            font-size: 22px;
            color: #333;
        }

        .input-container {
            position: relative;
            margin-bottom: 20px;
            text-align: left;
        }

        input[type="email"], input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            background-color: #fff;
            color: #333;
            transition: all 0.2s ease;
            font-size: 16px;
        }

        label {
            position: absolute;
            top: 12px;
            left: 12px;
            color: #aaa;
            font-size: 14px;
            pointer-events: none;
            transition: all 0.2s ease;
        }

        input:focus + label, input:not(:placeholder-shown) + label {
            top: -10px;
            left: 12px;
            font-size: 12px;
            color: #5a67d8;
        }

        input:focus {
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
            width: 100%;
            transition: background-color 0.3s, transform 0.2s;
        }

        button:hover {
            background-color: #4c51bf;
            transform: translateY(-2px);
        }

        button:active {
            background-color: #434190;
        }

        .message {
            margin-top: 15px;
            color: #555;
        }

        /* Overlay Message Styles */
        .overlay-message {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1000;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            width: 300px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }

        .success {
            background-color: #28a745;
            color: #ffffff;
            animation: fadeIn 0.5s ease;
        }

        .error {
            background-color: #dc3545;
            color: #ffffff;
            animation: fadeIn 0.5s ease;
        }

        /* Simple Loader Animation */
        .loader {
            margin-top: 15px;
            border: 6px solid #f3f3f3;
            border-radius: 50%;
            border-top: 6px solid #ffffff;
            width: 24px;
            height: 24px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Fade In Effect */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
</head>
<body>

    <div class="signup-container">
        <h2>Customer Panel</h2>
        <form class="login-form" method="post" role="form" id="signup-form" autocomplete="off">
            <div id="errorDiv"></div>
            
            <!-- Email Input -->
            <div class="input-container">
                <input type="email" required name="email" placeholder=" " id="email">
                <label for="email">Email Address</label>
            </div>

            <!-- Password Input -->
            <div class="input-container">
                <input type="password" required name="password" placeholder=" " id="password">
                <label for="password">Password</label>
            </div>

            <!-- Submit Button -->
            <div class="form-group">
                <button type="submit" id="btn-signup">
                    SIGN IN
                </button>
            </div>

            <!-- Forgot Password Link -->
            <p class="message">
                <a href="forgot_password.php">Forgot Password?</a><br>
            </p>

        </form>
    </div>

    <script src="assets/js/jquery-1.12.4-jquery.min.js"></script>
    <script src="assets/js/sweetalert2.js"></script>

</body>
</html>
