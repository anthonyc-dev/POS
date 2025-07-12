<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();

// Search logic
$searchQuery = "";
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    // Sanitize the input and prepare for the SQL query
    $searchQuery = " WHERE customer_name LIKE ? OR customer_email LIKE ?";
}

// Delete customer logic
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $adn = "DELETE FROM  rpos_customers  WHERE  customer_id = ?";
    $stmt = $mysqli->prepare($adn);
    $stmt->bind_param('s', $id);
    $stmt->execute();
    $stmt->close();
    if ($stmt) {
        $success = "Deleted" && header("refresh:1; url=customes.php");
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
        <div style="background-image: url(../admin/assets/img/theme/21231.avif); background-size: cover;" class="header pb-8 pt-5 pt-md-8">
            <span class="mask bg-gradient-dark opacity-8"></span>
            <div class="container-fluid">
                <div class="header-body">
                </div>
            </div>
        </div>

        <!-- Page content -->
        <div class="container-fluid mt--8">
            <!-- Table -->
            <div class="row">
                <div class="col">
                    <div class="card shadow">
                        <div class="card-header border-0">
                            <!-- Add Customer Button -->
                            <a href="add_customer.php" class="btn btn-outline-success">
                                <i class="fas fa-user-plus"></i>
                                Add New Customer
                            </a>
                            
                            <!-- Search Form -->
                            <form action="customes.php" method="GET" class="d-inline-block float-right">
                                <input type="text" name="search" class="form-control" placeholder="Search by Name or Email" value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>" style="width: 300px; display: inline-block;">
                                <button type="submit" class="btn btn-outline-primary">Search</button>
                            </form>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col">Full Name</th>
                                        <th scope="col">Contact Number</th>
                                        <th scope="col">Email</th>
                                        <th scope="col">Address</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="customer-table">
                                    <?php
                                    // Modify the SQL query to include search functionality
                                    $ret = "SELECT * FROM rpos_customers" . $searchQuery . " ORDER BY `rpos_customers`.`created_at` DESC";
                                    $stmt = $mysqli->prepare($ret);

                                    if ($searchQuery) {
                                        // Bind parameters for both name and email search
                                        $searchTerm = "%$search%";
                                        $stmt->bind_param('ss', $searchTerm, $searchTerm);
                                    }

                                    $stmt->execute();
                                    $res = $stmt->get_result();

                                    // Loop through the results and display the customers
                                    while ($cust = $res->fetch_object()) {
                                    ?>
                                    <tr class="customer-row" data-name="<?php echo strtolower($cust->customer_name); ?>">
                                        <td><?php echo $cust->customer_name; ?></td>
                                        <td><?php echo $cust->customer_phoneno; ?></td>
                                        <td><?php echo $cust->customer_email; ?></td>
                                        <td><?php echo $cust->customer_address; ?></td>
                                        <td>
                                            <a href="update_customer.php?update=<?php echo $cust->customer_id; ?>">
                                                <button class="btn btn-sm btn-primary">
                                                    <i class="fas fa-user-edit"></i>
                                                    Update
                                                </button>
                                            </a>
                                            <a href="customes.php?delete=<?php echo $cust->customer_id; ?>">
                                                <button class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                    Delete
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
            const customerRows = document.querySelectorAll(".customer-row");

            searchInput.addEventListener("input", function () {
                const filter = searchInput.value.toLowerCase();
                customerRows.forEach(function (row) {
                    const customerName = row.getAttribute("data-name");
                    if (customerName.includes(filter)) {
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
