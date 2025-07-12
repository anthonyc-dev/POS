<?php
session_start();
include('config/config.php');

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Verify the token
    $stmt = $mysqli->prepare("SELECT email FROM password_resets WHERE token = ?");
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $stmt->bind_result($email);
    $stmt->fetch();
    $stmt->close();

    if ($email) {
        if (isset($_POST['reset_password'])) {
            $new_password = $_POST['new_password']; // No hashing
            $stmt = $mysqli->prepare("UPDATE rpos_admin SET admin_password = ? WHERE admin_email = ?");
            $stmt->bind_param('ss', $new_password, $email);
            $stmt->execute();

            // Delete the token after successful reset
            $stmt = $mysqli->prepare("DELETE FROM password_resets WHERE token = ?");
            $stmt->bind_param('s', $token);
            $stmt->execute();

            $success = "Password reset successfully. <a href='login.php'>Log in</a>";
        }
    } else {
        $err = "Invalid or expired token.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="card p-4 shadow" style="width: 100%; max-width: 400px;">
            <h2 class="text-center mb-4">Reset Password</h2>
            <?php if (isset($err)): ?>
                <div class="alert alert-danger"><?php echo $err; ?></div>
            <?php endif; ?>
            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php else: ?>
                <form method="post">
                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password</label>
                        <input type="password" name="new_password" id="new_password" class="form-control" required placeholder="Enter new password">
                    </div>
                    <button type="submit" name="reset_password" class="btn btn-primary w-100">Reset Password</button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
