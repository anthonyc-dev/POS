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
            $new_password = sha1(md5($_POST['new_password']));
            $stmt = $mysqli->prepare("UPDATE rpos_admin SET admin_password = ? WHERE admin_email = ?");
            $stmt->bind_param('ss', $new_password, $email);
            $stmt->execute();

            // Delete the token after successful reset
            $stmt = $mysqli->prepare("DELETE FROM password_resets WHERE token = ?");
            $stmt->bind_param('s', $token);
            $stmt->execute();

            echo "Password reset successfully. <a href='login.php'>Log in</a>";
        }
    } else {
        echo "Invalid or expired token.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Reset Password</title>
</head>
<body>
    <form method="post">
        <h2>Reset Password</h2>
        <input type="password" name="new_password" required placeholder="Enter new password">
        <button type="submit" name="reset_password">Reset Password</button>
    </form>
</body>
</html>
