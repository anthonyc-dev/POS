<?php session_start();
include('config/config.php');

// login functionality 
if (isset($_POST['login'])) {
    $staff_email = $_POST['staff_email'];
    $staff_password = $_POST['staff_password']; // Use plain text password directly

    // Check if email or password is empty
    if (empty($staff_email) || empty($staff_password)) {
        $err = "Please fill in both fields.";
    } else {
        // Query the database
        $stmt = $mysqli->prepare("SELECT staff_email, staff_password, staff_id FROM rpos_staff WHERE (staff_email =? AND staff_password =?)");
        $stmt->bind_param('ss', $staff_email, $staff_password);
        $stmt->execute();
        $stmt->bind_result($staff_email, $staff_password, $staff_id);
        $rs = $stmt->fetch();
        
        $_SESSION['staff_id'] = $staff_id;

        if ($rs) {
            // If successful
            header("location: dashboard.php");
        } else {
            $err = "Incorrect Authentication Credentials";
        }
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
                    <h3 class="text-center mb-4" style="color: #5c6bc0;">Cashier Login</h3>
                    <div class="form-group mb-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="ni ni-email-83"></i></span>
                            </div>
                            <input class="form-control" required name="staff_email" placeholder=" Email" type="email" />
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            </div>
                            <input class="form-control" id="password" required name="staff_password" placeholder=" Password" type="password" />
                            <div class="input-group-append">
                                <span class="input-group-text" id="togglePassword" style="cursor: pointer;">
                                    <i class="fas fa-eye"></i>
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
                                togglePassword.innerHTML = '<i class="fas fa-eye-slash"></i>';
                            } else {
                                passwordField.type = "password";
                                togglePassword.innerHTML = '<i class="fas fa-eye"></i>';
                            }
                        });
                    </script>

                    <div class="form-group custom-control custom-checkbox mb-3">
                        <input class="custom-control-input" id="customCheckLogin" type="checkbox" />
                        <label class="custom-control-label" for="customCheckLogin">
                            <span class="text-muted">Remember me</span>
                        </label>
                    </div>

                    <div class="form-group text-center">
                        <button type="submit" name="login" class="btn btn-primary my-4">Log In</button>
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

</html>
