<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();

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
    ?>
    <!-- Header -->
    <div style="background-image: url(../admin/assets/img/theme/customer.png); background-size: cover;"
      class="header pb-8 pt-5 pt-md-8">
      <span class="mask bg-gradient-dark opacity-8"></span>
      <div class="container-fluid">
        <div class="header-body">
        </div>
      </div>
    </div>
    <!-- Page content -->
    <div class="container-fluid mt--8">
      <!-- Grid Layout -->
      <div class="row">
        <div class="col">
          <div class="card shadow">
            <div class="card-header border-0">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="text-dark font-weight-bold">Select a Product to Place an Order</h3>
                <div class="form-group mb-0">
                  <div class="input-group">
                    <input type="text" id="product-search" class="form-control form-control-lg"
                      placeholder="Search your product...">
                    <div class="input-group-append">
                      <span class="input-group-text bg-gradient-primary text-white">
                        <i class="fas fa-search"></i>
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="card-body">
              <div class="row">
                <?php
                $ret = "SELECT * FROM rpos_products ORDER BY `rpos_products`.`created_at` DESC";
                $stmt = $mysqli->prepare($ret);
                $stmt->execute();
                $res = $stmt->get_result();
                while ($prod = $res->fetch_object()) {
                  ?>
                  <div class="col-md-4 col-sm-6 mb-4 product-item"
                    data-product-name="<?php echo strtolower($prod->prod_name); ?>">
                    <div class="card h-100">
                      <img class="card-img-top img-thumbnail"
                        src="../admin/assets/img/products/<?php echo $prod->prod_img ?: 'default.jpg'; ?>"
                        alt="Product Image" style="height: 200px; object-fit: cover;"
                        onclick="openLightbox('<?php echo $prod->prod_img ?: 'default.jpg'; ?>')">
                      <div class="card-body text-center">
                        <h5 class="card-title"><?php echo $prod->prod_name; ?></h5>
                        <p class="card-text">Code: <?php echo $prod->prod_code; ?></p>
                        <p class="card-text">Price: ₱<?php echo $prod->prod_price; ?></p>
                        <p class="card-text">Quantity: <?php echo $prod->prod_stock; ?></p>
                        <p class="card-text"><?php echo $prod->prod_desc; ?></p>
                        <!-- Product Description Added -->
                        <a href="make_oder.php?prod_id=<?php echo $prod->prod_id; ?>&prod_name=<?php echo $prod->prod_name; ?>&prod_price=<?php echo $prod->prod_price; ?>"
                          class="btn btn-warning btn-sm">
                          <i class="fas fa-cart-plus"></i> Place Order
                        </a>
                      </div>
                    </div>
                  </div>
                <?php } ?>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- Footer -->
      <?php
      require_once('partials/_footer.php');
      ?>
    </div>
  </div>

  <!-- Lightbox Modal -->
  <div id="lightbox-modal" class="lightbox-modal" style="display: none;">
    <div id="lightbox-content" class="lightbox-content" onclick="event.stopPropagation();">
      <img id="lightbox-image" src="" alt="Product Image">
    </div>
  </div>

  <!-- Argon Scripts -->
  <?php
  require_once('partials/_scripts.php');
  ?>
</body>

<style>
  .card {
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
  }

  .card:hover {
    transform: translateY(-5px);
    box-shadow: 0px 15px 40px rgba(0, 0, 0, 0.2);
  }

  .card-title {
    font-weight: 600;
    color: #333;
    font-size: 1.1rem;
  }

  .card-body {
    padding: 20px;
    text-align: center;
  }

  .card-text {
    font-size: 0.9rem;
    color: #555;
    margin-bottom: 10px;
  }

  .card-img-top {
    border-bottom: 2px solid #eee;
    transition: transform 0.3s ease;
  }

  .card-img-top:hover {
    transform: scale(1.05);
  }

  .product-item {
    padding: 10px;
  }

  .btn-warning {
    background-color: #ff9900;
    color: #fff;
    border-radius: 30px;
    padding: 8px 20px;
    font-weight: 500;
    transition: background-color 0.3s ease, transform 0.3s ease;
  }

  .btn-warning:hover {
    background-color: #ff7700;
    transform: translateY(-2px);
  }

  .input-group {
    max-width: 350px;
    margin: 0 auto 20px;
  }

  .input-group-text {
    border-radius: 0 25px 25px 0;
    background-color: #f8f9fa;
    border: 1px solid #ddd;
  }

  .form-control {
    border-radius: 25px 0 0 25px;
    box-shadow: none;
  }

  .form-control:focus {
    box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
    border-color: #007bff;
  }

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
        const productName = item.getAttribute("data-product-name");
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