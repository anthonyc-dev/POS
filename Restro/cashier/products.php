<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $adn = "DELETE FROM rpos_products WHERE prod_id = ?";
    $stmt = $mysqli->prepare($adn);
    $stmt->bind_param('i', $id);  // Corrected to 'i' (integer)
    if ($stmt->execute()) {
        $success = "Deleted";
        header("refresh:1; url=products.php");
    } else {
        $err = "Try Again Later";
    }
    $stmt->close();
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
        <div style="background-image: url(../admin/assets/img/theme/21231.avif); background-size: cover;" class="header pb-8 pt-5 pt-md-8">
            <span class="mask bg-gradient-dark opacity-8"></span>
            <div class="container-fluid">
                <div class="header-body"></div>
            </div>
        </div>

        <!-- Page content -->
        <div class="container-fluid mt--8">
            <!-- Grid Layout -->
            <div class="row">
                <div class="col">
                    <div class="card shadow">
                        <div class="card-header border-0 d-flex justify-content-between align-items-center">
                            <h3 class="mb-0">Available Items</h3>
                            <div class="form-group mb-0">
                                <div class="input-group">
                                    <input type="text" id="product-search" class="form-control" placeholder="Search products...">
                                    <div class="input-group-append">
                                        <span class="input-group-text bg-primary text-white">
                                            <i class="fas fa-search"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="row" id="product-list">
                                <?php
                                $ret = "SELECT * FROM rpos_products ORDER BY `created_at` DESC";
                                $stmt = $mysqli->prepare($ret);
                                $stmt->execute();
                                $res = $stmt->get_result();
                                while ($prod = $res->fetch_object()) { ?>
                                    <div class="col-md-4 col-sm-6 mb-4 product-item" data-name="<?php echo strtolower($prod->prod_name); ?>">
                                        <div class="card h-100">
                                            <img class="card-img-top img-thumbnail" src="../admin/assets/img/products/<?php echo $prod->prod_img ?: 'default.jpg'; ?>" alt="Product Image" style="height: 200px; object-fit: cover;" onclick="openLightbox('<?php echo $prod->prod_img ?: 'default.jpg'; ?>')">
                                            <div class="card-body">
                                                <h5 class="card-title"><?php echo $prod->prod_name; ?></h5>
                                                <p class="card-text">Code: <?php echo $prod->prod_code; ?></p>
                                                <p class="card-text">Price: ₱<?php echo $prod->prod_price; ?></p>
                                                <p class="card-text">Quantity: <?php echo $prod->prod_stock; ?></p>
                                                <div class="d-flex">
                                                    <a href="products.php?delete=<?php echo $prod->prod_id; ?>" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </a>
                                                    <a href="update_product.php?update=<?php echo $prod->prod_id; ?>" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-edit"></i> Update
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <?php require_once('partials/_footer.php'); ?>
    </div>
    
    <!-- Lightbox Modal -->
    <div id="lightbox-modal" class="lightbox-modal" style="display: none;">
        <div id="lightbox-content" class="lightbox-content" onclick="event.stopPropagation();">
            <img id="lightbox-image" src="" alt="Product Image">
        </div>
    </div>

    <!-- Argon Scripts -->
    <?php require_once('partials/_scripts.php'); ?>
</body>

<style>
    .lightbox-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.8);
        justify-content: center;
        align-items: center;
        z-index: 1000;
    }

    .lightbox-content {
        position: relative;
        max-width: 100%;
        max-height: 100%;
        overflow: hidden;
    }

    .lightbox-content img {
        width: auto;
        height: auto;
        max-width: 90vw;
        max-height: 90vh;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4);
    }

    .card-img-top {
        transition: transform 0.3s ease;
    }

    .card-img-top:hover {
        transform: scale(1.1);
    }
</style>

<script>
    // Function to open lightbox
    function openLightbox(imageSrc) {
        var lightboxModal = document.getElementById('lightbox-modal');
        var lightboxImage = document.getElementById('lightbox-image');
        lightboxImage.src = "../admin/assets/img/products/" + imageSrc; // Load the clicked image into the modal
        lightboxModal.style.display = "flex"; // Show the modal
    }

    // Close lightbox when clicking outside the image
    document.getElementById('lightbox-modal').addEventListener('click', function () {
        this.style.display = 'none'; // Hide the modal when clicked outside the image
    });

    // Search functionality
    document.addEventListener("DOMContentLoaded", function () {
        const searchInput = document.getElementById("product-search");
        const productItems = document.querySelectorAll(".product-item");

        searchInput.addEventListener("input", function () {
            const filter = searchInput.value.toLowerCase();
            productItems.forEach(function (item) {
                const productName = item.getAttribute("data-name");
                if (productName.includes(filter)) {
                    item.style.display = "block"; // Show matching products
                } else {
                    item.style.display = "none"; // Hide non-matching products
                }
            });
        });
    });
</script>

</html>
