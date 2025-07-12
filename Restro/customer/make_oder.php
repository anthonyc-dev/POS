    <?php
    session_start();
    include('config/config.php');
    include('config/checklogin.php');
    include('config/code-generator.php');

    check_login();

    if (isset($_POST['make'])) {
        // Prevent Posting Blank Values
        if (empty($_POST["order_code"]) || empty($_POST["customer_name"]) || empty($_GET['prod_price'])) {
            $err = "Blank Values Not Accepted";
        } else {
            $order_id = $_POST['order_id'];
            $order_code = $_POST['order_code'];
            $customer_id = $_SESSION['customer_id'];
            $customer_name = $_POST['customer_name'];
            $prod_id = $_GET['prod_id'];
            $prod_name = $_GET['prod_name'];
            $prod_price = $_GET['prod_price'];
            $prod_qty = $_POST['prod_qty'];
            $payment_method = $_POST['payment_method']; // Added payment method

            // Step 1: Check the available stock for the product
            $checkStockQuery = "SELECT prod_stock FROM rpos_products WHERE prod_id = ?";
            $checkStockStmt = $mysqli->prepare($checkStockQuery);
            $checkStockStmt->bind_param('i', $prod_id);
            $checkStockStmt->execute();
            $result = $checkStockStmt->get_result();
            $product = $result->fetch_object();

            if ($product) {
                // Check if there is enough stock
                if ($product->prod_stock >= $prod_qty) {
                    // Step 2: Deduct the purchased quantity from the stock
                    $newQty = $product->prod_stock - $prod_qty;
                    $updateStockQuery = "UPDATE rpos_products SET prod_stock = ? WHERE prod_id = ?";
                    $updateStockStmt = $mysqli->prepare($updateStockQuery);
                    $updateStockStmt->bind_param('ii', $newQty, $prod_id);
                    $updateStockStmt->execute();

                    // Step 3: Insert the order into the database, including payment method
                    $postQuery = "INSERT INTO rpos_orders (prod_qty, order_id, order_code, customer_id, customer_name, prod_id, prod_name, prod_price, payment_method) VALUES(?,?,?,?,?,?,?,?,?)";
                    $postStmt = $mysqli->prepare($postQuery);
                    // Bind parameters
                    $rc = $postStmt->bind_param('sssssssss', $prod_qty, $order_id, $order_code, $customer_id, $customer_name, $prod_id, $prod_name, $prod_price, $payment_method);
                    $postStmt->execute();

                    // Declare a variable which will be passed to alert function
                    if ($postStmt) {
                        $success = "Order Submitted" && header("refresh:1; url=payments.php");
                    } else {
                        $err = "Please Try Again Or Try Later";
                    }
                } else {
                    $err = "Not enough stock for the product.";
                }
            } else {
                $err = "Product not found.";
            }
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
                <!-- Table -->
                <div class="row">
                    <div class="col">
                        <div class="card shadow">
                            <div class="card-header border-0">
                                <h3>Please Fill All Fields</h3>
                            </div>
                            <div class="card-body">
                                <form method="POST" enctype="multipart/form-data">
                                    <div class="form-row">
                                        <div class="col-md-6">
                                            <label>Customer Name</label>
                                            <?php
                                            // Load All Customers
                                            $customer_id = $_SESSION['customer_id'];
                                            $ret = "SELECT * FROM  rpos_customers WHERE customer_id = '$customer_id' ";
                                            $stmt = $mysqli->prepare($ret);
                                            $stmt->execute();
                                            $res = $stmt->get_result();
                                            while ($cust = $res->fetch_object()) {
                                                ?>
                                                <input class="form-control" readonly name="customer_name"
                                                    value="<?php echo $cust->customer_name; ?>">
                                            <?php } ?>
                                            <input type="hidden" name="order_id" value="<?php echo $orderid; ?>"
                                                class="form-control">
                                        </div>
                                        <div class="col-md-6">
                                            <label>Order Code</label>
                                            <input type="text" readonly name="order_code"
                                                value="<?php echo $alpha; ?>-<?php echo $beta; ?>" class="form-control"
                                                value="">
                                        </div>
                                    </div>
                                    <hr>
                                    <?php
                                    $prod_id = $_GET['prod_id'];
                                    $ret = "SELECT * FROM  rpos_products WHERE prod_id = '$prod_id'";
                                    $stmt = $mysqli->prepare($ret);
                                    $stmt->execute();
                                    $res = $stmt->get_result();
                                    while ($prod = $res->fetch_object()) {
                                        ?>
                                        <div class="form-row">
                                            <div class="col-md-6">
                                                <label>Product Price (₱)</label>
                                                <input type="text" readonly name="prod_price"
                                                    value="₱ <?php echo $prod->prod_price; ?>" class="form-control">
                                            </div>
                                            <div class="col-md-6">
                                                <label>Customer Address</label>
                                                <?php
                                                // Fetch customer address
                                                $customer_id = $_SESSION['customer_id'];
                                                $ret = "SELECT customer_address FROM rpos_customers WHERE customer_id = '$customer_id'";
                                                $stmt = $mysqli->prepare($ret);
                                                $stmt->execute();
                                                $res = $stmt->get_result();
                                                $customer = $res->fetch_object();
                                                ?>
                                                <input type="text" name="customer_address" class="form-control"
                                                    value="<?php echo $customer->customer_address; ?>" readonly>
                                            </div>

                                            <div class="col-md-6">
                                                <label>Product Quantity</label>
                                                <div class="input-group">
                                                    <button type="button" class="btn btn-danger btn-custom"
                                                        id="decrease-qty">-</button>
                                                    <input type="number" name="prod_qty" id="prod_qty" class="form-control"
                                                        value="1" min="1" style="text-align: center;">
                                                    <button type="button" class="btn btn-success btn-custom"
                                                        id="increase-qty">+</button>
                                                </div>
                                                <small id="error-msg" class="text-danger" style="display: none;">Quantity cannot
                                                    be less than 1.</small>
                                            </div>

                                            <div class="col-md-6">
                                                <label>Payment Method</label>
                                                <select name="payment_method" class="form-control">
                                                    <option value="Cash">Cash</option>
                                                    <option value="Cash on Delivery">Cash on Delivery</option>
                                                    <option value="GCash">GCash</option>
                                                </select>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <br>
                                    <div class="form-row justify-content-end">
                                        <div class="col-md-0">
                                            <input type="submit" name="make" value="Make Order" class="btn btn-success"
                                                value="">
                                        </div>
                                    </div>
                                </form>
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

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const prodQtyInput = document.getElementById("prod_qty");
            const errorMsg = document.getElementById("error-msg");

            // Increase Button
            document.getElementById("increase-qty").addEventListener("click", function () {
                let currentQty = parseInt(prodQtyInput.value);
                prodQtyInput.value = currentQty + 1;
                errorMsg.style.display = "none";  // Hide error message
            });

            // Decrease Button
            document.getElementById("decrease-qty").addEventListener("click", function () {
                let currentQty = parseInt(prodQtyInput.value);
                if (currentQty > 1) {
                    prodQtyInput.value = currentQty - 1;
                    errorMsg.style.display = "none";  // Hide error message
                } else {
                    errorMsg.style.display = "block";  // Show error message if less than 1
                }
            });

            // Ensure no negative quantity is submitted
            prodQtyInput.addEventListener("input", function () {
                let currentQty = parseInt(prodQtyInput.value);
                if (currentQty < 1) {
                    errorMsg.style.display = "block";  // Show error message if less than 1
                } else {
                    errorMsg.style.display = "none";  // Hide error message if valid
                }
            });
        });
    </script>