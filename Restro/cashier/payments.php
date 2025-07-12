<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();
//Cancel Order
if (isset($_GET['cancel'])) {
    $id = $_GET['cancel'];
    $adn = "DELETE FROM  rpos_orders  WHERE  order_id = ?";
    $stmt = $mysqli->prepare($adn);
    $stmt->bind_param('s', $id);
    $stmt->execute();
    $stmt->close();
    if ($stmt) {
        $success = "Deleted" && header("refresh:1; url=payments.php");
    } else {
        $err = "Try Again Later";
    }
}
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
        <div style="background-image: url(../admin/assets/img/theme/21231.avif); background-size: cover;"
            class="header  pb-8 pt-5 pt-md-8">
            <span class="mask bg-gradient-dark opacity-8"></span>
            <div class="container-fluid">
                <div class="header-body">
                </div>
            </div>
        </div>
        <!-- Page content -->
        <div class="container-fluid mt--8">
            <!-- Table -->
            <div class="row">
                <div class="col">
                    <div class="card shadow">
                        <div class="card-header border-0 d-flex justify-content-between align-items-center">
                            <h3 class="mb-0">Payments</h3>
                            <div class="form-inline">
                                <div class="input-group mr-2">
                                    <input type="text" id="search-input" class="form-control"
                                        placeholder="Search by Customer Name, Product, or Payment Method">
                                    <div class="input-group-append">
                                        <span class="input-group-text bg-primary text-white">
                                            <i class="fas fa-search"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col">Customer</th>
                                        <th scope="col">Address</th>
                                        <th scope="col">Product</th>
                                        <th scope="col">Total Price</th>
                                        <th scope="col">Payment Method</th> <!-- Added Payment Method Column -->
                                        <th scope="col">Date</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $ret = "SELECT rpos_orders.*, rpos_customers.customer_name, rpos_customers.customer_address, rpos_orders.payment_method
                                    FROM rpos_orders 
                                    JOIN rpos_customers ON rpos_orders.customer_id = rpos_customers.customer_id 
                                    WHERE rpos_orders.order_status = ''  
                                    ORDER BY rpos_orders.created_at DESC";                            
                                    $stmt = $mysqli->prepare($ret);
                                    $stmt->execute();
                                    $res = $stmt->get_result();
                                    while ($order = $res->fetch_object()) {
                                        $total = ((float) $order->prod_price * (int) $order->prod_qty);
                                        ?>
                                        <tr>
                                            <td><?php echo $order->customer_name; ?></td>
                                            <td><?php echo $order->customer_address; ?></td>
                                            <td><?php echo $order->prod_name; ?></td>
                                            <td>₱ <?php echo $total; ?></td>
                                            <td><?php echo $order->payment_method; ?></td> <!-- Displaying Payment Method -->
                                            <td><?php echo date('d/M/Y g:i', strtotime($order->created_at)); ?></td>
                                            <td>
                                                <a href="pay_order.php?order_code=<?php echo $order->order_code; ?>&customer_id=<?php echo $order->customer_id; ?>&order_status=Paid">
                                                    <button class="btn btn-sm btn-success">
                                                        <i class="fas fa-handshake"></i>
                                                        Pay Order
                                                    </button>
                                                </a>

                                                <a href="payments.php?cancel=<?php echo $order->order_id; ?>">
                                                    <button class="btn btn-sm btn-danger">
                                                        <i class="fas fa-window-close"></i>
                                                        Cancel Order
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

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const searchInput = document.getElementById("search-input");
        const tableRows = document.querySelectorAll("table tbody tr");

        function filterTable() {
            const searchFilter = searchInput.value.toLowerCase();

            tableRows.forEach(row => {
                const customerName = row.cells[0].textContent.toLowerCase();
                const product = row.cells[2].textContent.toLowerCase();
                const paymentMethod = row.cells[4].textContent.toLowerCase();

                const matchesSearch = customerName.includes(searchFilter) || product.includes(searchFilter) || paymentMethod.includes(searchFilter);

                if (matchesSearch) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            });
        }

        searchInput.addEventListener("input", filterTable);
    });
</script>

</html>
