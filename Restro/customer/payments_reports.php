<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();
require_once('partials/_head.php');
?>

<body>
    <!-- Sidenav -->
    <?php
    require_once('partials/_sidebar.php');
    ?>
    <!-- Main content -->
    <div class="main-content">
        <!-- Top navbar -->
        <?php
        require_once('partials/_topnav.php');
        ?>
        <!-- Header -->
        <div style="background-image: url(../admin/assets/img/theme/customer.png); background-size: cover;"
            class="header  pb-8 pt-5 pt-md-8">
            <span class="mask bg-gradient-dark opacity-8"></span>
            <div class="container-fluid">
                <div class="header-body">
                </div>
            </div>
        </div>
        <!-- Page content -->
        <div class="container-fluid mt--8">
            <!-- Date Filter Form -->
            <div class="row mb-4">
                <div class="col">
                    <form method="GET">
                        <div class="row">
                            <div class="col-md-4">
                                <label style="color:white;">Start Date</label>
                                <input type="date" name="start_date" class="form-control"
                                    value="<?php echo isset($_GET['start_date']) ? $_GET['start_date'] : ''; ?>">
                            </div>
                            <div class="col-md-4">
                                <label style="color:white;">End Date</label>
                                <input type="date" name="end_date" class="form-control"
                                    value="<?php echo isset($_GET['end_date']) ? $_GET['end_date'] : ''; ?>">
                            </div>
                            <div class="col-md-4">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary btn-block">Search</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Payment Reports Table -->
            <div class="row">
                <div class="col">
                    <div class="card shadow">
                        <div class="card-header border-0">
                            Payment Reports
                        </div>
                        <div class="table-responsive">
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-success" scope="col">Payment Code</th>
                                        <th scope="col">Payment Method</th>
                                        <th class="text-success" scope="col">Order Code</th>
                                        <th scope="col">Amount Paid</th>
                                        <th class="text-success" scope="col">Date Paid</th>
                                        <th scope="col">Customer Address</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $customer_id = $_SESSION['customer_id'];
                                    $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
                                    $end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';

                                    // Base SQL query
                                    $query = "SELECT rpay.*, cus.customer_address FROM rpos_payments rpay 
                                              INNER JOIN rpos_customers cus ON rpay.customer_id = cus.customer_id 
                                              WHERE rpay.customer_id = ?";

                                    // Add date filters to the query if provided
                                    if ($start_date && $end_date) {
                                        $query .= " AND DATE(rpay.created_at) BETWEEN ? AND ?";
                                    } elseif ($start_date) {
                                        $query .= " AND DATE(rpay.created_at) >= ?";
                                    } elseif ($end_date) {
                                        $query .= " AND DATE(rpay.created_at) <= ?";
                                    }

                                    $query .= " ORDER BY rpay.created_at DESC";

                                    $stmt = $mysqli->prepare($query);

                                    // Bind parameters based on the filter criteria
                                    if ($start_date && $end_date) {
                                        $stmt->bind_param("sss", $customer_id, $start_date, $end_date);
                                    } elseif ($start_date) {
                                        $stmt->bind_param("ss", $customer_id, $start_date);
                                    } elseif ($end_date) {
                                        $stmt->bind_param("ss", $customer_id, $end_date);
                                    } else {
                                        $stmt->bind_param("s", $customer_id);
                                    }

                                    $stmt->execute();
                                    $res = $stmt->get_result();

                                    // Display payment records
                                    while ($payment = $res->fetch_object()) {
                                    ?>
                                        <tr>
                                            <th class="text-success" scope="row">
                                                <?php echo $payment->pay_code; ?>
                                            </th>
                                            <th scope="row">
                                                <?php echo $payment->pay_method; ?>
                                            </th>
                                            <td class="text-success">
                                                <?php echo $payment->order_code; ?>
                                            </td>
                                            <td>
                                                ₱<?php echo $payment->pay_amt; ?>
                                            </td>
                                            <td class="text-success">
                                                <?php echo date('d/M/Y g:i', strtotime($payment->created_at)); ?>
                                            </td>
                                            <td>
                                                <?php echo $payment->customer_address; ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Footer -->
            <?php
            require_once('partials/_footer.php');
            ?>
        </div>
    </div>
    <!-- Argon Scripts -->
    <?php
    require_once('partials/_scripts.php');
    ?>
</body>

</html>
