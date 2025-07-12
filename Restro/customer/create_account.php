<?php
session_start();
include('config/config.php');

// Add Customer
if (isset($_POST['addCustomer'])) {
    // Prevent Posting Blank Values
    if (
        empty($_POST["customer_phoneno"]) ||
        empty($_POST["customer_name"]) ||
        empty($_POST['customer_email']) ||
        empty($_POST['customer_password']) ||
        empty($_POST['customer_address'])
    ) {
        $err = "Blank Values Not Accepted";
    } else {
        $customer_name = $_POST['customer_name'];
        $customer_phoneno = $_POST['customer_phoneno'];
        $customer_email = $_POST['customer_email'];
        $customer_password = $_POST['customer_password']; // No Hashing
        $customer_address = $_POST['customer_address'];
        $customer_id = $_POST['customer_id'];

        // Validate Email Format
        if (!filter_var($customer_email, FILTER_VALIDATE_EMAIL)) {
            $err = "Invalid email format.";
        }

        // Validate Email Existence
        $checkEmailQuery = "SELECT * FROM rpos_customers WHERE customer_email = ?";
        $checkEmailStmt = $mysqli->prepare($checkEmailQuery);
        $checkEmailStmt->bind_param('s', $customer_email);
        $checkEmailStmt->execute();
        $result = $checkEmailStmt->get_result();

        if ($result->num_rows > 0) {
            $err = "Email already exists. Please use a different email.";
        } else {
            // Password Validation: Must contain uppercase, lowercase, and special character
            // Password Validation: Must contain uppercase, lowercase, special character, and max length of 11
            if (!preg_match('/[A-Z]/', $customer_password)) {
                $err = "Password must contain at least one uppercase letter.";
            } elseif (!preg_match('/[a-z]/', $customer_password)) {
                $err = "Password must contain at least one lowercase letter.";
            } elseif (!preg_match('/[\W_]/', $customer_password)) {
                $err = "Password must contain at least one special character.";
            } elseif (strlen($customer_password) > 11) {
                $err = "Password must not exceed 11 characters.";
            } else {
                // Insert Captured Information to Database Table
                $postQuery = "INSERT INTO rpos_customers (customer_id, customer_name, customer_phoneno, customer_email, customer_password, customer_address) 
                  VALUES(?,?,?,?,?,?)";
                $postStmt = $mysqli->prepare($postQuery);
                $rc = $postStmt->bind_param('ssssss', $customer_id, $customer_name, $customer_phoneno, $customer_email, $customer_password, $customer_address);
                $postStmt->execute();

                // Check if the query executed successfully
                if ($postStmt) {
                    $success = "Customer Account Created";
                    header("refresh:1; url=index.php");
                    exit;
                } else {
                    $err = "Please Try Again Or Try Later";
                }
            }
        }
    }
}

// Fetch the customer ID (Make sure you have a proper code to generate it in 'code-generator.php')


require_once('partials/_head.php');
require_once('config/code-generator.php');
?>

<style>
    body {
        font-family: 'Arial', sans-serif;
        background: linear-gradient(135deg, #e63946, #f1faee, #a8dadc);
    }

    .container {
        margin-top: 50px;
    }

    .card {
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        background-color: white;
        /* Card background color */
    }

    .form-control {
        border-radius: 10px;
        padding: 15px;
        font-size: 14px;
    }

    .input-group-prepend {
        border-radius: 10px 0 0 10px;
    }

    .input-group-text {
        border-radius: 10px;
        background-color: #3498db;
        color: white;
    }

    .btn-primary {
        background-color: #3498db;
        border-radius: 10px;
        width: 100%;
        padding: 15px;
        font-size: 16px;
        margin-top: 20px;
    }

    .btn-success {
        background-color: #2ecc71;
        border-radius: 10px;
        width: 100%;
        padding: 15px;
        font-size: 16px;
        margin-top: 20px;
    }

    .alert {
        border-radius: 10px;
        font-size: 14px;
    }

    .toggle-password {
        cursor: pointer;
    }

    .header h1 {
        font-size: 30px;
        color: white;
        margin-bottom: 20px;
    }

    .input-group {
        margin-bottom: 15px;
    }

    .header-body {
        margin-bottom: 50px;
    }

    .card-body {
        padding: 30px;
    }

    /* Media Queries */
    @media (max-width: 768px) {
        .container {
            margin-top: 30px;
        }

        .card {
            padding: 15px;
        }

        .btn-primary,
        .btn-success {
            width: 100%;
        }
    }

    #password-strength {
        width: 100%;
        height: 8px;
        margin-top: 10px;
        border-radius: 5px;
        background-color: #ddd;
    }

    #password-strength-bar {
        height: 100%;
        width: 0;
        border-radius: 5px;
    }

    .weak {
        background-color: red;
    }

    .medium {
        background-color: yellow;
    }

    .strong {
        background-color: green;
    }
</style>

<body>
    <div class="main-content">
        <div class="header">
            <div class="container">
                <div class="header-body text-center mb-7">
                    <h1> .</h1>
                </div>
            </div>
        </div>

        <div class="container mt--8 pb-5">
            <div class="row justify-content-center">
                <div class="col-lg-5 col-md-7">
                    <div class="card bg-white shadow border-0">
                        <div class="card-body px-lg-5 py-lg-5">
                            <form method="post" role="form">
                                <?php if (isset($err)): ?>
                                    <div class="alert alert-danger">
                                        <?php echo $err; ?>
                                    </div>
                                <?php elseif (isset($success)): ?>
                                    <div class="alert alert-success">
                                        <?php echo $success; ?>
                                    </div>
                                <?php endif; ?>

                                <div class="form-group">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        </div>
                                        <input class="form-control" required name="customer_name"
                                            placeholder=" Full Name" type="text">
                                        <input class="form-control" value="<?php echo $cus_id; ?>" required
                                            name="customer_id" type="hidden">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                        </div>
                                        <input class="form-control" required name="customer_phoneno"
                                            placeholder=" Phone Number" type="text">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                        </div>
                                        <input class="form-control" required name="customer_address"
                                            placeholder=" Address" type="text">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="ni ni-email-83"></i></span>
                                        </div>
                                        <input class="form-control" required name="customer_email" placeholder=" Email"
                                            type="email">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="ni ni-lock-circle-open"></i></span>
                                        </div>
                                        <input class="form-control" id="customer_password" required name="customer_password" placeholder=" Password" type="password" onkeyup="checkPasswordStrength()">
                                        <div class="input-group-append">
                                            <span class="input-group-text toggle-password" onclick="togglePasswordVisibility()">
                                                <i class="fas fa-eye"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <!-- Password Strength Bar -->
                                    <div id="password-strength">
                                        <div id="password-strength-bar"></div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <button type="submit" name="addCustomer" class="btn btn-primary my-4">Create
                                        Account</button>
                                    <a href="index.php" class="btn btn-success pull-right">Log In</a>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-6">
                            <!--<a href="../admin/forgot_pwd.php" target="_blank" class="text-light"><small>Forgot password?</small></a> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php require_once('partials/_footer.php'); ?>
    <!-- Argon Scripts -->
    <?php require_once('partials/_scripts.php'); ?>
</body>

<script>
    function togglePasswordVisibility() {
        const passwordField = document.getElementById('customer_password');
        const toggleIcon = document.querySelector('.toggle-password i');

        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            passwordField.type = 'password';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    }

    function checkPasswordStrength() {
    const password = document.getElementById('customer_password').value;
    const strengthBar = document.getElementById('password-strength-bar');
    const passwordLength = password.length;
    let strength = 0;

    // Length check
    if (passwordLength >= 6) {
        strength++;
    }
    // Uppercase check
    if (/[A-Z]/.test(password)) {
        strength++;
    }
    // Lowercase check
    if (/[a-z]/.test(password)) {
        strength++;
    }
    // Special character check
    if (/[\W_]/.test(password)) {
        strength++;
    }

    // Set strength bar color and width based on password strength
    switch (strength) {
        case 1:
            strengthBar.style.width = '25%';
            strengthBar.className = 'weak';
            break;
        case 2:
            strengthBar.style.width = '50%';
            strengthBar.className = 'medium';
            break;
        case 3:
        case 4:
            strengthBar.style.width = '100%';
            strengthBar.className = 'strong';
            break;
        default:
            strengthBar.style.width = '0';
            strengthBar.className = '';
            break;
    }
}
</script>

</html>