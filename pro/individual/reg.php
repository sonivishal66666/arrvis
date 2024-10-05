<?php
if (!isset($file_access)) die("Direct File Access Denied");

$me = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Train Tickets</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Custom CSS for Neon and Animations -->
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #1a1a1a; /* Dark background */
            color: whitesmoke; /* Whitesmoke for body text */
        }

        .neon-text {
            color: #00b7ff;
            text-shadow: 0 0 5px rgba(0, 183, 255, 0.6),
                         0 0 10px rgba(0, 183, 255, 0.6),
                         0 0 20px rgba(0, 183, 255, 0.6);
        }

        .neon-button {
            background-color: #2c2c2c;
            border: none;
            color: #fff;
            box-shadow: 0 0 5px rgba(255,255,255,0.3),
                        0 0 10px rgba(0, 183, 255, 0.6),
                        0 0 20px rgba(0, 183, 255, 0.6);
            transition: all 0.3s ease;
        }

        .neon-button:hover {
            color: #00b7ff;
            box-shadow: 0 0 15px rgba(0, 183, 255, 1);
        }

        .modal-content {
            background-color: #1c1c1c;
            color: whitesmoke;
            box-shadow: 0 0 10px rgba(0, 183, 255, 0.6),
                        0 0 20px rgba(0, 183, 255, 0.6);
            transition: transform 0.3s ease;
        }

        .modal-content:hover {
            transform: scale(1.05);
        }

        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
            box-shadow: 0 0 10px rgba(0,255,0,0.4);
        }

        .btn-success:hover {
            background-color: #218838;
            box-shadow: 0 0 20px rgba(0,255,0,0.7);
        }

        table th, table td {
            color: whitesmoke;
        }

        .table-striped tbody tr {
            background-color: black;
            color: whitesmoke;
        }

        .table-striped tbody tr:nth-child(odd) td {
            background-color: black;
            color: white;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 183, 255, 0.2);
        }

        .card-header {
            background-color: #1f1f1f;
            border-bottom: 2px solid #00b7ff;
        }
    </style>
</head>
<body>

<div class="content py-5">
    <div class="container">
        <div class="card bg-dark text-white">
            <div class="card-header neon-text">
                <h3 class="card-title"><b>Book Train Tickets</b></h3>
            </div>
            <div class="card-body">
                <table id="example1" class="table table-hover neon-table w-100 table-bordered table-striped">
                    <thead class="neon-text">
                        <tr>
                            <th>#</th>
                            <th>Route</th>
                            <th>Status</th>
                            <th>Date/Time</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $row = querySchedule('future');
                        if ($row->num_rows < 1) {
                            echo "<div class='alert alert-danger' role='alert'>
                            Sorry, There are no schedules at the moment! Please visit after some time.
                            </div>";
                        }
                        $sn = 0;
                        while ($fetch = $row->fetch_assoc()) {
                            $db_date = $fetch['date'];
                            if ($db_date == date('d-m-Y')) {
                                $db_time = $fetch['time'];
                                $current_time = date('H:i');
                                if ($current_time >= $db_time) {
                                    continue;
                                }
                            }
                            $id = $fetch['id']; ?>
                            <tr>
                                <td><?php echo ++$sn; ?></td>
                                <td><?php echo $fullname = getRoutePath($fetch['route_id']); ?></td>
                                <td><?php
                                    $array = getTotalBookByType($id);
                                    echo ($max_first = ($array['first'] - $array['first_booked'])), " Seat(s) Available for First Class" . "<hr/>" . ($max_second = ($array['second'] - $array['second_booked'])) . " Seat(s) Available for Second Class";
                                    ?></td>
                                <td><?php echo $fetch['date'], " / ", formatTime($fetch['time']); ?></td>
                                <td>
                                    <button type="button" class="btn neon-button" data-bs-toggle="modal"
                                            data-bs-target="#book<?php echo $id ?>">
                                        <i class="fas fa-ticket-alt"></i> Book
                                    </button>
                                </td>
                            </tr>

                            <!-- Modal for booking form -->
                            <div class="modal fade" id="book<?php echo $id ?>" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title">Book For <?php echo $fullname; ?> &#128642;</h4>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- Form to send booking data to preview.php -->
                                            <form action="dist/preview.php" method="post">
                                                <input type="hidden" class="form-control" name="id"
                                                       value="<?php echo $id ?>" required>

                                                <p>Number of Tickets (If you are the only one, leave as it is):
                                                    <input type="number" min='1' value="1"
                                                           max='<?php echo $max_first >= $max_second ? $max_first : $max_second ?>'
                                                           name="number" class="form-control" required>
                                                </p>
                                                <p>
                                                    Class: <select name="class" required class="form-control">
                                                        <option value="">-- Select Class --</option>
                                                        <option value="first">First Class (₹<?php echo ($fetch['first_fee']); ?>)</option>
                                                        <option value="second">Second Class (₹<?php echo ($fetch['second_fee']); ?>)</option>
                                                    </select>
                                                </p>
                                                <input type="submit" name="submit" class="btn btn-success" value="Proceed">
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap 5 JS and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
