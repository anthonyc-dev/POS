<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();

// Update Profile
if (isset($_POST['ChangeProfile'])) {
    $admin_id = $_SESSION['admin_id'];
    $admin_name = $_POST['admin_name'];
    $admin_email = $_POST['admin_email'];

    // Handle Profile Image Upload
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $image = $_FILES['profile_image'];
        $target_dir = "../admin/assets/img/";  // Change the path if needed
        $target_file = $target_dir . basename($image["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Validate file
        if (getimagesize($image["tmp_name"]) !== false) { // Check if the file is an image
            if ($image["size"] < 500000) { // Check file size
                if (in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) { // Allow specific file formats
                    if (move_uploaded_file($image["tmp_name"], $target_file)) {
                        $profile_image = $target_file;
                    } else {
                        $err = "Sorry, there was an error uploading your file.";
                    }
                } else {
                    $err = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                }
            } else {
                $err = "Sorry, your file is too large.";
            }
        } else {
            $err = "File is not an image.";
        }
    } else {
        // Use the existing image path if no new image is uploaded
        $profile_image = $_POST['existing_image'] ?? '';
    }

    // Update admin information
    $Qry = "UPDATE rpos_admin SET admin_name = ?, admin_email = ?, admin_image = ? WHERE admin_id = ?";
    $postStmt = $mysqli->prepare($Qry);
    $postStmt->bind_param('ssss', $admin_name, $admin_email, $profile_image, $admin_id);
    $postStmt->execute();

    if ($postStmt->affected_rows > 0) {
        $success = "Account Updated" && header("refresh:1; url=dashboard.php");
    } else {
        $err = "Please Try Again Or Try Later";
    }
}

// Change Password
if (isset($_POST['changePassword'])) {
  $old_password = $_POST['old_password'];
  $new_password = $_POST['new_password'];
  $confirm_password = $_POST['confirm_password'];
  $customer_id = $_SESSION['admin_id'];

  if (empty($old_password) || empty($new_password) || empty($confirm_password)) {
      $err = "All fields are required.";
  } elseif ($new_password !== $confirm_password) {
      $err = "New password and confirm password do not match.";
  } else {
      // Remove password hashing and store the new password as plain text
      $updateQuery = "UPDATE rpos_admin SET admin_password = ? WHERE admin_id = ?";
      $updateStmt = $mysqli->prepare($updateQuery);
      $updateStmt->bind_param('si', $new_password, $customer_id);
      $updateStmt->execute();

      if ($updateStmt->affected_rows > 0) {
          $success = "Password changed successfully.";
          header("refresh:2; url=change_profile.php");
      } else {
          $err = "Failed to change the password.";
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
    $admin_id = $_SESSION['admin_id'];
    //$login_id = $_SESSION['login_id'];
    $ret = "SELECT * FROM  rpos_admin  WHERE admin_id = '$admin_id'";
    $stmt = $mysqli->prepare($ret);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($admin = $res->fetch_object()) {
      ?>
      <!-- Header -->
      <div class="header pb-8 pt-5 pt-lg-8 d-flex align-items-center"
        style="min-height: 600px; background-image: url(../admin/assets/img/theme/adminimg.avif); background-size: cover; background-position: center top;">
        <!-- Mask -->
        <span class="mask bg-gradient-default opacity-8"></span>
        <!-- Header container -->
        <div class="container-fluid d-flex align-items-center">
          <div class="row">
            <div class="col-lg-7 col-md-10">
              <h1 class="display-2 text-white">Hello <?php echo $admin->admin_name; ?></h1>
              <p class="text-white mt-0 mb-5">This is your profile page. You can customize your profile as you want And
                also change password too</p>
            </div>
          </div>
        </div>
      </div>
      <!-- Page content -->
      <div class="container-fluid mt--8">
        <div class="row">
          <div class="col-xl-4 order-xl-2 mb-5 mb-xl-0">
            <div class="card card-profile shadow">
              <div class="row justify-content-center">
                <div class="col-lg-3 order-lg-2">
                  <div class="card-profile-image">
                    <a href="#">
                    <img src="<?php echo isset($admin->admin_image) ? $admin->admin_image: '../admin/assets/img/theme/man.png'; ?>"
                                                    class="rounded-circle"
                                                    style="border-radius: 50%; width: 200px; height: 200px; object-fit: cover;">
                    </a>
                  </div>
                </div>
              </div>
              <div class="card-header text-center border-0 pt-8 pt-md-4 pb-0 pb-md-4">
                <div class="d-flex justify-content-between">
                </div>
              </div>
              <div class="card-body pt-0 pt-md-4">
                <div class="row">
                  <div class="col">
                    <div class="card-profile-stats d-flex justify-content-center mt-md-5">
                      <div>
                      </div>
                      <div>
                      </div>
                      <div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="text-center">
                  <h3>
                    <?php echo $admin->admin_name; ?></span>
                  </h3>
                  <div class="h5 font-weight-300">
                    <i class="ni location_pin mr-2"></i><?php echo $admin->admin_email; ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-xl-8 order-xl-1">
            <div class="card bg-secondary shadow">
              <div class="card-header bg-white border-0">
                <div class="row align-items-center">
                  <div class="col-8">
                    <h3 class="mb-0">My account</h3>
                  </div>
                  <div class="col-4 text-right">
                  </div>
                </div>
              </div>
              <div class="card-body">
                <form method="post"  enctype="multipart/form-data">
                  <h6 class="heading-small text-muted mb-4">User information</h6>
                  <div class="pl-lg-4">
                    <div class="row">
                      <div class="col-lg-6">
                        <div class="form-group">
                          <label class="form-control-label" for="input-username">User Name</label>
                          <input type="text" name="admin_name" value="<?php echo $admin->admin_name; ?>"
                            id="input-username" class="form-control form-control-alternative">
                              </div>
                            </div>
                            <div class=" col-lg-6">
                          <div class="form-group">
                            <label class="form-control-label" for="input-email">Email address</label>
                            <input type="email" id="input-email" value="<?php echo $admin->admin_email; ?>"
                              name="admin_email" class="form-control form-control-alternative">
                          </div>
                        </div>

                        <div class="col-lg-12">
                                                <div class="form-group">
                                                    <label for="profile_image">Upload Profile Image</label>
                                                    <input type="file" name="profile_image" id="profile_image"
                                                        class="form-control form-control-alternative">
                                                </div>
                                            </div>
                        <div class="col-lg-12">
                          <div class="form-group">
                            <input type="submit" id="input-email" name="ChangeProfile"
                              class="btn btn-success form-control-alternative" value="Submit">
                      </div>
                    </div>
                  </div>
                </div>
              </form>
              <hr>
              <form method="post">
                            <h6 class="heading-small text-muted mb-4">Change Password</h6>
                            <div class="pl-lg-4">
                              <div class="row">
                                <div class="col-lg-12">
                                  <div class="form-group">
                                    <label class="form-control-label" for="input-username">Old Password</label>
                                    <input type="password" name="old_password" id="input-username"
                                      class="form-control form-control-alternative">
                                  </div>
                                </div>

                                <div class="col-lg-12">
                                  <div class="form-group">
                                    <label class="form-control-label" for="input-email">New Password</label>
                                    <input type="password" name="new_password"
                                      class="form-control form-control-alternative">
                                  </div>
                                </div>

                                <div class="col-lg-12">
                                  <div class="form-group">
                                    <label class="form-control-label" for="input-email">Confirm New Password</label>
                                    <input type="password" name="confirm_password"
                                      class="form-control form-control-alternative">
                                  </div>
                                </div>

                                <div class="col-lg-12">
                                  <div class="form-group">
                                    <input type="submit" id="input-email" name="changePassword"
                                      class="btn btn-success form-control-alternative" value="Change Password">
                                  </div>
                                </div>
                              </div>
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
  require_once('partials/_sidebar.php');
  ?>
</body>

</html>
