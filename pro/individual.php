<?php
@session_start();
$file_access = true;
include '../conn.php';
include 'session.php';
include '../constants.php';
if (@$_GET['page'] == 'print' && isset($_GET['print'])) printClearance($_GET['print']);
$fullname = getIndividualName($_SESSION['user_id'], $conn);
if (isset($_GET['error'])) {
    echo "<script>alert('Payment could not be initialized! Network Error!'); window.location = 'individual.php?page=reg';</script>";
    exit;
}
?>
<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">

    <title><?php echo SITE_NAME, ' - Passenger\'s Account' ?> </title>
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
    <!-- Custom CSS for enhancements -->
    <style>
        body {
            background-color: #1d1f21;
            color: #f8f9fa;
        }
        .main-header.navbar {
            background-color: #333;
            border-bottom: 1px solid #555;
        }
        .main-sidebar {
            background-color: #1d1f21;
        }
        .nav-link {
            color: #f8f9fa;
        }
        .nav-link:hover {
            background-color: #555;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .brand-link, .brand-text {
            color: #f8f9fa !important;
        }
        .sidebar .user-panel img {
            border-radius: 50%;
            border: 2px solid #f8f9fa;
        }
        .sidebar .nav-icon {
            animation: iconBounce 1s infinite alternate;
        }
        @keyframes iconBounce {
            0% { transform: translateY(0); }
            100% { transform: translateY(-5px); }
        }
        .content-wrapper {
            background-color: #222;
            padding: 20px;
            border-radius: 10px;
        }
        h1 {
            color: #f8f9fa;
        }
        .btn, .nav-link {
            border-radius: 20px;
        }
        .loader {
            position: fixed;
            z-index: 1000;
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            background: rgba(0, 0, 0, 0.7);
            visibility: hidden;
        }
        .loader .spinner-border {
            width: 5rem;
            height: 5rem;
        }
        .loading-active .loader {
            visibility: visible;
        }

        /* Red logout animation */
        .logout-animation {
            position: fixed;
            z-index: 1001;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 0, 0, 0.8);
            display: none;
            justify-content: center;
            align-items: center;
            color: white;
            font-size: 2rem;
            transition: opacity 1s;
        }
        .logout-active {
            display: flex;
            opacity: 1;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini">

<div class="loader">
    <div class="spinner-border text-light"></div>
</div>

<!-- Red Logout Animation -->
<div class="logout-animation" id="logoutAnimation">
    Logging Out...
</div>

<div class="wrapper loading-container">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-dark">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
            </li>
            <li class="navbar-nav">
                <a class="nav-link" href="#"><?php echo SITE_NAME ?></a>
            </li>
        </ul>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <a href="individual.php" class="brand-link">
            <span class="brand-text font-weight-light"><?php echo date("D d, M y"); ?></span>
        </a>

        <div class="sidebar">
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image">
                    <img src="<?php echo getImage($_SESSION['user_id'], $conn); ?>" class="img-circle elevation-2" alt="User Image">
                </div>
                <div class="info">
                    <a href="#" class="d-block"><?php echo $fullname; ?></a>
                </div>
            </div>

            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <li class="nav-item">
                        <a href="individual.php" class="nav-link <?php echo (@$_GET['page'] == '') ? 'active' : '';?>">
                            <i class="nav-icon fas fa-home"></i>
                            <p>My Profile</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="individual.php?page=reg" class="nav-link <?php echo (@$_GET['page'] == 'reg') ? 'active' : '';?>">
                            <i class="fa fa-plus nav-icon"></i>
                            <p>New Booking</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="individual.php?page=paid" class="nav-link <?php echo (@$_GET['page'] == 'paid') ? 'active' : '';?>">
                            <i class="fa fa-book nav-icon"></i>
                            <p>View Bookings</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="individual.php?page=feedback" class="nav-link <?php echo (@$_GET['page'] == 'feedback') ? 'active' : '';?>">
                            <i class="fa fa-mail-bulk nav-icon"></i>
                            <p>Feedback</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link" id="logoutBtn">
                            <i class="nav-icon fas fa-power-off"></i>
                            <p>Logout</p>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0"> <?php echo htmlspecialchars($fullname); ?>'s Dashboard</h1>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <div class="content">
            <?php
                if (!isset($_GET['page']))
                    include 'individual/index.php';
                elseif ($_GET['page'] == 'reg')
                    include 'individual/reg.php';
                elseif ($_GET['page'] == 'paid')
                    include 'individual/paid.php';
                elseif ($_GET['page'] == 'feedback')
                    include 'individual/feedback.php';
                elseif ($_GET['page'] == 'logout') {
                    @session_destroy();
                    // Redirect will be handled by the JavaScript below
                }
            ?>
        </div>
    </div>

  <!-- Main Footer -->
<footer class="main-footer">
    <div class="footer-content">
        <div class="footer-left">
            <strong>&copy; <?php echo date("Y"); ?> <?php echo SITE_NAME; ?> - All Rights Reserved</strong>
        </div>
        <div class="footer-right">
            <span>Follow us:</span>
            <a href="#" class="footer-link">
                <i class="fab fa-facebook-f"></i>
            </a>
            <a href="#" class="footer-link">
                <i class="fab fa-twitter"></i>
            </a>
            <a href="#" class="footer-link">
                <i class="fab fa-instagram"></i>
            </a>
            <a href="#" class="footer-link">
                <i class="fab fa-linkedin-in"></i>
            </a>
            <a href="#" class="footer-link">
                <i class="fab fa-youtube"></i>
            </a>
        </div>
    </div>
</footer>

<style>
    .main-footer {
        background-color: #2d2f31;
        color: #f8f9fa;
        padding: 30px;
        text-align: center;
        position: relative;
        bottom: 0;
        width: 90%;
        box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.5);
    }
    .footer-link {
        color: #f8f9fa;
        margin-left: 10px;
    }
    .footer-link:hover {
        color: #ffc107;
    }
</style>
</body>

<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="dist/js/adminlte.min.js"></script>
<script>
    $(document).ready(function () {
        // Logout button click event
        $('#logoutBtn').on('click', function (e) {
            e.preventDefault();
            // Show the logout animation
            $('#logoutAnimation').addClass('logout-active');
            setTimeout(function () {
                // After the animation duration, redirect to the logout page
                window.location.href = 'individual.php?page=logout';
            }, 2000); // Adjust the duration as needed
        });
    });
</script>
</html>

