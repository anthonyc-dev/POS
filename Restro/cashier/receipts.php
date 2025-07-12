<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();
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
        <div style="background-image: url(../admin/assets/img/theme/21231.avif); background-size: cover;" class="header pb-8 pt-5 pt-md-8">
            <span class="mask bg-gradient-dark opacity-8"></span>
            <div class="container-fluid">
                <div class="header-body">
                </div>
            </div>
        </div>

        <!-- Page content -->
        <div class="container-fluid mt--8">
            <!-- Date Filter Form -->
            <form method="GET" action="">
                <div class="row mb-4">
                    <!-- Date Range Filters -->
                    <div class="col-md-4">
                        <label style="color:white;">Start Date</label>
                        <input type="date" name="start_date" class="form-control" value="<?php echo isset($_GET['start_date']) ? $_GET['start_date'] : ''; ?>">
                    </div>
                    <div class="col-md-4">
                        <label style="color:white;">End Date</label>
                        <input type="date" name="end_date" class="form-control" value="<?php echo isset($_GET['end_date']) ? $_GET['end_date'] : ''; ?>">
                    </div>

                    <div class="col-md-4">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary btn-block">Search</button>
                    </div>
                </div>
            </form>

            <!-- Table -->
            <div class="row">
                <div class="col">
                    <div class="card shadow">
                        <div class="card-header border-0">
                            Paid Orders
                        </div>
                        <div class="table-responsive">
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-success" scope="col">Code</th>
                                        <th scope="col">Customer</th>
                                        <th class="text-success" scope="col">Product</th>
                                        <th scope="col">Unit Price</th>
                                        <th class="text-success" scope="col">#</th>
                                        <th scope="col">Total Price</th>
                                        <th scope="col">Amount Received</th>
                                        <th scope="col">Change</th> <!-- Added Change Column -->
                                        <th scope="col">Date</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Get the start and end dates from the GET request, if available
                                    $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
                                    $end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';

                                    // Base query to fetch paid orders
                                    $query = "SELECT * FROM rpos_orders WHERE order_status = 'Paid'";

                                    // Modify query based on date range
                                    if ($start_date && $end_date) {
                                        $query .= " AND DATE(created_at) BETWEEN ? AND ?";
                                    } elseif ($start_date) {
                                        $query .= " AND DATE(created_at) >= ?";
                                    } elseif ($end_date) {
                                        $query .= " AND DATE(created_at) <= ?";
                                    }

                                    $query .= " ORDER BY `created_at` DESC";

                                    // Prepare the statement
                                    $stmt = $mysqli->prepare($query);

                                    // Bind parameters based on available date range
                                    if ($start_date && $end_date) {
                                        $stmt->bind_param("ss", $start_date, $end_date);
                                    } elseif ($start_date) {
                                        $stmt->bind_param("s", $start_date);
                                    } elseif ($end_date) {
                                        $stmt->bind_param("s", $end_date);
                                    }

                                    $stmt->execute();
                                    $res = $stmt->get_result();

                                    while ($order = $res->fetch_object()) {
                                        $total = ($order->prod_price * $order->prod_qty);

                                        // Fetch the payment details (amount_received, change)
                                        $payment_query = "SELECT amount_received, `change` FROM rpos_payments WHERE order_code = ?";
                                        $payment_stmt = $mysqli->prepare($payment_query);
                                        $payment_stmt->bind_param("s", $order->order_code);
                                        $payment_stmt->execute();
                                        $payment_res = $payment_stmt->get_result();
                                        $payment_data = $payment_res->fetch_object();

                                        // Set defaults if no payment data found
                                        $amount_received = $payment_data ? $payment_data->amount_received : 0;
                                        $change = $payment_data ? $payment_data->change : 0;
                                    ?>
                                    <tr>
                                        <th class="text-success" scope="row"><?php echo $order->order_code; ?></th>
                                        <td><?php echo $order->customer_name; ?></td>
                                        <td class="text-success"><?php echo $order->prod_name; ?></td>
                                        <td>₱ <?php echo number_format($order->prod_price, 2); ?></td>
                                        <td class="text-success"><?php echo $order->prod_qty; ?></td>
                                        <td>₱ <?php echo number_format($total, 2); ?></td>
                                        <td>₱ <?php echo number_format($amount_received, 2); ?></td>
                                        <td>₱ <?php echo number_format($change, 2); ?></td>
                                        <td><?php echo date('d/M/Y g:i A', strtotime($order->created_at)); ?></td>
                                        <td>
                                            <a target="_blank" href="print_receipt.php?order_code=<?php echo $order->order_code; ?>">
                                                <button class="btn btn-sm btn-primary">
                                                    <i class="fas fa-print"></i> Print Receipt
                                                </button>
                                            </a>
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
            <?php require_once('partials/_footer.php'); ?>
        </div>
    </div>

    <!-- Argon Scripts -->
    <?php require_once('partials/_scripts.php'); ?>
</body>

</html>
