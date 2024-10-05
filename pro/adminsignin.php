<?php
session_start();
require_once '../conn.php';
$file = "admin";

$cur_page = 'signup';
include 'includes/inc-header.php';

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
        // Check for login
        $password = md5($password);
        $check = $conn->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
        $check->bind_param("ss", $email, $password);
        if (!$check->execute()) die("Form Filled With Error");
        $res = $check->get_result();
        $no_rows = $res->num_rows;
        if ($no_rows ==  1) {
            $row = $res->fetch_assoc();
            $id = $row['id'];
            session_regenerate_id(true);
            $_SESSION['category'] = "super";
            $_SESSION['admin'] = $id;
?>
<script>
    // Use a more aesthetic approach for success message
    document.addEventListener("DOMContentLoaded", function() {
        const successDiv = document.createElement("div");
        successDiv.innerHTML = "Access Granted!";
        successDiv.style.position = "fixed";
        successDiv.style.top = "50%";
        successDiv.style.left = "50%";
        successDiv.style.transform = "translate(-50%, -50%)";
        successDiv.style.padding = "20px";
        successDiv.style.backgroundColor = "#28a745"; // Green color
        successDiv.style.color = "#fff";
        successDiv.style.borderRadius = "5px";
        successDiv.style.boxShadow = "0 0 10px rgba(0, 0, 0, 0.5)";
        successDiv.style.zIndex = "1000";
        document.body.appendChild(successDiv);
        setTimeout(() => {
            window.location = "admin.php";
        }, 2000);
    });
</script>
<?php
        } else { ?>
<script>
    // Use a more aesthetic approach for error message
    document.addEventListener("DOMContentLoaded", function() {
    const errorDiv = document.createElement("div");
    errorDiv.innerHTML = "Access Denied.";
    errorDiv.style.position = "fixed";
    errorDiv.style.top = "50%";
    errorDiv.style.left = "50%";
    errorDiv.style.transform = "translate(-50%, -50%)";
    errorDiv.style.padding = "20px";
    errorDiv.style.backgroundColor = "#dc3545"; // Red color
    errorDiv.style.color = "#fff";
    errorDiv.style.borderRadius = "5px";
    errorDiv.style.boxShadow = "0 0 10px rgba(0, 0, 0, 0.5)";
    errorDiv.style.zIndex = "1000";
    document.body.appendChild(errorDiv);

    // Timeout to remove the error message after 3 seconds (3000 milliseconds)
    setTimeout(() => {
        errorDiv.style.opacity = 0; // Fade out effect
        setTimeout(() => {
            document.body.removeChild(errorDiv); // Remove the element after fading out
        }, 500); // Wait for 0.5 seconds before removing the element
    }, 3000); // Show the message for 3 seconds
});

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #121212; /* Dark background */
            color: white; /* White text */
            font-family: Arial, sans-serif;
            overflow: hidden; /* Prevent scrolling during landing screen */
        }

        .signup-page {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh; /* Full height */
            position: relative;
        }

        .form {
            background: rgba(255, 255, 255, 0.1); /* Semi-transparent white */
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
            width: 400px; /* Fixed width */
            text-align: center;
        }

        .form h2 {
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 5px;
            background-color: rgba(255, 255, 255, 0.2);
            color: white; /* White text in input fields */
        }

        button {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 5px;
            background-color: #007bff; /* Bootstrap primary color */
            color: white;
            font-size: 16px;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3; /* Darker blue on hover */
            transform: scale(1.05); /* Button scaling */
        }

        /* Landing Screen Styles */
        #landing-screen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            z-index: 999;
            transition: opacity 1s ease;
        }

        #landing-screen.hidden {
            opacity: 0;
            pointer-events: none;
        }
    </style>
</head>
<body>

    <div id="landing-screen">Welcome to the Admin Portal</div> <!-- Landing Screen -->

    <div class="signup-page">
        <div class="form">
            <h2>Admin Sign In</h2>
            <br>
            <form class="login-form" method="post" role="form" id="signup-form" autocomplete="off">
                <div id="errorDiv"></div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="text" required name="email" placeholder="Enter your email">
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" id="password" placeholder="Enter your password">
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        <button type="submit" id="btn-signup">SIGN IN</button>
                    </div>
                </div>
                <p class="message">
                    <a href="#">Forgot your password?</a><br>
                </p>
            </form>
        </div>
    </div>

    <script src="assets/js/jquery-1.12.4-jquery.min.js"></script>
    <script>
        // Hide landing screen after 3 seconds
        document.addEventListener('DOMContentLoaded', function () {
            setTimeout(function () {
                document.getElementById('landing-screen').classList.add('hidden');
                document.body.style.overflow = 'auto'; // Enable scrolling
            }, 3000);
        });
    </script>

</body>
</html>
