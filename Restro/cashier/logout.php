<?php
session_start();
unset($_SESSION['staff_id']);
session_destroy();
header("Location: ../cashier/index.php");
exit;
