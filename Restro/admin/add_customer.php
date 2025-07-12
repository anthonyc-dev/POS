<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
include('config/code-generator.php');
check_login();

//Add Customer
if (isset($_POST['addCustomer'])) {
  //Prevent Posting Blank Values
  if (empty($_POST["customer_phoneno"]) || empty($_POST["customer_name"]) || empty($_POST['customer_email']) || empty($_POST['customer_password']) || empty($_POST['customer_address'])) {
    $err = "Blank Values Not Accepted";
  } else {
    $customer_name = $_POST['customer_name'];
    $customer_phoneno = $_POST['customer_phoneno'];
    $customer_email = $_POST['customer_email'];
    $customer_password = $_POST['customer_password']; // Plaintext password
    $customer_address = $_POST['customer_address'];
    $customer_id = $_POST['customer_id'];

    // Validate phone number (must be at least 11 digits)
    if (strlen($customer_phoneno) < 11) {
      $err = "Phone number must be at least 11 digits.";
    }

    // Check if email already exists
    $emailCheckQuery = "SELECT * FROM rpos_customers WHERE customer_email = ?";
    $emailCheckStmt = $mysqli->prepare($emailCheckQuery);
    $emailCheckStmt->bind_param('s', $customer_email);
    $emailCheckStmt->execute();
    $emailCheckResult = $emailCheckStmt->get_result();
    if ($emailCheckResult->num_rows > 0) {
      $err = "Email already exists.";
    }

    // Check if phone number already exists
    $phoneCheckQuery = "SELECT * FROM rpos_customers WHERE customer_phoneno = ?";
    $phoneCheckStmt = $mysqli->prepare($phoneCheckQuery);
    $phoneCheckStmt->bind_param('s', $customer_phoneno);
    $phoneCheckStmt->execute();
    $phoneCheckResult = $phoneCheckStmt->get_result();
    if ($phoneCheckResult->num_rows > 0) {
      $err = "Phone number already exists.";
    }

    // Validate password strength (at least one uppercase, one lowercase, and one special character)
    if (!preg_match('/[A-Z]/', $customer_password)) {
      $err = "Password must contain at least one uppercase letter.";
    } elseif (!preg_match('/[a-z]/', $customer_password)) {
      $err = "Password must contain at least one lowercase letter.";
    } elseif (!preg_match('/[\W_]/', $customer_password)) { // Special characters like !@#$%^&*()
      $err = "Password must contain at least one special character.";
    }

    // If no errors, insert customer data into database
    if (!isset($err)) {
      $postQuery = "INSERT INTO rpos_customers (customer_id, customer_name, customer_phoneno, customer_email, customer_password, customer_address) VALUES(?,?,?,?,?,?)";
      $postStmt = $mysqli->prepare($postQuery);
      // Bind parameters
      $rc = $postStmt->bind_param('ssssss', $customer_id, $customer_name, $customer_phoneno, $customer_email, $customer_password, $customer_address);
      $postStmt->execute();

      // Declare a variable to be passed to alert function
      if ($postStmt) {
        $success = "Customer Added" && header("refresh:1; url=customes.php");
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
    ?>
    <!-- Header -->
    <div style="background-image: url(../admin/assets/img/theme/adminimg.avif); background-size: cover;"
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
              <form method="POST">
                <div class="form-row">
                  <div class="col-md-6">
                    <label>Customer Name</label>
                    <input type="text" name="customer_name" class="form-control">
                    <input type="hidden" name="customer_id" value="<?php echo $cus_id; ?>" class="form-control">
                  </div>
                  <div class="col-md-6">
                    <label>Customer Phone Number</label>
                    <input type="text" name="customer_phoneno" class="form-control" value="">
                  </div>
                </div>
                <hr>
                <div class="form-row">
                  <div class="col-md-6">
                    <label>Customer Email</label>
                    <input type="email" name="customer_email" class="form-control" value="">
                  </div>
                  <div class="col-md-6">
                    <label>Customer Password</label>
                    <input type="password" name="customer_password" class="form-control" value="">
                  </div>
                  <div class="col-md-12">
                    <label>Customer Address</label>
                    <input type="text" name="customer_address" class="form-control">
                  </div>
                </div>
                <br>
                <div class="form-row">
                  <div class="col-md-6">
                    <input type="submit" name="addCustomer" value="Add Customer" class="btn btn-success" value="">
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
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