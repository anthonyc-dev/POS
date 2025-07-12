<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
include('config/code-generator.php');

check_login();

if (isset($_POST['pay'])) {
  // Prevent Posting Blank Values
  if (empty($_POST["pay_code"]) || empty($_POST["pay_amt"]) || empty($_POST['pay_method'])) {
    $err = "Blank Values Not Accepted";
  } else {
    $pay_Code = $_POST['pay_code'];

    if (strlen($pay_Code) < 10) {
      $err = "Payment Code Verification Failed, Please Try Again";
    } elseif (strlen($pay_Code) > 10) {
      $err = "Payment Code Verification Failed, Please Try Again";
    } else {
      $pay_code = $_POST['pay_code'];
      $order_code = $_GET['order_code'];
      $customer_id = $_GET['customer_id'];
      $pay_amt = $_POST['pay_amt'];
      $pay_method = $_POST['pay_method'];
      $pay_id = $_POST['pay_id'];
      $order_status = $_GET['order_status'];

      // Insert Captured information into a database table
      $postQuery = "INSERT INTO rpos_payments (pay_id, pay_code, order_code, customer_id, pay_amt, pay_method) VALUES(?,?,?,?,?,?)";
      $upQry = "UPDATE rpos_orders SET order_status =? WHERE order_code =?";

      $postStmt = $mysqli->prepare($postQuery);
      $upStmt = $mysqli->prepare($upQry);

      // Bind parameters
      $rc = $postStmt->bind_param('ssssss', $pay_id, $pay_code, $order_code, $customer_id, $pay_amt, $pay_method);
      $rc = $upStmt->bind_param('ss', $order_status, $order_code);

      $postStmt->execute();
      $upStmt->execute();

      // Declare a variable to be passed to alert function
      if ($upStmt && $postStmt) {
        $success = "Paid" && header("refresh:1; url=payments_reports.php");
      } else {
        $err = "Please Try Again Or Try Later";
      }
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
    $order_code = $_GET['order_code'];
    $ret = "SELECT * FROM rpos_orders WHERE order_code ='$order_code'";
    $stmt = $mysqli->prepare($ret);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($order = $res->fetch_object()) {
      $total = ((float) ($order->prod_price ?? 0) * (int) ($order->prod_qty ?? 0));
      ?>

      <!-- Header -->
      <div style="background-image: url(../admin/assets/img/theme/customer.png); background-size: cover;"
        class="header pb-8 pt-5 pt-md-8">
        <span class="mask bg-gradient-dark opacity-8"></span>
        <div class="container-fluid">
          <div class="header-body">
          </div>
        </div>
      </div>

      <!-- Page content -->
      <div class="container-fluid mt--8">
        <!-- Form Section -->
        <div class="row justify-content-center">
          <div class="col-lg-10">
            <div class="card shadow-lg border-0">
              <div class="card-header bg-gradient-primary text-white">
                <h4 class="text-center mb-0">Complete Your Purchase</h4>
              </div>
              <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                  <!-- Payment Details -->
                  <h5 class="text-muted mb-4">Payment Details</h5>
                  <div class="form-group row align-items-center">
                    <label for="pay_id" class="col-md-3 col-form-label">Payment ID</label>
                    <div class="col-md-9">
                      <input type="text" name="pay_id" id="pay_id" readonly value="<?php echo $payid; ?>"
                        class="form-control">
                    </div>
                  </div>
                  <div class="form-group row align-items-center">
                    <label for="pay_code" class="col-md-3 col-form-label">Payment Code</label>
                    <div class="col-md-9">
                      <input type="text" name="pay_code" id="pay_code" value="<?php echo $mpesaCode; ?>"
                        class="form-control">
                    </div>
                  </div>

                  <!-- Amount and Payment Method -->
                  <h5 class="text-muted mb-4">Payment Information</h5>
                  <div class="form-group row align-items-center">
                    <label for="pay_amt" class="col-md-3 col-form-label">Amount (₱)</label>
                    <div class="col-md-9">
                      <input type="text" name="pay_amt" id="pay_amt" readonly value="<?php echo $total; ?>"
                        class="form-control">
                    </div>
                  </div>
                  <div class="form-group row align-items-center">
                    <label for="pay_method" class="col-md-3 col-form-label">Payment Method</label>
                    <div class="col-md-9">
                      <select class="form-control" name="pay_method" id="pay_method">
                        <option selected>Cash</option>
                        <option>GCash</option>
                      </select>
                    </div>
                  </div>

                  <h5 class="text-muted mb-4">Shipping Information</h5>
                  <div class="form-group row align-items-center">
                    <label for="pay_amt" class="col-md-3 col-form-label">Customer Address</label>
                    <div class="col-md-9">
                    <?php
                      // Fetch customer address based on session customer_id
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
                  </div>


                  <!-- <div class="form-group row align-items-center">
                    <div class="col-md-12 text-center">
                      <button type="submit" name="pay" class="btn btn-success">Complete Payment</button>
                    </div>
                  </div> -->
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
    </div>

    <!-- Argon Scripts -->
    <?php
    require_once('partials/_scripts.php');
    }
    ?>
</body>

</html>