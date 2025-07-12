<?php 
session_start();
include('config/config.php');

// Login functionality 
if (isset($_POST['login'])) {
    $customer_email = $_POST['customer_email'];
    $customer_password = $_POST['customer_password']; // No hashing applied

    $stmt = $mysqli->prepare("SELECT customer_email, customer_password, customer_id FROM rpos_customers WHERE (customer_email =? AND customer_password =?)"); 
    $stmt->bind_param('ss', $customer_email, $customer_password); // Bind parameters
    $stmt->execute(); 
    $stmt->bind_result($customer_email, $customer_password, $customer_id); 
    $rs = $stmt->fetch();
    $_SESSION['customer_id'] = $customer_id;

    if ($rs) {
        // Successful login
        header("location: dashboard.php");
    } else {
        $err = "Incorrect Authentication Credentials";
    }
}

require_once('partials/_head.php');
?>


    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #e63946, #f1faee, #a8dadc);
            /* Darker reddish sunset gradient */
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .header {
            padding: 40px 0;
            background: rgba(0, 0, 0, 0.7);
        }

        .header h1 {
            font-size: 2.5rem;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .main-content {
            width: 100%;
            max-width: 450px;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .card {
            border-radius: 10px;
            box-shadow: 0px 5px 20px rgba(0, 0, 0, 0.2);
        }

        .card-body {
            padding: 40px;
        }

        .form-group input {
            font-size: 16px;
            padding: 12px;
            border-radius: 10px;
            border: 1px solid #ddd;
            width: 100%;
            background-color: #f8f8f8;
            margin-bottom: 20px;
            transition: border-color 0.3s, background-color 0.3s;
        }

        .form-group input:focus {
            border-color: #5c6bc0;
            background-color: #fff;
        }

        .btn {
            width: 100%;
            padding: 14px;
            font-size: 18px;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .btn-primary {
            background-color: #5c6bc0;
            border: none;
        }

        .btn-primary:hover {
            background-color: #3f4c8c;
        }

        .btn-success {
            background-color: #28a745;
            border: none;
        }

        .btn-success:hover {
            background-color: #218838;
        }

        .form-group label {
            font-size: 14px;
            color: #333;
            margin-bottom: 5px;
        }

        .input-group-text {
            background-color: #5c6bc0;
            color: #fff;
        }

        .forgot-password {
            font-size: 14px;
            color: #777;
            text-decoration: none;
        }

        .forgot-password:hover {
            color: #5c6bc0;
        }

        .brand-logo {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 30px;
        }

        .brand-logo img {
            height: 50px;
            /* Adjust the size of your logo */
            margin-right: 10px;
        }

        .brand-logo h2 {
            font-family: 'Poppins', sans-serif;
            color: #5c6bc0;
            font-size: 2rem;
            font-weight: bold;
        }
    </style>

    <body>
        <div class="main-content">
            <div class="card bg-white shadow border-0">
                <div class="card-body">
                    <!-- Henry's Hardware Branding -->
                    <div class="brand-logo">
                        <h2>Henry's Hardware</h2>
                    </div>

                    <form method="post" role="form">
                        <h3 class="text-center mb-4" style="color: #5c6bc0;">Customer Login</h3>
                        <div class="form-group mb-3">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="ni ni-email-83"></i></span>
                                </div>
                                <input class="form-control" required name="customer_email" placeholder=" Email"
                                    type="email" />
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <!-- Font Awesome lock icon -->
                                </div>
                                <input class="form-control" id="password" required name="customer_password"
                                    placeholder=" Password" type="password" />
                                <div class="input-group-append">
                                    <span class="input-group-text" id="togglePassword" style="cursor: pointer;">
                                        <i class="fas fa-eye"></i> <!-- Font Awesome eye icon for show password -->
                                    </span>
                                </div>
                            </div>
                        </div>

                        <script>
                            const togglePassword = document.getElementById("togglePassword");
                            const passwordField = document.getElementById("password");

                            togglePassword.addEventListener("click", function () {
                                // Toggle password visibility
                                if (passwordField.type === "password") {
                                    passwordField.type = "text";
                                    togglePassword.innerHTML = '<i class="fas fa-eye-slash"></i>'; // Font Awesome eye-slash icon for hide password
                                } else {
                                    passwordField.type = "password";
                                    togglePassword.innerHTML = '<i class="fas fa-eye"></i>'; // Font Awesome eye icon for show password
                                }
                            });
                        </script>


                        <div class="form-group custom-control custom-checkbox mb-3">
                            <input class="custom-control-input" id="customCheckLogin" type="checkbox" />
                            <label class="custom-control-label" for="customCheckLogin">
                                <span class="text-muted">Remember me</span>
                            </label>
                        </div>

                        <div class="text-center mt-3">
                            <a href="forgot_password.php" class="forgot-password">Forgot password?</a>
                        </div>

                        <div class="form-group text-center">
                            <button type="submit" name="login" class="btn btn-primary my-4">Log In</button>
                        </div>

                        <div class="form-group text-center">
                            <a href="create_account.php" class="btn btn-success">Create Account</a>
                        </div>

                        <!-- <div class="text-center mt-3">
                            <a href="forgot_password.php" class="forgot-password">Forgot password?</a>
                        </div> -->
                    </form>
                </div>
            </div>
        </div>

        <?php require_once('partials/_scripts.php'); ?>
    </body>
    <script>
        const togglePassword = document.getElementById("togglePassword");
        const passwordField = document.getElementById("password");

        togglePassword.addEventListener("click", function () {
            // Toggle password visibility
            if (passwordField.type === "password") {
                passwordField.type = "text";
                togglePassword.innerHTML = '<i class="ni ni-eye-off"></i>';
            } else {
                passwordField.type = "password";
                togglePassword.innerHTML = '<i class="ni ni-eye"></i>';
            }
        });
    </script>

    </html>