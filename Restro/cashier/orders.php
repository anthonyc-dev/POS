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
    <div style="background-image: url(../admin/assets/img/theme/21231.avif); background-size: cover;"
      class="header  pb-8 pt-5 pt-md-8">
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
              Select Any Product To Make An Order
            </div>
            <div class="card-body">
              <div class="row">
                <?php
                $ret = "SELECT * FROM  rpos_products  ORDER BY `rpos_products`.`created_at` DESC ";
                $stmt = $mysqli->prepare($ret);
                $stmt->execute();
                $res = $stmt->get_result();
                while ($prod = $res->fetch_object()) {
                  ?>
                  <div class="col-md-4 col-sm-6 mb-4">
                    <div class="card h-100">
                      <img class="card-img-top img-thumbnail"
                        src="../admin/assets/img/products/<?php echo $prod->prod_img ?: 'default.jpg'; ?>"
                        alt="Product Image" style="height: 200px; object-fit: cover;">
                      <div class="card-body text-center">
                        <h5 class="card-title"><?php echo $prod->prod_name; ?></h5>
                        <p class="card-text">Code: <?php echo $prod->prod_code; ?></p>
                        <p class="card-text">Price: ₱<?php echo $prod->prod_price; ?></p>
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
  <!-- Argon Scripts -->
  <?php
  require_once('partials/_scripts.php');
  ?>
</body>
<style>
  .card {
    border-radius: 10px;
    overflow: hidden;
  }

  .card-title {
    font-weight: bold;
    color: #333;
  }

  .card-body {
    padding: 15px;
  }

  .card-img-top {
    border-bottom: 1px solid #ddd;
  }

  .card-img-top {
    transition: transform 0.3s ease;
    /* Smooth zoom transition */
  }

  .card-img-top:hover {
    transform: scale(1.1);
    /* Slightly zoom in the image */
  }
</style>

</html>