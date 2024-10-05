<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("User not logged in!");
}

// Include the database connection
require_once '../../conn.php';

// Check database connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Check if form data is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    // Capture booking data
    $class = $_POST['class'];
    $number = $_POST['number'];
    $schedule_id = $_POST['id'];

    // Check for valid input
    if ($number < 1) {
        die("Invalid Number");
    }

    // Determine which fee to retrieve based on the class
    $fee_column = ($class === 'first') ? 'first_fee' : 'second_fee';

    // Fetch route from the database
    $route_query = "SELECT start, stop FROM route WHERE id = (SELECT route_id FROM schedule WHERE id = ?)";
    $fee_query = "SELECT $fee_column FROM schedule WHERE id = ?";

    // Prepare and bind parameters for the route query
    if ($stmt = $conn->prepare($route_query)) {
        $stmt->bind_param("i", $schedule_id);
        $stmt->execute();
        $stmt->bind_result($start, $stop);
        $stmt->fetch();
        $stmt->close();

        // Combine start and stop for the route description
        $route = $start . " to " . $stop;
    } else {
        die("Failed to fetch route.");
    }

    // Prepare and bind parameters for the fee query
    if ($stmt = $conn->prepare($fee_query)) {
        $stmt->bind_param("i", $schedule_id);
        $stmt->execute();
        $stmt->bind_result($fee);
        $stmt->fetch();
        $stmt->close();
    } else {
        die("Failed to fetch fee.");
    }

    // Calculate totals
    $total = $fee * $number; // Use the retrieved fee
    $vat = ceil($total * 0.01);
    $grand_total = $total + $vat;

    // Store the data in session for later use
    $_SESSION['amount'] = $grand_total;
    $_SESSION['schedule'] = $schedule_id;
    $_SESSION['no'] = $number;
    $_SESSION['class'] = $class;
} else {
    die("No booking details provided.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Preview</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #1d1f21; /* Dark background */
            color: #f8f9fa; /* Text color */
            overflow: hidden; /* Prevent scrolling */
            margin: 0; /* Remove default body margin */
        }

        .container {
            height: 100vh; /* Full height to eliminate vertical scroll */
            display: flex;
            flex-direction: column;
            justify-content: center; /* Center content vertically */
            align-items: center; /* Center content horizontally */
            padding: 20px;
            box-sizing: border-box; /* Include padding in height */
        }

        .card {
            background-color: #2d2f31;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            padding: 30px; /* Add padding for card content */
            width: 100%;
            max-width: 600px; /* Max width for better aesthetics */
            transition: transform 0.3s ease;
            margin: 20px 0; /* Space between cards */
        }

        .card:hover {
            transform: translateY(-5px); /* Slight lift effect on hover */
            box-shadow: 0 80px 250px rgba(0, 4, 0, 0.4); /* Stronger shadow on hover */
        }

        .preview-box {
            background-color: #28a745; /* Green background */
            color: #fff; /* White text */
            padding: 15px;
            border-radius: 8px;
            width: 100%;
            max-width: 600px; /* Same width as card */
            margin-bottom: 20px; /* Space below the preview box */
            text-align: center; /* Center text */
        }

        .btn {
            padding: 10px 20px;
            border-radius: 20px;
            border: none;
            color: #fff;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s;
            margin-top: 10px; /* Margin for spacing */
            width: 100%; /* Full width buttons */
        }

        .btn-success {
            background-color: #007bff; /* Blue color for proceed */
        }

        .btn-success:hover {
            background-color: #0056b3; /* Darker blue on hover */
            transform: scale(1.05); /* Scale effect on hover */
        }

        .btn-danger {
            background-color: #dc3545; /* Red color for go back */
        }

        .btn-danger:hover {
            background-color: #c82333; /* Darker red on hover */
            transform: scale(1.05); /* Scale effect on hover */
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="preview-box">
            <h4>Booking Preview</h4>
        </div>
        <div class="card">
            <p>You are about to book <?php echo $number, " Ticket", $number > 1 ? 's' : '', ' for ', $route; ?>.</p>
            <p>Class: <?php echo ucwords($class); ?></p>
            <p>Total: ₹<?php echo $total; ?></p>
            <p>VAT: ₹<?php echo $vat; ?></p>
            <p>Grand Total: ₹<?php echo $grand_total; ?></p>
            <a href="../pay.php" class="btn btn-success">Proceed to Payment</a>
            <a href="javascript:history.back()" class="btn btn-danger">Go Back</a> <!-- Go Back button -->
        </div>
    </div>
</body>
</html>
