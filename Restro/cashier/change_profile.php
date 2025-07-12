<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();

// Update Profile
if (isset($_POST['ChangeProfile'])) {
    $staff_id = $_SESSION['staff_id'];
    $staff_name = $_POST['staff_name'];
    $staff_email = $_POST['staff_email'];

    // Check if an image is uploaded
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $image = $_FILES['profile_image'];
        $target_dir = "../admin/assets/img/";
        $target_file = $target_dir . basename($image["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if the file is an image
        if (getimagesize($image["tmp_name"]) !== false) {
            // Check file size (optional)
            if ($image["size"] < 500000) {
                // Allow certain file formats (optional)
                if (in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
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
        $profile_image = null; // If no image is uploaded, set to null
    }

    // Update the database with profile information
    $Qry = "UPDATE rpos_staff SET staff_name = ?, staff_email = ?, staff_image = ? WHERE staff_id = ?";
    $postStmt = $mysqli->prepare($Qry);
    // Bind parameters
    $rc = $postStmt->bind_param('sssi', $staff_name, $staff_email, $profile_image, $staff_id);
    $postStmt->execute();

    // Declare a variable which will be passed to alert function
    if ($postStmt) {
        $success = "Account Updated" && header("refresh:1; url=dashboard.php");
    } else {
        $err = "Please Try Again Or Try Later";
    }
}

if (isset($_POST['changePassword'])) {
    // Change Password
    $error = 0;
    if (isset($_POST['old_password']) && !empty($_POST['old_password'])) {
        $old_password = mysqli_real_escape_string($mysqli, trim(sha1(md5($_POST['old_password']))));
    } else {
        $error = 1;
        $err = "Old Password Cannot Be Empty";
    }
    if (isset($_POST['new_password']) && !empty($_POST['new_password'])) {
        $new_password = mysqli_real_escape_string($mysqli, trim(sha1(md5($_POST['new_password']))));
    } else {
        $error = 1;
        $err = "New Password Cannot Be Empty";
    }
    if (isset($_POST['confirm_password']) && !empty($_POST['confirm_password'])) {
        $confirm_password = mysqli_real_escape_string($mysqli, trim(sha1(md5($_POST['confirm_password']))));
    } else {
        $error = 1;
        $err = "Confirmation Password Cannot Be Empty";
    }

    if (!$error) {
        $staff_id = $_SESSION['staff_id'];
        $sql = "SELECT * FROM rpos_staff WHERE staff_id = '$staff_id'";
        $res = mysqli_query($mysqli, $sql);
        if (mysqli_num_rows($res) > 0) {
            $row = mysqli_fetch_assoc($res);
            if ($old_password != $row['staff_password']) {
                $err = "Please Enter Correct Old Password";
            } elseif ($new_password != $confirm_password) {
                $err = "Confirmation Password Does Not Match";
            } else {
                $new_password = sha1(md5($_POST['new_password']));
                // Insert Captured information to a database table
                $query = "UPDATE rpos_staff SET staff_password = ? WHERE staff_id = ?";
                $stmt = $mysqli->prepare($query);
                // Bind parameters
                $rc = $stmt->bind_param('si', $new_password, $staff_id);
                $stmt->execute();

                // Declare a variable which will be passed to alert function
                if ($stmt) {
                    $success = "Password Changed" && header("refresh:1; url=dashboard.php");
                } else {
                    $err = "Please Try Again Or Try Later";
                }
            }
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
        $staff_id = $_SESSION['staff_id'];
        $ret = "SELECT * FROM rpos_staff WHERE staff_id = '$staff_id'";
        $stmt = $mysqli->prepare($ret);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($staff = $res->fetch_object()) {
            ?>
            <!-- Header -->
            <div class="header pb-8 pt-5 pt-lg-8 d-flex align-items-center"
                style="min-height: 600px; background-image: url(../admin/assets/img/theme/21231.avif); background-size: cover; background-position: center top;">
                <!-- Mask -->
                <span class="mask bg-gradient-default opacity-8"></span>
                <!-- Header container -->
                <div class="container-fluid d-flex align-items-center">
                    <div class="row">
                        <div class="col-lg-7 col-md-10">
                            <h1 class="display-2 text-white">Hello <?php echo $staff->staff_name; ?></h1>
                            <p class="text-white mt-0 mb-5">This is your profile page. You can customize your profile as you
                                want And also change password too</p>
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
                                            <img src="<?php echo isset($staff->staff_image) ? $staff->staff_image : '../admin/assets/img/theme/man.png'; ?>"
                                                class="rounded-circle"
                                                style="marginborder-radius: 50%; width: 150px; height: 150px; object-fit: cover;">
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-header text-center border-0 pt-8 pt-md-4 pb-0 pb-md-4">
                                <div class="d-flex justify-content-between">
                                </div>
                            </div>
                            <div class="card-body pt-0 pt-md-4">
                                <div class="text-center">
                                    <br>
                                    <br>
                                    <h3>
                                        <?php echo $staff->staff_name; ?>
                                    </h3>
                                    <div class="h5 font-weight-300">
                                        <i class="fas fa-map-marker-alt mr-2"></i><?php echo $staff->staff_email; ?>
                                    </div>
                                </div>
                            </div>
                            <style>
                                .card-profile {
                                    display: flex;
                                    flex-direction: column;
                                    /* Stack image and text vertically */
                                    align-items: center;
                                    /* Center them horizontally */
                                }

                                .card-profile-image {
                                    margin-top: -30px;
                                    /* Moves the image higher to prevent overlap */
                                }

                                .card-body {
                                    padding-top: 80px;
                                    /* Adds space between the image and text */
                                }

                                .card-profile-image img {
                                    border-radius: 50%;
                                    width: 150px;
                                    height: 150px;
                                    object-fit: cover;
                                }
                            </style>
                        </div>
                    </div>
                    <div class="col-xl-8 order-xl-1">
                        <div class="card bg-secondary shadow">
                            <div class="card-header bg-white border-0">
                                <div class="row align-items-center">
                                    <div class="col-8">
                                        <h3 class="mb-0">My account</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <form method="post" enctype="multipart/form-data">
                                    <h6 class="heading-small text-muted mb-4">User Information</h6>
                                    <div class="pl-lg-4">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label class="form-control-label" for="input-username">Staff
                                                        Name</label>
                                                    <input type="text" name="staff_name"
                                                        value="<?php echo $staff->staff_name; ?>" id="input-username"
                                                        class="form-control form-control-alternative">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label class="form-control-label" for="input-email">Email
                                                        Address</label>
                                                    <input type="email" id="input-email"
                                                        value="<?php echo $staff->staff_email; ?>" name="staff_email"
                                                        class="form-control form-control-alternative">
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
                                                    <label class="form-control-label" for="input-username">Old
                                                        Password</label>
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
                                                    <label class="form-control-label" for="input-email">Confirm New
                                                        Password</label>
                                                    <input type="password" name="confirm_password"
                                                        class="form-control form-control-alternative">
                                                </div>
                                            </div>

                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <input type="submit" id="input-email" name="changePassword"
                                                        class="btn btn-success form-control-alternative"
                                                        value="Change Password">
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