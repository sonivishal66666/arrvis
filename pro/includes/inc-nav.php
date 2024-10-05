
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup Page</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #1c1c1c, #434343);
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            position: relative;
            color: #fff; /* Set default text color to white */
        }

        /* Navbar at the top */
        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            padding: 15px 20px;
            z-index: 100; /* Ensures navbar stays above other content */
        }

        .navbar a {
            color: white;
            text-decoration: none;
            margin: 0 15px;
            padding: 10px 20px;
            border-radius: 4px;
            transition: background 0.3s ease;
        }

        .navbar a:hover {
            background-color: #575757;
        }

        .navbar .active a {
            background-color: #5a67d8; /* Active page link */
        }

        /* Form container */
        .form-container {
            background: rgba(255, 255, 255, 0.9); /* Light background for the form */
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 500px;
            text-align: center;
            position: relative;
            animation: slideIn 0.8s ease-out;
        }

        /* Animation for form container */
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

        /* Button animations */
        .btn {
            background-color: #5a67d8;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s, transform 0.2s;
        }

        .btn:hover {
            background-color: #4c51bf;
            transform: translateY(-2px);
        }

        .btn:active {
            background-color: #434190;
            transform: translateY(0px);
        }

        input {
            padding: 10px;
            width: 90%;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            outline: none;
            transition: border-color 0.3s;
        }

        input:focus {
            border-color: #5a67d8;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="container-fluid">
            <ul class="nav navbar-nav">
                <li class="<?php echo $class == 'reg' ? 'active' : '' ?>">
                    <a href="individual_reg.php">Sign Up</a>
                </li>
                <li class="<?php echo $class != 'reg' ? 'active' : '' ?>">
                    <a href="signin.php">Sign In</a>
                </li>
                <li>
                    <a href="../">Go Back</a>
                </li>
            </ul>
        </div>
    </nav>