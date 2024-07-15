<?php
require_once 'php/core/init.php';
$user = new User();
$override = new OverideData();
$email = new Email();
$random = new Random();
$validate = new validate();
$users = $override->getData('user');

$successMessage = null;
$errorM = false;
$errorMessage = null;

$profile = $override->getNews('user', 'status', 1, 'id', $_GET['user_id'])[0];
$position = $override->getNews('position', 'status', 1, 'id', $profile['position'])[0];
$site = $override->getNews('sites', 'status', 1, 'id', $profile['site_id'])[0];
$country = $override->getNews('country', 'status', 1, 'id', $profile['country'])[0];
$type = $override->getNews('type', 'status', 1, 'id', $profile['type'])[0];


if ($user->isLoggedIn()) {
  if (Input::exists('post')) {
    if (Input::get('update_profile')) {
      $validate = $validate->check($_POST, array(
        'email_address' => array(
          'required' => true,
        ),
        'phone_number' => array(
          'required' => true,
        ),
      ));
      if ($validate->passed()) {
        try {
          $user->updateRecord('user', array(
            'email_address' => Input::get('email_address'),
            'phone_number' => Input::get('phone_number'),
            'country' => Input::get('country'),
            'site_id' => Input::get('site_id'),
            'position' => Input::get('position'),
            'type' => Input::get('type'),
            'address' => Input::get('address'),
            'about' => Input::get('about'),
            'twitter' => Input::get('twitter'),
            'facebook' => Input::get('facebook'),
            'instagram' => Input::get('instagram'),
            'linkedin' => Input::get('linkedin'),
          ), $_GET['user_id']);
          $successMessage = 'Profile Updated Successful';
          Redirect::to('profile.php?id=' . $_GET['id'] . '&msg=' . $successMessage);
        } catch (Exception $e) {
          die($e->getMessage());
        }
      } else {
        $pageError = $validate->errors();
      }
    } else if (Input::get('update_password')) {
      $validate = new validate();
      $validate = $validate->check($_POST, array(
        'new_password' => array(
          'required' => true,
          'min' => 6,
        ),
        'current_password' => array(
          'required' => true,
        ),
        'retype_password' => array(
          'required' => true,
          'matches' => 'new_password'
        )
      ));
      if ($validate->passed()) {
        $salt = $random->get_rand_alphanumeric(32);
        if (Hash::make(Input::get('current_password'), $user->data()->salt) !== $user->data()->password) {
          $errorMessage = 'Your current password is wrong';
        } else {
          try {
            $user->updateRecord('user', array(
              'password' => Hash::make(Input::get('new_password'), $salt),
              'salt' => $salt
            ), $_GET['id']);
          } catch (Exception $e) {
            $e->getMessage();
          }
        }
        $successMessage = 'Password changed successfully';
      } else {
        $pageError = $validate->errors();
      }
    }
  }
} else {
  Redirect::to('index.php');
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Users / Profile - LogBook</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="assets/css/style.css" rel="stylesheet">

  <!-- =======================================================
  * Template Name: NiceAdmin
  * Template URL: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/
  * Updated: Apr 20 2024 with Bootstrap v5.3.3
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body>

  <!-- ======= Header ======= -->
  <?php include 'header.php'; ?>
  <!-- End Header -->

  <!-- ======= Sidebar ======= -->
  <?php include 'sidemenu.php'; ?>
  <!-- End Sidebar-->

  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Profile</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item">Users</li>
          <li class="breadcrumb-item active">Profile</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section profile">
      <?php if ($errorMessage) { ?>
        <div class="alert alert-danger">
          <h4>Error!</h4>
          <?= $errorMessage ?>
        </div>
      <?php } elseif ($pageError) { ?>
        <div class="alert alert-danger">
          <h4>Error!</h4>
          <?php foreach ($pageError as $error) {
            echo $error . ' , ';
          } ?>
        </div>
      <?php } elseif ($successMessage) { ?>
        <div class="alert alert-success">
          <h4>Success!</h4>
          <?= $successMessage ?>
        </div>
      <?php } ?>
      <div class="row">
        <div class="col-xl-4">

          <div class="card">
            <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">

              <img src="assets/img/profile-img.jpg" alt="Profile" class="rounded-circle">
              <h2><?= $profile['firstname'] . ' ' . $profile['lastname'] ?></h2>
              <h3><?= $position['name'] . ' ( ' . $type['name'] . ' ) ' ?></h3>
              <div class="social-links mt-2">
                <a href="#" class="twitter"><i class="bi bi-twitter"></i></a>
                <a href="#" class="facebook"><i class="bi bi-facebook"></i></a>
                <a href="#" class="instagram"><i class="bi bi-instagram"></i></a>
                <a href="#" class="linkedin"><i class="bi bi-linkedin"></i></a>
              </div>
            </div>
          </div>

        </div>

        <div class="col-xl-8">
          <div class="card">
            <div class="card-body pt-3">
              <!-- Bordered Tabs -->
              <ul class="nav nav-tabs nav-tabs-bordered">

                <li class="nav-item">
                  <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile-overview">Overview</button>
                </li>

                <li class="nav-item">
                  <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-edit">Edit Profile</button>
                </li>

                <li class="nav-item">
                  <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-settings">Settings</button>
                </li>

                <li class="nav-item">
                  <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-change-password">Change Password</button>
                </li>

              </ul>
              <div class="tab-content pt-2">

                <div class="tab-pane fade show active profile-overview" id="profile-overview">
                  <h5 class="card-title">About</h5>
                  <p class="small fst-italic"><?= $profile['about'] ?></p>

                  <h5 class="card-title">Profile Details</h5>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label ">Full Name</div>
                    <div class="col-lg-9 col-md-8"><?= $profile['firstname'] . ' ' . $profile['lastname'] ?></div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Type</div>
                    <div class="col-lg-9 col-md-8"><?= $type['name'] ?></div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Site</div>
                    <div class="col-lg-9 col-md-8"><?= $site['name'] ?></div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Job</div>
                    <div class="col-lg-9 col-md-8"><?= $position['name'] ?></div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Country</div>
                    <div class="col-lg-9 col-md-8"><?= $country['name'] ?></div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Address</div>
                    <div class="col-lg-9 col-md-8"><?= $profile['address'] ?></div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Phone</div>
                    <div class="col-lg-9 col-md-8"><?= $profile['phone_number'] ?></div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Email</div>
                    <div class="col-lg-9 col-md-8"><?= $profile['email_address'] ?></div>
                  </div>

                </div>

                <div class="tab-pane fade profile-edit pt-3" id="profile-edit">

                  <!-- Profile Edit Form -->
                  <form method="POST">
                    <div class="row mb-3">
                      <label for="profileImage" class="col-md-4 col-lg-3 col-form-label">Profile Image</label>
                      <div class="col-md-8 col-lg-9">
                        <img src="assets/img/profile-img.jpg" alt="Profile">
                        <div class="pt-2">
                          <a href="#" class="btn btn-primary btn-sm" title="Upload new profile image"><i class="bi bi-upload"></i></a>
                          <a href="#" class="btn btn-danger btn-sm" title="Remove my profile image"><i class="bi bi-trash"></i></a>
                        </div>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="fullName" class="col-md-4 col-lg-3 col-form-label">Full Name</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="fullName" type="text" class="form-control" id="fullName" value="<?= $profile['firstname'] . ' ' . $profile['lastname'] ?>">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="about" class="col-md-4 col-lg-3 col-form-label">About</label>
                      <div class="col-md-8 col-lg-9">
                        <textarea name="about" class="form-control" id="about" style="height: 100px"><?= $profile['about'] ?></textarea>
                      </div>
                    </div>



                    <div class="row mb-3">
                      <label for="Job" class="col-md-4 col-lg-3 col-form-label">Job</label>
                      <div class="col-md-8 col-lg-9">
                        <select class="form-select" id="position" name="position" aria-label="Default select example">
                          <!-- <option selected>Open this select menu</option> -->
                          <option value="<?= $position['id'] ?>"><?php if ($profile['position']) {
                                                                    print_r($position['name']);
                                                                  } else {
                                                                    echo 'Select Position';
                                                                  } ?>
                          </option>
                          <?php foreach ($override->get('position', 'status', 1) as $value) { ?>
                            <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                          <?php } ?>
                        </select>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="Job" class="col-md-4 col-lg-3 col-form-label">Country</label>
                      <div class="col-md-8 col-lg-9">
                        <select class="form-select" id="country" name="country" aria-label="Default select example">
                          <!-- <option selected>Open this select menu</option> -->
                          <option value="<?= $country['id'] ?>"><?php if ($profile['country']) {
                                                                  print_r($country['name']);
                                                                } else {
                                                                  echo 'Select Country';
                                                                } ?>
                          </option>
                          <?php foreach ($override->get('country', 'status', 1) as $value) { ?>
                            <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                          <?php } ?>
                        </select>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="Job" class="col-md-4 col-lg-3 col-form-label">Site</label>
                      <div class="col-md-8 col-lg-9">
                        <select class="form-select" id="site_id" name="site_id" aria-label="Default select example">
                          <!-- <option selected>Open this select menu</option> -->
                          <option value="<?= $site['id'] ?>"><?php if ($profile['site_id']) {
                                                                print_r($site['name']);
                                                              } else {
                                                                echo 'Select Site';
                                                              } ?>
                          </option>
                          <?php foreach ($override->get('sites', 'status', 1) as $value) { ?>
                            <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                          <?php } ?>
                        </select>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="Job" class="col-md-4 col-lg-3 col-form-label">Type</label>
                      <div class="col-md-8 col-lg-9">
                        <select class="form-select" id="type" name="type" aria-label="Default select example">
                          <!-- <option selected>Open this select menu</option> -->
                          <option value="<?= $site['id'] ?>"><?php if ($profile['type']) {
                                                                print_r($type['name']);
                                                              } else {
                                                                echo 'Select Type';
                                                              } ?>
                          </option>
                          <?php foreach ($override->get('type', 'status', 1) as $value) { ?>
                            <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                          <?php } ?>
                        </select>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="Address" class="col-md-4 col-lg-3 col-form-label">Address</label>
                      <div class="col-md-8 col-lg-9">
                        <!-- <input name="address" type="text" class="form-control" id="address" value="A108 Adam Street, New York, NY 535022"> -->
                        <input name="address" type="text" class="form-control" id="address" value="<?= $profile['address'] ?>">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="Phone" class="col-md-4 col-lg-3 col-form-label">Phone</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="phone_number" type="text" class="form-control" id="phone_number" value="<?= $profile['phone_number'] ?>">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="Email" class="col-md-4 col-lg-3 col-form-label">Email</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="email_address" type="email" class="form-control" id="email_address" value="<?= $profile['email_address'] ?>">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="Twitter" class="col-md-4 col-lg-3 col-form-label">Twitter Profile</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="twitter" type="text" class="form-control" id="Twitter" value="<?= $profile['twitter'] ?>">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="Facebook" class="col-md-4 col-lg-3 col-form-label">Facebook Profile</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="facebook" type="text" class="form-control" id="Facebook" value="<?= $profile['facebook'] ?>">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="Instagram" class="col-md-4 col-lg-3 col-form-label">Instagram Profile</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="instagram" type="text" class="form-control" id="Instagram" value="<?= $profile['instagram'] ?>">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="Linkedin" class="col-md-4 col-lg-3 col-form-label">Linkedin Profile</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="linkedin" type="text" class="form-control" id="Linkedin" value="<?= $profile['linkedin'] ?>">
                      </div>
                    </div>

                    <div class="text-center">
                      <input type="submit" name="update_profile" value="Save Changes" class="btn btn-primary btn-block">
                    </div>
                  </form><!-- End Profile Edit Form -->

                </div>

                <div class="tab-pane fade pt-3" id="profile-settings">
                  <!-- Settings Form -->
                  <form>

                    <div class="row mb-3">
                      <label for="fullName" class="col-md-4 col-lg-3 col-form-label">Email Notifications</label>
                      <div class="col-md-8 col-lg-9">
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="changesMade" checked>
                          <label class="form-check-label" for="changesMade">
                            Changes made to your account
                          </label>
                        </div>
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="newProducts" checked>
                          <label class="form-check-label" for="newProducts">
                            Information on new products and services
                          </label>
                        </div>
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="proOffers">
                          <label class="form-check-label" for="proOffers">
                            Marketing and promo offers
                          </label>
                        </div>
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="securityNotify" checked disabled>
                          <label class="form-check-label" for="securityNotify">
                            Security alerts
                          </label>
                        </div>
                      </div>
                    </div>

                    <div class="text-center">
                      <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                  </form><!-- End settings Form -->

                </div>

                <div class="tab-pane fade pt-3" id="profile-change-password">
                  <!-- Change Password Form -->
                  <form method="post">

                    <div class="row mb-3">
                      <label for="currentPassword" class="col-md-4 col-lg-3 col-form-label">Current Password</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="current_password" type="password" class="form-control" id="currentPassword">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="newPassword" class="col-md-4 col-lg-3 col-form-label">New Password</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="new_password" type="password" class="form-control" id="newPassword">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="renewPassword" class="col-md-4 col-lg-3 col-form-label">Re-enter New Password</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="retype_password" type="password" class="form-control" id="renewPassword">
                      </div>
                    </div>

                    <div class="text-center">
                      <input type="submit" name="update_password" value="Change Password" class="btn btn-primary btn-block">
                      <!-- <button type="submit" class="btn btn-primary"></button> -->
                    </div>
                  </form><!-- End Change Password Form -->

                </div>

              </div><!-- End Bordered Tabs -->

            </div>
          </div>

        </div>
      </div>
    </section>

  </main>
  <!-- End #main -->

  <!-- ======= Footer ======= -->
  <?php include 'footer.php'; ?>
  <!-- End Footer -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/chart.js/chart.umd.js"></script>
  <script src="assets/vendor/echarts/echarts.min.js"></script>
  <script src="assets/vendor/quill/quill.js"></script>
  <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>

</body>

</html>