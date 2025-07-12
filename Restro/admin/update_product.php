<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
include('config/code-generator.php');

check_login();
if (isset($_POST['UpdateProduct'])) {
    // Prevent Posting Blank Values
    if (empty($_POST["prod_code"]) || empty($_POST["prod_name"]) || empty($_POST['prod_desc']) || empty($_POST['prod_price']) || empty($_POST['prod_stock'])) {
        $err = "Blank Values Not Accepted";
    } else {
        $update = $_GET['update'];
        $prod_code = $_POST['prod_code'];
        $prod_name = $_POST['prod_name'];
        $prod_img = $_FILES['prod_img']['name'];

        // Handle Image Upload
        if (!empty($prod_img)) {
            move_uploaded_file($_FILES["prod_img"]["tmp_name"], "assets/img/products/" . $_FILES["prod_img"]["name"]);
        } else {
            $prod_img = $_POST['existing_prod_img']; // Retain the existing image if no new image is uploaded
        }

        $prod_desc = $_POST['prod_desc'];
        $prod_price = $_POST['prod_price'];
        $prod_stock = $_POST['prod_stock']; // Get product stock value (corrected from prod_stocks to prod_stock)

        // Update Captured Information in Database Table
        $postQuery = "UPDATE rpos_products SET prod_code =?, prod_name =?, prod_img =?, prod_desc =?, prod_price =?, prod_stock =? WHERE prod_id = ?";
        $postStmt = $mysqli->prepare($postQuery);
        // Bind Parameters
        $rc = $postStmt->bind_param('sssssss', $prod_code, $prod_name, $prod_img, $prod_desc, $prod_price, $prod_stock, $update);
        $postStmt->execute();

        // Check if update is successful
        if ($postStmt) {
            $success = "Product Updated" && header("refresh:1; url=products.php");
        } else {
            $err = "Please Try Again Or Try Later";
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
        $update = $_GET['update'];
        $ret = "SELECT * FROM rpos_products WHERE prod_id = '$update'";
        $stmt = $mysqli->prepare($ret);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($prod = $res->fetch_object()) {
            ?>
            <!-- Header -->
            <div style="background-image: url(../admin/assets/img/theme/adminimg.avif); background-size: cover;" class="header pb-8 pt-5 pt-md-8">
                <span class="mask bg-gradient-dark opacity-8"></span>
                <div class="container-fluid">
                    <div class="header-body">
                    </div>
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
                                <form method="POST" enctype="multipart/form-data">
                                    <div class="form-row">
                                        <div class="col-md-6">
                                            <label>Product Name</label>
                                            <input type="text" value="<?php echo $prod->prod_name; ?>" name="prod_name" class="form-control">
                                        </div>
                                        <div class="col-md-6">
                                            <label>Product Code</label>
                                            <input type="text" name="prod_code" value="<?php echo $prod->prod_code; ?>" class="form-control">
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="form-row">
                                        <div class="col-md-6">
                                            <label>Product Image</label>
                                            <input type="file" name="prod_img" class="btn btn-outline-success form-control">
                                            <input type="hidden" name="existing_prod_img" value="<?php echo $prod->prod_img; ?>"> <!-- Keep existing image -->
                                        </div>
                                        <div class="col-md-6">
                                            <label>Product Price</label>
                                            <input type="text" name="prod_price" class="form-control" value="<?php echo $prod->prod_price; ?>">
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="form-row">
                                        <div class="col-md-6">
                                            <label>Product Stock Quantity</label>
                                            <div class="input-group">
                                                <button type="button" class="btn btn-outline-danger" id="decreaseQty">-</button>
                                                <input type="number" name="prod_stock" id="prod_stock" value="<?php echo $prod->prod_stock; ?>" class="form-control text-center" required readonly>
                                                <button type="button" class="btn btn-outline-success" id="increaseQty">+</button>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label>Product Description</label>
                                            <textarea rows="5" name="prod_desc" class="form-control"><?php echo $prod->prod_desc; ?></textarea>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="form-row">
                                        <div class="col-md-6">
                                            <input type="submit" name="UpdateProduct" value="Update Product" class="btn btn-success">
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Footer -->
                <?php
                require_once('partials/_footer.php');
            }
            ?>
        </div>
    </div>
    <!-- Argon Scripts -->
    <?php
    require_once('partials/_scripts.php');
    ?>
</body>

<script>
    // Script for increasing and decreasing product stock quantity
    document.getElementById('increaseQty').addEventListener('click', function() {
        let qtyInput = document.getElementById('prod_stock'); // Changed to prod_stock to match the correct input field
        let currentQty = parseInt(qtyInput.value);
        qtyInput.value = currentQty + 1;
    });

    document.getElementById('decreaseQty').addEventListener('click', function() {
        let qtyInput = document.getElementById('prod_stock'); // Changed to prod_stock to match the correct input field
        let currentQty = parseInt(qtyInput.value);
        if (currentQty > 0) {
            qtyInput.value = currentQty - 1;
        }
    });
</script>
</html>
