<?php
if (!isset($file_access)) die("Direct File Access Denied");
?>

<!-- Content Section -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- Callout Info -->
                <div class="callout callout-info">
                    <h5><i class="fas fa-info"></i> Info:</h5>
                    We always want to hear from you! Replied to within 24 hours.
                </div>

                <!-- Feedback Message Container -->
                <div id="feedback-message" class="feedback-message"></div>

                <!-- Feedbacks List -->
                <div class="card">
                    <div class="card-header alert-success">
                        <h5 class="card-title"><b>List of all Feedbacks</b></h5>
                        <div class="float-right">
                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#add">
                                Send New Feedback
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-dark table-striped" id='example1'>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Your Comment</th>
                                    <th>Response</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sn = 0;
                                $query = getFeedbacks();
                                while ($row = $query->fetch_assoc()) {
                                    $sn++;
                                    echo "<tr>
                                    <td>$sn</td>
                                    <td>" . $row['message'] . "</td>
                                    <td>" . ($row['response'] == NULL ? '-- No Response Yet --' : $row['response']) . "</td>
                                    </tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</section>

<!-- Modal for Sending Feedback -->
<div class="modal fade" id="add">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Send New Feedback</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" method="post">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                Type Message: 
                                <textarea name="message" required minlength="10" cols="30" rows="10" class="form-control"></textarea>
                            </div>
                        </div>
                    </div>
                    <input type="submit" name="sendFeedback" class="btn btn-success" value="Send">
                </form>
            </div>
        </div>
    </div>
</div>

<?php
if (isset($_POST['sendFeedback'])) {
    $msg = $_POST['message'];
    $send = sendFeedback($msg);
    
    if ($send) {
        echo "<div class='alert-success'>Feedback sent! We will get back to you.</div>";
    } else {
        echo "<div class='alert-danger'>Feedback could not be sent! Try again!</div>";
    }
}
?>

<!-- Additional Styles for Beautification -->
<style>
    /* Dark background for better visibility */
    body {
        background-color: #1a1a1a;
        color: #f1f1f1;
    }

    .card-header {
        background-color: #28a745 !important;
        color: white !important;
    }

    .callout-info {
        background-color: #2a2a2a;
        border-left-color: #5a67d8;
        color: white;
    }

    /* Feedback Message Styling */
    .feedback-message {
        margin: 10px 0;
        padding: 15px;
        border-radius: 5px;
        font-size: 16px;
        font-weight: bold;
    }

    .alert-success {
        background-color: #28a745;
        color: white;
        border: 1px solid #218838;
    }

    .alert-danger {
        background-color: #dc3545;
        color: white;
        border: 1px solid #c82333;
    }

    /* Hover effects on buttons */
    .btn-primary {
        background-color: #4c51bf;
        border-color: #4c51bf;
        transition: background-color 0.3s ease, transform 0.2s ease;
    }

    .btn-primary:hover {
        background-color: #5a67d8;
        transform: translateY(-2px);
    }

    .btn-success {
        background-color: #28a745;
        transition: background-color 0.3s ease, transform 0.2s ease;
    }

    .btn-success:hover {
        background-color: #218838;
        transform: translateY(-2px);
    }

    /* Table design */
    table {
        background-color: #1a1a1a;
        color: white;
    }

    thead th {
        background-color: #333;
        color: #fff;
    }

    tbody tr:hover {
        background-color: #333;
    }

    /* Modal animations */
    .modal.fade .modal-dialog {
        transition: transform 0.3s ease-out;
        transform: translateY(-50px);
    }

    .modal.show .modal-dialog {
        transform: translateY(0);
    }

    /* Dark background for the table and hover effects */
    .table-dark {
        background-color: #2a2a2a;
        color: #fff;
    }

    .table-dark th, .table-dark td {
        color: #fff;
    }

    /* Adding hover effects to rows */
    .table-dark tbody tr:hover {
        background-color: #444;
    }

    /* Textarea styling */
    textarea.form-control {
        background-color: #333;
        color: #fff;
        border: 1px solid #555;
        transition: border-color 0.3s ease;
    }

    textarea.form-control:focus {
        border-color: #5a67d8;
        outline: none;
    }
</style>
