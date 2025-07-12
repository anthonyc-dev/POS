<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();

// Initialize dates
$start_date = $end_date = "";

// Handle date filter form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
}

require_once('partials/_head.php');
?>

<body>
    <!-- Sidenav -->
    <?php require_once('partials/_sidebar.php'); ?>

    <!-- Main content -->
    <div class="main-content">
        <!-- Top navbar -->
        <?php require_once('partials/_topnav.php'); ?>

        <!-- Header -->
        <div style="background-image: url(../admin/assets/img/theme/adminimg.avif); background-size: cover;"
            class="header pb-8 pt-5 pt-md-8">
            <span class="mask bg-gradient-dark opacity-8"></span>
            <div class="container-fluid">
                <div class="header-body"></div>
            </div>
        </div>

        <!-- Page content -->
        <div class="container-fluid mt--8">
            
            <!-- Date Filter Form -->
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h3 class="mb-0">Filter by Date</h3>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="form-row">
                            <div class="col-md-5">
                                <label>Start Date</label>
                                <input type="date" name="start_date" class="form-control" value="<?php echo $start_date; ?>" required>
                            </div>
                            <div class="col-md-5">
                                <label>End Date</label>
                                <input type="date" name="end_date" class="form-control" value="<?php echo $end_date; ?>" required>
                            </div>
                            <div class="col-md-2 align-self-end">
                                <button type="submit" class="btn btn-primary">Filter</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Table for total sales per day -->
            <div class="row mt-4">
                <div class="col">
                    <div class="card shadow">
                        <div class="card-header border-0">
                            Sales Reports
                        </div>
                        <div class="table-responsive">
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-success" scope="col">Date</th>
                                        <th scope="col">Total Sales</th>
                                        <th class="text-success" scope="col">Number of Payments</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Query for total sales per day
                                    $query = "SELECT DATE(created_at) AS payment_date, SUM(pay_amt) AS total_paid, COUNT(*) AS total_payments 
                                              FROM rpos_payments WHERE 1";
                                    if ($start_date && $end_date) {
                                        $query .= " AND DATE(created_at) BETWEEN ? AND ?";
                                    }
                                    $query .= " GROUP BY DATE(created_at) ORDER BY DATE(created_at) DESC";

                                    $stmt = $mysqli->prepare($query);
                                    if ($start_date && $end_date) {
                                        $stmt->bind_param('ss', $start_date, $end_date);
                                    }
                                    $stmt->execute();
                                    $res = $stmt->get_result();
                                    while ($payment = $res->fetch_object()) {
                                    ?>
                                        <tr>
                                            <th class="text-success" scope="row"><?php echo date('d/M/Y', strtotime($payment->payment_date)); ?></th>
                                            <td>₱<?php echo number_format($payment->total_paid, 2); ?></td>
                                            <td class="text-success"><?php echo $payment->total_payments; ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <?php require_once('partials/_footer.php'); ?>
        </div>
    </div>

    <!-- Argon Scripts -->
    <?php require_once('partials/_scripts.php'); ?>
</body>
</html>
