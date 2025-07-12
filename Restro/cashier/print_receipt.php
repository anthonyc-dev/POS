<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Henry's Hardware Maranding Branch</title>
    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="../admin/assets/img/icons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../admin/assets/img/icons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../admin/assets/img/icons/favicon-16x16.png">
    <link rel="manifest" href="../admin/assets/img/icons/site.webmanifest">
    <link rel="mask-icon" href="../admin/assets/img/icons/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">
    <link href="assets/css/bootstrap.css" rel="stylesheet" id="bootstrap-css">
    <script src="assets/js/bootstrap.js"></script>
    <script src="assets/js/jquery.js"></script>
    <style>
        body {
            margin-top: 20px;
        }

        .table th,
        .table td {
            text-align: center;
            padding: 8px;
        }

        table {
            width: 100%;
        }

        .table-bordered th,
        .table-bordered td {
            border: 1px solid #ddd;
        }

        .table td {
            padding-left: 10px;
            padding-right: 10px;
        }

        .text-right {
            text-align: right;
        }

        .receipt-total,
        .receipt-amount {
            text-align: left;
        }

        .text-danger {
            color: red;
        }

        .text-success {
            color: green;
        }
    </style>
</head>

<?php
$order_code = $_GET['order_code'];
$ret = "SELECT o.*, c.customer_address as customer_address FROM rpos_orders o 
        LEFT JOIN rpos_customers c ON o.customer_id = c.customer_id WHERE order_code = '$order_code'";
$stmt = $mysqli->prepare($ret);
$stmt->execute();
$res = $stmt->get_result();
while ($order = $res->fetch_object()) {
    $total = ($order->prod_price * $order->prod_qty);

    // Fetch payment details (Amount Received and Change)
    $payment_query = "SELECT amount_received, `change` FROM rpos_payments WHERE order_code = ?";
    $payment_stmt = $mysqli->prepare($payment_query);
    $payment_stmt->bind_param("s", $order_code);
    $payment_stmt->execute();
    $payment_res = $payment_stmt->get_result();
    $payment_data = $payment_res->fetch_object();

    // Set default values for Amount Received and Change if no payment data found
    $amount_received = $payment_data ? $payment_data->amount_received : 0;
    $change = $payment_data ? $payment_data->change : 0;
    ?>

    <body>
        <div class="container">
            <div class="row">
                <div id="Receipt" class="well col-xs-10 col-sm-10 col-md-6 col-xs-offset-1 col-sm-offset-1 col-md-offset-3">
                    <div class="row">
                        <div class="col-xs-6 col-sm-6 col-md-6">
                            <address>
                                <strong>Henry's Hardware Store</strong><br>
                                9211<br>
                                Brgy. Maranding, Lala, Lanao del Norte<br>
                                +639095300049
                            </address>
                        </div>
                        <div class="col-xs-6 col-sm-6 col-md-6 text-right">
                            <p><em>Date: <?php echo date('d/M/Y g:i', strtotime($order->created_at)); ?></em></p>
                            <p><em class="text-success">Receipt #: <?php echo $order->order_code; ?></em></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <p><strong>Name:</strong> <?php echo $order->customer_name; ?></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <p><strong>Address:</strong> <?php echo $order->customer_address; ?></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="text-center">
                            <h2>Receipt</h2>
                        </div>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Quantity</th>
                                    <th class="text-center">Unit Price</th>
                                    <th class="text-center">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="col-md-6"><em> <?php echo $order->prod_name; ?> </em></td>
                                    <td class="col-md-2" style="text-align: center"> <?php echo $order->prod_qty; ?></td>
                                    <td class="col-md-2 text-center">₱<?php echo number_format($order->prod_price, 2); ?>
                                    </td>
                                    <td class="col-md-2 text-center">₱<?php echo number_format($total, 2); ?></td>
                                </tr>
                                <tr>
                                    <td colspan="2"></td>
                                    <td class="text-right">
                                        <p><strong>Subtotal: </strong></p>
                                        <p><strong>Amount Received: </strong></p>
                                        <p><strong>Change: </strong></p>
                                    </td>
                                    <td class="text-center">
                                        <p><strong>₱<?php echo number_format($total, 2); ?></strong></p>
                                        <p><strong>₱<?php echo number_format($amount_received, 2); ?></strong></p>
                                        <p><strong>₱<?php echo number_format($change, 2); ?></strong></p>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2"></td>
                                    <td class="text-right">
                                        <h4><strong>Total: </strong></h4>
                                    </td>
                                    <td class="text-center text-danger">
                                        <h4><strong>₱<?php echo number_format($total, 2); ?></strong></h4>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <!-- Display the QR code (image source to be replaced with actual path) -->
                        <div class="text-center">
                            <img src="../admin/assets/img/theme/qrcode.jpg" alt="QR Code" width="auto" height="500">
                        </div>
                    </div>
                </div>
                <div class="well col-xs-10 col-sm-10 col-md-6 col-xs-offset-1 col-sm-offset-1 col-md-offset-3">
                    <button id="print" onclick="printContent('Receipt');"
                        class="btn btn-success btn-lg text-justify btn-block">
                        Print <span class="fas fa-print"></span>
                    </button>
                </div>
            </div>
        </div>
    </body>


    </html>

    <script>
        function printContent(el) {
            var restorepage = $('body').html();
            var printcontent = $('#' + el).clone();
            $('body').empty().html(printcontent);
            window.print();
            $('body').html(restorepage);
        }
    </script>

<?php } ?>