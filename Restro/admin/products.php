<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();

// Check if the delete action is triggered
if (isset($_GET['delete'])) {
  $id = intval($_GET['delete']);
  $adn = "DELETE FROM rpos_products WHERE prod_id = ?";
  $stmt = $mysqli->prepare($adn);
  $stmt->bind_param('i', $id);

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
    <div style="background-image: url(../admin/assets/img/theme/adminimg.avif); background-size: cover;"
      class="header pb-8 pt-5 pt-md-8">
      <span class="mask bg-gradient-dark opacity-8"></span>
      <div class="container-fluid">
        <div class="header-body"></div>
      </div>
    </div>

    <!-- Page content -->
    <div class="container-fluid mt--8">
      <!-- Add Product and Search -->
      <div class="row mb-4">
        <div class="col-md-12 d-flex justify-content-end align-items-center">
          <!-- Add Product Button -->
          <a href="add_product.php" class="btn btn-primary btn-lg mr-3" id="add-product-btn">
            <i class="fas fa-plus-circle"></i> Add New Product
          </a>

          <!-- Search Form -->
          <div class="form-group mb-0" style="width: 300px;">
            <input type="text" id="product-search" class="form-control form-control-lg"
              placeholder="Search products by name...">
          </div>
        </div>
      </div>

      <!-- Product Grid -->
      <div class="row" id="product-list">
        <?php
        $ret = "SELECT * FROM rpos_products"; // Query to fetch products
        $stmt = $mysqli->prepare($ret);
        $stmt->execute();
        $res = $stmt->get_result();

        while ($prod = $res->fetch_object()) { ?>
          <div class="col-md-4 product-item" data-name="<?php echo strtolower($prod->prod_name); ?>">
            <div class="card mb-4">
              <!-- Image Link to Lightbox -->
              <a href="#lightbox-<?php echo $prod->prod_id; ?>">
                <img class="card-img-top img-fluid"
                  src="../admin/assets/img/products/<?php echo $prod->prod_img ?: 'default.jpg'; ?>" alt="Product Image"
                  style="height: 250px; object-fit: cover;">
              </a>
              <div class="card-body">
                <h5 class="card-title"><?php echo $prod->prod_name; ?></h5>
                <p class="card-text"><strong>Code:</strong> <?php echo $prod->prod_code; ?></p>
                <p class="card-text"><strong>Price:</strong>
                  ₱<?php echo number_format((float) str_replace(',', '', $prod->prod_price), 2); ?></p>
                <p class="card-text"><strong>Quantity:</strong> <?php echo $prod->prod_stock; ?></p>
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

            <div id="lightbox-<?php echo $prod->prod_id; ?>" class="lightbox">
              <a href="#"
                style="position: absolute; top: 20px; right: 20px; font-size: 30px; color: white; text-decoration: none;">&times;</a>
              <img src="../admin/assets/img/products/<?php echo $prod->prod_img ?: 'default.jpg'; ?>"
                alt="Product Full Image">
            </div>
          </div>
        <?php } ?>
      </div>
    </div>

    <!-- Footer -->
    <?php require_once('partials/_footer.php'); ?>
  </div>
  </div>

  <!-- Argon Scripts -->
  <?php require_once('partials/_scripts.php'); ?>

  <script>
    document.addEventListener("DOMContentLoaded", function () {
      const lightboxes = document.querySelectorAll(".lightbox");

      lightboxes.forEach((lightbox) => {
        lightbox.addEventListener("click", function (e) {
          // Prevent hash change and closing behavior when clicking inside the lightbox
          if (e.target === lightbox) {
            lightbox.style.display = "none";  // Close the lightbox without changing URL
          }
        });
      });
    });

  </script>
</body>

<!-- Styles and Script -->
<style>
  /* Button Styling */
  #add-product-btn {
    font-size: 16px;
    padding: 12px 20px;
    border-radius: 30px;
    background-color: #007bff;
    color: white;
    text-align: center;
    transition: background-color 0.3s ease, transform 0.2s ease;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  }

  #add-product-btn:hover {
    background-color: #0056b3;
    transform: translateY(-3px);
  }

  /* Search Input Styling */
  #product-search {
    font-size: 16px;
    padding: 12px;
    border-radius: 30px;
    border: 1px solid #ddd;
    box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
  }

  #product-search:focus {
    border-color: #007bff;
    box-shadow: inset 0 4px 8px rgba(0, 0, 0, 0.1), 0 0 6px rgba(0, 123, 255, 0.3);
    outline: none;
  }

  /* Responsive Styling */
  .d-flex {
    align-items: center;
    justify-content: flex-end;
  }

  @media (max-width: 768px) {
    .d-flex {
      flex-direction: column;
      align-items: flex-end;
    }

    #add-product-btn {
      width: 100%;
      margin-bottom: 10px;
    }

    #product-search {
      width: 100%;
    }
  }

  /* Card Styling */
  .card {
    border-radius: 15px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }

  .card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
  }

  /* Lightbox Styling */
  /* Lightbox Styling */
  .lightbox {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.8);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 1000;
  }

  .lightbox img {
    max-width: 90%;
    max-height: 90%;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4);
  }

  .lightbox:target {
    display: flex;
  }
</style>

<script>
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