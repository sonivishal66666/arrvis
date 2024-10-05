
<?php
if (!isset($file_access)) die("Direct File Access Denied");
session_start();

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login if user is not logged in
    exit();
}

// Include database connection
require_once '../conn.php';

// Debugging: Test database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user_id from session
$user_id = $_SESSION['user_id'];
echo "<pre>Session user_id: $user_id</pre>";

// SQL Query: Fetch user details from the database including gender
$sql = "SELECT name, email, phone, address, gender, loc FROM passenger WHERE id = ?";
$query = $conn->prepare($sql);

if (!$query) {
    die("Query preparation failed: " . $conn->error);
}

$query->bind_param("i", $user_id);

// Execute the query
if (!$query->execute()) {
    die("Query execution failed: " . $query->error);
}

// Bind result variables including gender
$query->bind_result($name, $email, $phone, $address, $gender, $loc);

// Fetch the result
$userDetails = [];
if ($query->fetch()) {
    $userDetails = [
        "Name:" => $name,
        "Email:" => $email,
        "Phone:" => $phone,
        "Address:" => $address,
        "Gender:" => $gender,
        "Location:" => $loc
    ];
} else {
    echo "No data found for user ID: $user_id";
}

$query->close();
?>

<!-- Dashboard Content CSS -->
<style>
    body {
        margin: 0; /* Remove default body margin */
        overflow: hidden; /* Prevent scrolling */
        background-color: #1d1f21; /* Dark background for consistency */
        color: #f8f9fa; /* Text color */
    }

    .dashboard-container {
        display: flex;
        justify-content: Top;
        align-items: center;
        flex-direction: column;
        height: 100vh; /* Full height to eliminate vertical scroll */
        padding: 20px; /* Add padding to prevent content touching edges */
        box-sizing: border-box; /* Ensure padding is included in height */
    }

    .card {
        background-color: #2d2f31;
        color: #f8f9fa;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        padding: 50px;
        width: 600%; /* Increased width for better spacing */
        max-width: 1100px; /* Max width to avoid overflow */
        transition: transform 0.3s ease;
        text-align: left; /* Align text to the left */
        margin: 20px 0; /* Margin between cards */
    }

    .card:hover {
        transform: translateY(-2px); /* Lift effect on hover */
        box-shadow: 0 80px 250px rgba(0, 4, 0, 0.4); /* Stronger shadow on hover */
    }

    .info-container {
        display: flex; /* Flex container for two columns */
        flex-wrap: wrap; /* Allow items to wrap onto the next line */
        justify-content: space-between; /* Space between columns */
        margin: -10px; /* Negative margin for alignment */
    }

    .info-item {
        width: calc(50% - 20px); /* Two items per row with spacing */
        margin: 10px; /* Spacing between items */
        padding: 20px;
        background-color: #444;
        border-radius: 10px;
        transition: background-color 0.3s, transform 0.3s;
        text-align: center; /* Center align text */
    }

    .info-item:hover {
        background-color: #555; /* Darker background on hover */
        transform: scale(1.03); /* Slight scaling on hover */
    }

    .info-label {
        font-weight: bold;
        font-size: 16px; /* Increased font size */
        color: #007bff;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 10px; /* Space between label and value */
    }

    .info-value {
        font-size: 18px; /* Increased font size */
        color: #f8f9fa;
    }

    .btn {
        margin-top: 20px;
        padding: 10px 20px;
        border-radius: 20px;
        background-color: #007bff;
        border: none;
        color: #fff;
        cursor: pointer;
        transition: background-color 0.3s, transform 0.3s;
    }

    .btn:hover {
        background-color: #0056b3;
        transform: scale(1.05);
    }
</style>

<!-- Dashboard Content -->
<div class="dashboard-container">
    <div class="card">
        <h2><center>User Dashboard </center></h2>
        <div class="info-container">
            <?php if (!empty($userDetails)): ?>
                <?php foreach ($userDetails as $key => $value): ?>
                    <div class="info-item">
                        <span class="info-label"><?php echo $key; ?></span>
                        <span class="info-value"><?php echo $value; ?></span>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="info-item">No user details available.</div>
            <?php endif; ?>
        </div>
    </div>
</div>
