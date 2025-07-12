<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
include('config/code-generator.php');

check_login();

if (isset($_POST['addProduct'])) {
    // Prevent Posting Blank Values
    if (empty($_POST["prod_code"]) || empty($_POST["prod_name"]) || empty($_POST['prod_desc']) || empty($_POST['prod_price']) || empty($_POST['prod_stock'])) {
        $err = "Blank Values Not Accepted";
    } else {
        $prod_id = $_POST['prod_id'];
        $prod_code = $_POST['prod_code'];
        $prod_name = $_POST['prod_name'];
        
        // Handle Product Image Upload
        $prod_img = $_FILES['prod_img']['name'];
        $upload_dir = "assets/img/products/";
        $upload_file = $upload_dir . basename($prod_img);
        
        if (move_uploaded_file($_FILES["prod_img"]["tmp_name"], $upload_file)) {
            $prod_desc = $_POST['prod_desc'];
            $prod_price = $_POST['prod_price'];
            $prod_stocks = $_POST['prod_stock']; // Capture product stock

            // Insert Captured Information into the Database Table
            $postQuery = "INSERT INTO rpos_products (prod_id, prod_code, prod_name, prod_img, prod_desc, prod_price, prod_stock) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $postStmt = $mysqli->prepare($postQuery);
            // Bind Parameters
            $rc = $postStmt->bind_param('sssssss', $prod_id, $prod_code, $prod_name, $prod_img, $prod_desc, $prod_price, $prod_stocks);
            $postStmt->execute();
            
            if ($postStmt) {
                $success = "Product Added";
                header("refresh:2; url=add_product.php"); // Redirect after success
            } else {
                $err = "Database Error. Please Try Again Later.";
            }
        } else {
            $err = "Failed to Upload Image. Please Try Again.";
        }
    }
}
require_once('partials/_head.php');
?>

<body>
    <!-- Sidenav -->
    <?php require_once('partials/_sidebar.php'); ?>
    <!-- Main content -->
    <div class="main-content">
        <!-- Top navbar -->
        <?php require_once('partials/_topnav.php'); ?>
        <!-- Header -->
        <div style="background-image: url(../admin/assets/img/theme/adminimg.avif); background-size: cover;" class="header pb-8 pt-5 pt-md-8">
            <span class="mask bg-gradient-dark opacity-8"></span>
            <div class="container-fluid">
                <div class="header-body"></div>
            </div>
        </div>
        <!-- Page content -->
        <div class="container-fluid mt--8">
            <div class="row">
                <div class="col">
                    <div class="card shadow">
                        <div class="card-header border-0">
                            <h3>Please Fill All Fields</h3>
                        </div>
                        <div class="card-body">
                            <!-- Form -->
                            <form method="POST" enctype="multipart/form-data">
                                <div class="form-row">
                                    <div class="col-md-6">
                                        <label>Product Name</label>
                                        <input type="text" name="prod_name" class="form-control" required>
                                        <input type="hidden" name="prod_id" value="<?php echo $prod_id; ?>" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label>Product Code</label>
                                        <input type="text" name="prod_code" value="<?php echo $alpha; ?>-<?php echo $beta; ?>" class="form-control" required>
                                    </div>
                                </div>
                                <hr>
                                <div class="form-row">
                                    <div class="col-md-6">
                                        <label>Product Image</label>
                                        <input type="file" name="prod_img" class="btn btn-outline-success form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Product Price</label>
                                        <input type="text" name="prod_price" class="form-control" required>
                                    </div>
                                </div>
                                <hr>
                                <div class="form-row">
                                    <div class="col-md-6">
                                        <label>Product Stock Quantity</label>
                                        <div class="input-group">
                                            <button type="button" class="btn btn-outline-danger" id="decreaseQty">-</button>
                                            <input type="number" name="prod_stock" id="prod_stock" value="0" class="form-control text-center" required readonly>
                                            <button type="button" class="btn btn-outline-success" id="increaseQty">+</button>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Product Description</label>
                                        <textarea rows="5" name="prod_desc" class="form-control" required></textarea>
                                    </div>
                                </div>
                                <br>
                                <div class="form-row">
                                    <div class="col-md-6">
                                        <button type="submit" name="addProduct" class="btn btn-success">Add Product</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Footer -->
            <?php require_once('partials/_footer.php'); ?>
        </div>
    </div>
    <!-- Argon Scripts -->
    <?php require_once('partials/_scripts.php'); ?>

    <script>
        // Script for increasing and decreasing product stock quantity
        document.getElementById('increaseQty').addEventListener('click', function() {
            let qtyInput = document.getElementById('prod_stock');
            let currentQty = parseInt(qtyInput.value);
            qtyInput.value = currentQty + 1;
        });

        document.getElementById('decreaseQty').addEventListener('click', function() {
            let qtyInput = document.getElementById('prod_stock');
            let currentQty = parseInt(qtyInput.value);
            if (currentQty > 0) {
                qtyInput.value = currentQty - 1;
            }
        });
    </script>
</body>
</html>
