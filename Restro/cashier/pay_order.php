<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
include('config/code-generator.php');

check_login();

if (isset($_POST['pay'])) {
  // Prevent Posting Blank Values
  if (empty($_POST["pay_code"]) || empty($_POST["pay_amt"]) || empty($_POST['amount_received']) || empty($_POST['pay_method'])) {
    $err = "Blank Values Not Accepted";
  } else {

    $pay_code = $_POST['pay_code'];
    $order_code = $_GET['order_code'];
    $customer_id = $_GET['customer_id'];
    $pay_amt = $_POST['pay_amt'];
    $pay_id = $_POST['pay_id'];
    $amount_received = $_POST['amount_received'];  // New field for amount received
    $pay_method = $_POST['pay_method'];  // Capture the payment method

    // Get the change value, or set it to 0 if it is not provided
    $change = isset($_POST['change']) ? $_POST['change'] : 0;  // Default to 0 if change is not set

    $order_status = $_GET['order_status'];

    // Insert Captured information into the database table
    $postQuery = "INSERT INTO rpos_payments (pay_id, pay_code, order_code, customer_id, pay_amt, amount_received, `change`, pay_method) VALUES(?,?,?,?,?,?,?,?)";
    $upQry = "UPDATE rpos_orders SET order_status =? WHERE order_code =?";

    $postStmt = $mysqli->prepare($postQuery);
    $upStmt = $mysqli->prepare($upQry);

    // Bind parameters
    $rc = $postStmt->bind_param('ssssssss', $pay_id, $pay_code, $order_code, $customer_id, $pay_amt, $amount_received, $change, $pay_method);
    $rc = $upStmt->bind_param('ss', $order_status, $order_code);

    $postStmt->execute();
    $upStmt->execute();

    // Declare a variable which will be passed to alert function
    if ($upStmt && $postStmt) {
      header("refresh:2; url=payments.php"); // Redirect after 2 seconds to the receipts page
    } else {
      $err = "Please Try Again Or Try Later";
    }
  }
}


require_once('partials/_head.php');
?>

<head>
  <!-- Include SweetAlert2 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.5.5/dist/sweetalert2.min.css" rel="stylesheet">

  <!-- Include SweetAlert2 JS -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.5.5/dist/sweetalert2.min.js"></script>
</head>

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
    $ret = "SELECT * FROM  rpos_orders WHERE order_code ='$order_code' ";
    $stmt = $mysqli->prepare($ret);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($order = $res->fetch_object()) {
      $total = ($order->prod_price * $order->prod_qty);

      ?>

      <!-- Header -->
      <div style="background-image: url(../admin/assets/img/theme/adminimg.avif); background-size: cover;"
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
          <div class="col-lg-8">
            <div class="card shadow-lg border-0">
              <div class="card-header bg-gradient-primary text-white text-center">
                <h4 class="mb-0">Customer Payment</h4>
              </div>
              <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                  <div class="form-row">
                    <div class="col-md-6">
                      <label>Transaction ID</label>
                      <input type="text" name="pay_id" readonly value="<?php echo $payid; ?>" class="form-control">
                    </div>
                    <div class="col-md-6">
                      <label>Transaction Code</label>
                      <input type="text" name="pay_code" value="<?php echo $mpesaCode; ?>" class="form-control">
                    </div>
                  </div>
                  <hr>
                  <div class="form-row">
                    <div class="col-md-6">
                      <label>Total Payment (₱)</label>
                      <input type="text" name="pay_amt" readonly value="<?php echo $total; ?>" class="form-control">
                    </div>
                    <div class="col-md-6">
                      <label>Payment Method</label>
                      <select name="pay_method" class="form-control">
                        <option value="Cash">Cash</option>
                        <option value="Cash on Delivery">Cash on Delivery</option>
                        <option value="GCash">GCash</option>
                      </select>
                    </div>
                  </div>
                  <br>
                  <div class="form-row">
                    <div class="col-md-6">
                      <label>Amount Given (₱)</label>
                      <input type="text" name="amount_received" class="form-control" placeholder="Enter amount received">
                    </div>
                    <div class="col-md-6">
                      <label>Change (₱)</label>
                      <input type="text" name="change" class="form-control" placeholder="Change will be calculated"
                        readonly>
                    </div>
                  </div>
                  <br>
                  <div class="form-row">
                    <div class="col-md-12 text-center">
                      <input type="submit" name="pay" value="Pay Order" class="btn btn-success w-100">
                    </div>
                  </div>
                  <!-- Hidden input to send the calculated change -->
                  <input type="hidden" name="change" id="change-field">
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
    }
    ?>
</body>

</html>

<script>
  // Add event listener to Amount Received input field for real-time calculation
  document.querySelector('input[name="amount_received"]').addEventListener('input', function () {
    var amountReceived = parseFloat(this.value) || 0;  // Get value of Amount Received
    var totalPayment = parseFloat(document.querySelector('input[name="pay_amt"]').value) || 0;  // Get value of Total Payment

    // Calculate change
    var change = amountReceived - totalPayment;

    // Set the change value to the Change field
    document.querySelector('input[name="change"]').value = change.toFixed(2);  // Limit to 2 decimal places

    // Set the hidden input field to send the change value to the backend
    document.getElementById('change-field').value = change.toFixed(2);
  });

  // Form submission validation
  document.querySelector('form').addEventListener('submit', function (event) {
    var amountReceived = parseFloat(document.querySelector('input[name="amount_received"]').value) || 0;
    var totalPayment = parseFloat(document.querySelector('input[name="pay_amt"]').value) || 0;

    if (amountReceived < totalPayment) {
      event.preventDefault();  // Prevent form submission
      Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: 'Amount Given cannot be less than Total Payment!',
      });
    } else {
      // After the successful validation, show success alert
      Swal.fire({
        icon: 'success',
        title: 'Payment Successful',
        timer: 2000,
        text: 'The payment has been successfully processed!',
      }).then(() => {
        // Redirect to the receipts page after 2 seconds
        window.location.href = 'payments.php';
      });
    }
  });
</script>