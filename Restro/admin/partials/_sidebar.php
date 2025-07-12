<?php
$admin_id = $_SESSION['admin_id'];
$ret = "SELECT * FROM rpos_admin WHERE admin_id = '$admin_id'";
$stmt = $mysqli->prepare($ret);
$stmt->execute();
$res = $stmt->get_result();
while ($admin = $res->fetch_object()) {
  ?>
  <style>
    #sidenav-main {
      background: linear-gradient(to bottom, #D32F2F, #333333);
      transition: transform 0.3s ease;
      transform: translateX(0);
      /* Sidebar is visible by default */
      z-index: 1050;
      /* Ensure sidebar stays on top */
      position: fixed;
      /* Keep sidebar fixed on the screen */
      height: 100vh;
      /* Ensure sidebar takes full height */
      top: 0;
      /* Keep sidebar at the top of the screen */
      left: 0;
      /* Align sidebar to the left side of the screen */
    }

    #sidenav-main.collapsed {
      transform: translateX(-250px);
      /* Sidebar will slide out */
    }

    #sidenav-main .nav-link,
    #sidenav-main .navbar-brand,
    #sidenav-main h6,
    #sidenav-main .dropdown-item {
      color: #ffffff;
    }

    #sidenav-main .nav-link:hover,
    #sidenav-main .dropdown-item:hover {
      color: #d1d1d1;
    }

    /* Optional: make icons white as well */
    #sidenav-main .nav-link i,
    #sidenav-main .dropdown-item i {
      color: #ffffff;
    }

    /* Toggle button styling */
    /* Compact Toggle Button Styling */
    /* Compact Toggle Button Styling (Borderless version) */
    /* Compact Toggle Button Styling (Borderless version, on right side) */
    .toggle-btn {
      position: fixed;
      top: 15px;
      right: 15px;
      /* Positioned on the right side of the screen */
      background-color: transparent;
      color: white;
      border: none;
      /* Removed the border */
      padding: 8px 12px;
      border-radius: 50%;
      /* Rounded button for aesthetic look */
      cursor: pointer;
      font-size: 14px;
      z-index: 1100;
      box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
      transition: background-color 0.3s ease, box-shadow 0.3s ease, transform 0.2s ease;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .toggle-btn:hover {
      background-color: rgba(255, 255, 255, 0.1);
      /* Subtle hover effect */
      box-shadow: 0px 6px 12px rgba(0, 0, 0, 0.15);
      /* Add more shadow on hover */
      transform: scale(1.05);
      /* Slight scaling effect on hover */
    }

    .toggle-btn i {
      font-size: 20px;
      /* Adjust icon size */
      transition: transform 0.4s ease, color 0.4s ease;
    }

    /* Rotate icon when sidebar is open */
    .rotate-icon {
      transform: rotate(180deg);
      color: #00796b;
      /* Change color when rotated */
    }
  </style>

  <nav class="navbar navbar-vertical fixed-left navbar-expand-md navbar-light bg-white" id="sidenav-main"
    style="width: 220px;">
    <div class="container-fluid">
      <!-- Toggler Button for the Sidebar -->
      <button class="toggle-btn" id="sidebarToggle">
        <i class="fas fa-bars"></i>
        <span></span>
      </button>
      <br>

      <!-- User -->
      <ul class="nav align-items-center d-md-none">
        <li class="nav-item dropdown">
          <a class="nav-link nav-link-icon" href="#" role="button" data-toggle="dropdown" aria-haspopup="true"
            aria-expanded="false">
            <i class="ni ni-bell-55"></i>
          </a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <div class="media align-items-center">
              <span class="avatar avatar-sm rounded-circle">
                <img alt="Image placeholder"
                  src="<?php echo isset($admin->admin_image) ? $admin->admin_image : '../admin/assets/img/theme/default-avatar.png'; ?>"
                  class="rounded-circle" style="border-radius: 50%; width: 150px; height: 150px; object-fit: cover;">
              </span>
            </div>
          </a>
          <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-right">
            <div class="dropdown-header noti-title">
              <h6 class="text-overflow m-0">Welcome!</h6>
            </div>
            <a href="change_profile.php" class="dropdown-item">
              <i class="ni ni-single-02"></i>
              <span>My profile</span>
            </a>
            <div class="dropdown-divider"></div>
            <a href="logout.php" class="dropdown-item">
              <i class="ni ni-user-run"></i>
              <span>Logout</span>
            </a>
          </div>
        </li>
      </ul>

      <!-- Collapse -->
      <div class="collapse navbar-collapse" id="sidenav-collapse-main">
        <!-- Navigation -->
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link" href="dashboard.php">
              <i class="ni ni-tv-2 text-primary"></i> Dashboard
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="hrm.php">
              <i class="fas fa-user-tie text-primary"></i> Staffs
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="customes.php">
              <i class="fas fa-users text-primary"></i> Customers
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="products.php">
              <i class="ni ni-bullet-list-67 text-primary"></i> Products
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="receipts.php">
              <i class="fas fa-file-invoice-dollar text-primary"></i> Receipts
            </a>
          </li>
        </ul>
        <hr class="my-3">
        <h6 class="navbar-heading text-muted">Reporting</h6>
        <ul class="navbar-nav mb-md-3">
          <li class="nav-item">
            <a class="nav-link" href="orders_reports.php">
              <i class="fas fa-shopping-basket"></i> Orders
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="payments_reports.php">
              <i class="fas fa-funnel-dollar"></i> Payments
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="total_sales.php">
              <i class="fas fa-funnel-dollar"></i> Sales
            </a>
          </li>
        </ul>
        <hr class="my-3">
        <ul class="navbar-nav mb-md-3">
          <li class="nav-item">
            <a class="nav-link" href="logout.php">
              <i class="fas fa-sign-out-alt text-danger"></i> Log Out
            </a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <script>
    // Get the sidebar and the toggle button
    const sidebar = document.getElementById('sidenav-main');
    const toggleButton = document.getElementById('sidebarToggle');

    // Add click event to the toggle button
    toggleButton.addEventListener('click', () => {
      sidebar.classList.toggle('collapsed'); // Toggle the 'collapsed' class on sidebar
    });

    // Variable to keep track if the sidebar is opened or collapsed
    let sidebarCollapsed = sidebar.classList.contains('collapsed');

    // Detect mouse movement to the left side of the screen
    let mouseX = 0;
    document.addEventListener('mousemove', (e) => {
      mouseX = e.clientX; // Get mouse position

      // If mouse is on the left and sidebar is collapsed, open the sidebar
      if (mouseX < 50 && sidebarCollapsed) {
        sidebar.classList.remove('collapsed'); // Automatically open the sidebar if mouse is on the left
        sidebarCollapsed = false; // Set flag to indicate sidebar is now open
      }
    });

    // Detect mouse leaving the sidebar area
    sidebar.addEventListener('mouseleave', () => {
      // If the sidebar is open, collapse it when mouse leaves
      if (!sidebarCollapsed) {
        sidebar.classList.add('collapsed'); // Collapse the sidebar when mouse leaves
        sidebarCollapsed = true; // Update the flag to indicate sidebar is collapsed
      }
    });

    // Reset sidebar state on click toggle
    sidebar.addEventListener('transitionend', () => {
      // Update the collapsed flag after sidebar animation ends
      if (sidebar.classList.contains('collapsed')) {
        sidebarCollapsed = true;
      } else {
        sidebarCollapsed = false;
      }
    });
  </script>
<?php } ?>