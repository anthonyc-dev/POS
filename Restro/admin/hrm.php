<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();

// Search logic
$searchQuery = "";
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $searchQuery = " WHERE staff_name LIKE ? OR staff_email LIKE ?";
}

// Delete Staff
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $adn = "DELETE FROM rpos_staff WHERE staff_id = ?";
    $stmt = $mysqli->prepare($adn);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
    if ($stmt) {
        $success = "Deleted" && header("refresh:1; url=hrm.php");
    } else {
        $err = "Try Again Later";
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
      <!-- Table -->
      <div class="row">
        <div class="col">
          <div class="card shadow">
            <div class="card-header border-0">
              <a href="add_staff.php" class="btn btn-outline-success">
                <i class="fas fa-user-plus"></i> Add New Staff
              </a>
              
              <!-- Search Form -->
              <form action="hrm.php" method="GET" class="d-inline-block float-right">
                <input type="text" name="search" class="form-control" placeholder="Search by Name or Email" value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>" style="width: 300px; display: inline-block;">
                <button type="submit" class="btn btn-outline-primary">Search</button>
              </form>
            </div>

            <div class="table-responsive">
              <table class="table align-items-center table-flush">
                <thead class="thead-light">
                  <tr>
                    <th scope="col">Staff Number</th>
                    <th scope="col">Name</th>
                    <th scope="col">Email</th>
                    <th scope="col">Actions</th>
                  </tr>
                </thead>
                <tbody id="staff-table">
                  <?php
                  // Prepare the SQL query with search functionality
                  $ret = "SELECT * FROM rpos_staff" . $searchQuery . " ORDER BY created_at DESC";
                  $stmt = $mysqli->prepare($ret);

                  if ($searchQuery) {
                      $searchTerm = "%$search%";
                      $stmt->bind_param('ss', $searchTerm, $searchTerm);
                  }

                  $stmt->execute();
                  $res = $stmt->get_result();

                  while ($staff = $res->fetch_object()) {
                  ?>
                  <tr class="staff-row" data-name="<?php echo strtolower($staff->staff_name); ?>" data-email="<?php echo strtolower($staff->staff_email); ?>">
                    <td><?php echo $staff->staff_number; ?></td>
                    <td><?php echo $staff->staff_name; ?></td>
                    <td><?php echo $staff->staff_email; ?></td>
                    <td>
                      <a href="hrm.php?delete=<?php echo $staff->staff_id; ?>">
                        <button class="btn btn-sm btn-danger">
                          <i class="fas fa-trash"></i> Delete
                        </button>
                      </a>
                      <a href="update_staff.php?update=<?php echo $staff->staff_id; ?>">
                        <button class="btn btn-sm btn-primary">
                          <i class="fas fa-user-edit"></i> Update
                        </button>
                      </a>
                    </td>
                  </tr>
                  <?php } ?>
                </tbody>
              </table>
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

  <!-- JavaScript for Live Search -->
  <script>
    document.addEventListener("DOMContentLoaded", function () {
        const searchInput = document.querySelector('input[name="search"]');
        const staffRows = document.querySelectorAll(".staff-row");

        searchInput.addEventListener("input", function () {
            const filter = searchInput.value.toLowerCase();
            staffRows.forEach(function (row) {
                const staffName = row.getAttribute("data-name");
                const staffEmail = row.getAttribute("data-email");
                if (staffName.includes(filter) || staffEmail.includes(filter)) {
                    row.style.display = ""; // Show matching rows
                } else {
                    row.style.display = "none"; // Hide non-matching rows
                }
            });
        });
    });
  </script>
</body>
</html>
