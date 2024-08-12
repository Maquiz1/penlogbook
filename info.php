<?php

require_once 'php/core/init.php';
$user = new User();
$override = new OverideData();
$email = new Email();
$random = new Random();
$validate = new validate();
$successMessage = null;
$pageError = null;
$errorMessage = null;
$numRec = 8;


if ($user->isLoggedIn()) {
    if (Input::exists('post')) {
        if (Input::get('delete_competence')) {
            $validate = $validate->check($_POST, array(
                'id' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    $user->updateRecord('logs', array(
                        'status' => 0,
                    ), Input::get('id'));
                    $successMessage = 'Competence Deleted Successful';
                    Redirect::to('info.php?id=' . $_GET['id'] . '&disease=' . $_GET['disease'] . '&msg=' . $successMessage);
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } else if (Input::get('update_profile')) {
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
                    ), $_GET['id']);
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

<?php include 'head.php'; ?>

<body>

    <!-- ======= Header ======= -->
    <?php include 'header.php'; ?>
    <!-- End Header -->

    <!-- ======= Sidebar ======= -->
    <?php include 'sidemenu.php'; ?>
    <!-- End Sidebar-->

    <main id="main" class="main">

        <div class="pagetitle">
            <h1><?= $override->getNews('diseases', 'status', 1, 'id', $_GET['disease_id'])[0]['name'] ?> Tables</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item">Tables</li>
                    <li class="breadcrumb-item active"><?= $override->getNews('diseases', 'status', 1, 'id', $_GET['disease_id'])[0]['name'] ?></li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section">
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
            <?php } elseif ($_GET['msg']) { ?>
                <div class="alert alert-success">
                    <h4>Success!</h4>
                    <?= $_GET['msg'] ?>
                </div>
            <?php } ?>
            <div class="row">
                <?php if ($_GET['id'] == 1) { ?>
                    <div class="col-lg-12">
                        <?php if ($user->data()->accessLevel == 1 || $user->data()->accessLevel == 2) { ?>

                            <!-- <a href="add.php?disease=<?= $_GET['disease'] ?>" class="btn btn-info">Add New Patient Visit</a> -->
                        <?php } ?>

                        <?php
                        $pagNum = 0;
                        $pagNum = $override->countData2('mentorships', 'status', 1, 'disease_id', $_GET['disease_id'], 'create_id', $user->data()->id);

                        $pages = ceil($pagNum / $numRec);
                        if (!$_GET['page'] || $_GET['page'] == 1) {
                            $page = 0;
                        } else {
                            $page = ($_GET['page'] * $numRec) - $numRec;
                        }
                        $data = $override->getWithLimit3('mentorships', 'status', 1, 'disease_id', $_GET['disease_id'], 'create_id', $user->data()->id, $page, $numRec);
                        ?>
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Table for Mentorship Visits</h5>

                                <!-- Table with stripped rows -->
                                <table class="table table-bordered table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Mentorship Date</th>
                                            <!-- <th scope="col">Mentee Name</th> -->
                                            <!-- <th scope="col">Menter Name</th> -->
                                            <!-- <th scope="col">PID</th> -->
                                            <th scope="col">Disease</th>
                                            <th scope="col">Site</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $x = 1;
                                        foreach ($data as $value) {
                                            // $mentor = $override->getNews('user', 'status', 1, 'id', $value['mentor'])[0];
                                            // $mentee = $override->getNews('user', 'status', 1, 'id', $value['mentee'])[0];
                                            $site = $override->getNews('sites', 'status', 1, 'id', $value['site_id'])[0];
                                            $disease = $override->getNews('diseases', 'status', 1, 'id', $value['disease_id'])[0];
                                        ?>
                                            <tr>
                                                <th scope="row"><?= $x; ?></th>
                                                <td class="table-user">
                                                    <?= $value['mentorship_date']; ?>
                                                </td>
                                                <!-- <td class="table-user">
                                                    <?= $mentee['firstname'] . ' - ' . $mentee['lastname']; ?>
                                                </td> -->
                                                <!-- <td class="table-user">
                                                    <?= $mentor['firstname'] . ' - ' . $mentor['lastname']; ?>
                                                </td> -->
                                                <!-- <td class="table-user">
                                                    <?= $value['pids']; ?>
                                                </td> -->
                                                <td class="table-user">
                                                    <?= $disease['name']; ?>
                                                </td>
                                                <td class="table-user">
                                                    <?= $site['name']; ?>
                                                </td>
                                                <td class="text-center">
                                                    <?php if ($user->data()->accessLevel == 1 || $user->data()->accessLevel == 2) { ?>
                                                        <a href="add.php?&id=1&mentorship_id=<?= $value['id'] ?>&disease_id=<?= $value['disease_id'] ?>" class="btn btn-info">Update</a>
                                                    <?php } ?>

                                                    <!-- <?php if ($user->data()->accessLevel == 1 || $user->data()->accessLevel == 2 || $user->data()->accessLevel == 3) { ?>
                                                        <a href="add.php?&id=1&mentorship_id=<?= $value['id'] ?>&disease=<?= $value['disease_id'] ?>" class="btn btn-success">View</a>
                                                    <?php } ?> -->

                                                    <?php if ($user->data()->accessLevel == 1 || $user->data()->accessLevel == 2 || $user->data()->accessLevel == 3) { ?>
                                                        <a href="add.php?&id=2&mentorship_id=<?= $value['id'] ?>&disease_id=<?= $value['disease_id'] ?>" class="btn btn-secondary">Add New Assessments</a>
                                                    <?php } ?>

                                                    <?php if ($user->data()->accessLevel == 1 || $user->data()->accessLevel == 2 || $user->data()->accessLevel == 3) { ?>
                                                        <a href="info.php?&id=2&mentorship_id=<?= $value['id'] ?>&disease_id=<?= $value['disease_id'] ?>" class="btn btn-primary">Viw Assessments</a>
                                                    <?php } ?>

                                                    <?php if ($user->data()->accessLevel == 1 || $user->data()->accessLevel == 2) { ?>
                                                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#delete<?= $value['id'] ?>">
                                                            Delete
                                                        </button>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                            <!-- Basic Modal -->
                                            <div class="modal fade" id="delete<?= $value['id'] ?>" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form method="POST">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Delete</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                Are you sure you want to delete this Assessment
                                                                ?
                                                            </div>
                                                            <div class="modal-footer">
                                                                <input type="hidden" name="id" value="<?= $value['id'] ?>">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                <input type="submit" name="delete_competence" value="Save Changes" class="btn btn-danger btn-block">
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- End Basic Modal-->
                                        <?php
                                            $x++;
                                        }
                                        ?>
                                    </tbody>
                                </table>
                                <!-- End Table with stripped rows -->

                            </div>
                        </div>
                        <div class="card-footer clearfix">
                            <ul class="pagination pagination-sm m-0 float-right">
                                <li class="page-item">
                                    <a class="page-link" href="info.php?id=1&page=<?php if (($_GET['page'] - 1) > 0) {
                                                                                        echo $_GET['page'] - 1;
                                                                                    } else {
                                                                                        echo 1;
                                                                                    } ?>">&laquo;
                                    </a>
                                </li>
                                <?php for ($i = 1; $i <= $pages; $i++) { ?>
                                    <li class="page-item">
                                        <a class="page-link <?php if ($i == $_GET['page']) {
                                                                echo 'active';
                                                            } ?>" href="info.php?id=1&page=<?= $i ?>"><?= $i ?>
                                        </a>
                                    </li>
                                <?php } ?>
                                <li class="page-item">
                                    <a class="page-link" href="info.php?id=1&page=<?php if (($_GET['page'] + 1) <= $pages) {
                                                                                        echo $_GET['page'] + 1;
                                                                                    } else {
                                                                                        echo $i - 1;
                                                                                    } ?>">&raquo;
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                <?php } elseif ($_GET['id'] == 2) { ?>
                    <div class="col-lg-12">
                        <?php if ($user->data()->accessLevel == 1 || $user->data()->accessLevel == 2) { ?>
                            <a href="add.php?id=2&mentorship_id=<?= $_GET['mentorship_id'] ?>&disease=<?= $_GET['disease'] ?>" class="btn btn-info">Add New Assessments</a>
                            <hr>
                            <!-- <br> -->
                        <?php } ?>

                        <?php
                        if ($_GET['disease']) {
                            if ($user->data()->accessLevel == 1 || $user->data()->accessLevel == 3) {
                                $pagNum = 0;
                                $pagNum = $override->countData('logs', 'status', 1, 'mentorship_id', $_GET['mentorship_id']);

                                $pages = ceil($pagNum / $numRec);
                                if (!$_GET['page'] || $_GET['page'] == 1) {
                                    $page = 0;
                                } else {
                                    $page = ($_GET['page'] * $numRec) - $numRec;
                                }
                                $data = $override->getWithLimit1('logs', 'status', 1, 'mentorship_id', $_GET['mentorship_id'], $page, $numRec);
                            } else {
                                $pagNum = 0;
                                $pagNum = $override->countData2('logs', 'status', 1, 'mentorship_id', $_GET['mentorship_id'], 'mentor', $user->data()->id);

                                $pages = ceil($pagNum / $numRec);
                                if (!$_GET['page'] || $_GET['page'] == 1) {
                                    $page = 0;
                                } else {
                                    $page = ($_GET['page'] * $numRec) - $numRec;
                                }
                                $data = $override->getWithLimit3('logs', 'status', 1, 'mentorship_id', $_GET['mentorship_id'], 'mentor', $user->data()->id, $page, $numRec);
                            }
                        } else {
                            if ($user->data()->accessLevel == 1 || $user->data()->accessLevel == 3) {
                                $pagNum = 0;
                                $pagNum = $override->getCount('logs', 'status', 1);

                                $pages = ceil($pagNum / $numRec);
                                if (!$_GET['page'] || $_GET['page'] == 1) {
                                    $page = 0;
                                } else {
                                    $page = ($_GET['page'] * $numRec) - $numRec;
                                }
                                $data = $override->getWithLimit('logs', 'status', 1, $page, $numRec);
                            } else {
                                $pagNum = 0;
                                $pagNum = $override->getCount('logs', 'status', 1);

                                $pages = ceil($pagNum / $numRec);
                                if (!$_GET['page'] || $_GET['page'] == 1) {
                                    $page = 0;
                                } else {
                                    $page = ($_GET['page'] * $numRec) - $numRec;
                                }
                                $data = $override->getWithLimit('logs', 'status', 1, $page, $numRec);
                            }
                        }

                        ?>
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Table for <?= $override->getNews('category', 'status', 1, 'id', $_GET['disease'])[0]['name'] ?> Comptences</h5>

                                <!-- Table with stripped rows -->
                                <table class="table table-bordered table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <!-- <th scope="col">Visit Date</th> -->
                                            <th scope="col">Mentee Name</th>
                                            <th scope="col">Menter Name</th>
                                            <!-- <th scope="col">PID</th> -->
                                            <!-- <th scope="col">Site</th> -->
                                            <th scope="col">Disease</th>
                                            <th scope="col">Competences</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $x = 1;
                                        foreach ($data as $value) {
                                            $mentor = $override->getNews('user', 'status', 1, 'id', $value['mentor'])[0];
                                            $mentee = $override->getNews('user', 'status', 1, 'id', $value['mentee'])[0];
                                            $site = $override->getNews('sites', 'status', 1, 'id', $value['site_id'])[0];

                                            $disease = $override->getNews('category', 'status', 1, 'id', $value['disease'])[0];

                                            $competencies[] = '';
                                            $competencies = $value['competencies'][0];

                                            print_r($competencies);

                                            // $competencies = $override->getNews('competencies', 'status', 1, 'id', $value['competencies'])[0];

                                            // foreach(){
                                            // }

                                        ?>
                                            <tr>
                                                <th scope="row"><?= $x; ?></th>
                                                <!-- <td class="table-user">
                                                    <?= $value['visit_date']; ?>
                                                </td> -->
                                                <td class="table-user">
                                                    <?= $mentee['firstname'] . ' - ' . $mentee['lastname']; ?>
                                                </td>
                                                <td class="table-user">
                                                    <?= $mentor['firstname'] . ' - ' . $mentor['lastname']; ?>
                                                </td>
                                                <!-- <td class="table-user">
                                                    <?= $value['pids']; ?>
                                                </td> -->
                                                <!-- <td class="table-user">
                                                    <?= $site['name']; ?>
                                                </td> -->
                                                <td class="table-user">
                                                    <?= $disease['name']; ?>
                                                </td>
                                                <td class="table-user">
                                                    <?= $site['name']; ?>
                                                </td>
                                                <td class="text-center">
                                                    <?php if ($user->data()->accessLevel == 1 || $user->data()->accessLevel == 2) { ?>
                                                        <a href="add.php?&id=2&mentorship_id=<?= $_GET['mentorship_id'] ?>&disease=<?= $_GET['disease'] ?>&log_id=<?= $value['id'] ?>&disease=<?= $value['disease'] ?>" class="btn btn-info">Update</a>
                                                    <?php } ?>

                                                    <?php if ($user->data()->accessLevel == 1 || $user->data()->accessLevel == 2 || $user->data()->accessLevel == 3) { ?>
                                                        <a href="add.php?&id=2&mentorship_id=<?= $_GET['mentorship_id'] ?>&disease=<?= $_GET['disease'] ?>&log_id=<?= $value['id'] ?>&disease=<?= $value['disease'] ?>" class="btn btn-success">View</a>
                                                    <?php } ?>

                                                    <?php if ($user->data()->accessLevel == 1 || $user->data()->accessLevel == 2) { ?>
                                                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#delete<?= $value['id'] ?>">
                                                            Delete
                                                        </button>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                            <!-- Basic Modal -->
                                            <div class="modal fade" id="delete<?= $value['id'] ?>" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form method="POST">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Delete</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                Are you sure you want to delete this Assessment ?
                                                            </div>
                                                            <div class="modal-footer">
                                                                <input type="hidden" name="id" value="<?= $value['id'] ?>">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                <input type="submit" name="delete_competence" value="Save Changes" class="btn btn-danger btn-block">
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- End Basic Modal-->
                                        <?php
                                            $x++;
                                        }
                                        ?>
                                    </tbody>
                                </table>
                                <!-- End Table with stripped rows -->

                            </div>
                        </div>
                        <div class="card-footer clearfix">
                            <ul class="pagination pagination-sm m-0 float-right">
                                <li class="page-item">
                                    <a class="page-link" href="info.php?id=1&page=<?php if (($_GET['page'] - 1) > 0) {
                                                                                        echo $_GET['page'] - 1;
                                                                                    } else {
                                                                                        echo 1;
                                                                                    } ?>">&laquo;
                                    </a>
                                </li>
                                <?php for ($i = 1; $i <= $pages; $i++) { ?>
                                    <li class="page-item">
                                        <a class="page-link <?php if ($i == $_GET['page']) {
                                                                echo 'active';
                                                            } ?>" href="info.php?id=1&page=<?= $i ?>"><?= $i ?>
                                        </a>
                                    </li>
                                <?php } ?>
                                <li class="page-item">
                                    <a class="page-link" href="info.php?id=1&page=<?php if (($_GET['page'] + 1) <= $pages) {
                                                                                        echo $_GET['page'] + 1;
                                                                                    } else {
                                                                                        echo $i - 1;
                                                                                    } ?>">&raquo;
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                <?php } elseif ($_GET['id'] == 3) { ?>
                    <div class="col-lg-12">
                        <?php if ($user->data()->accessLevel == 1 || $user->data()->accessLevel == 2) { ?>

                            <!-- <a href="add.php?disease=<?= $_GET['disease'] ?>" class="btn btn-info">Add New Patient Visit</a> -->
                        <?php } ?>

                        <?php
                        if ($_GET['type']) {
                            if ($user->data()->accessLevel == 1 || $user->data()->accessLevel == 3) {
                                $pagNum = 0;
                                $pagNum = $override->countData('user', 'status', 1, 'type', $_GET['type']);

                                $pages = ceil($pagNum / $numRec);
                                if (!$_GET['page'] || $_GET['page'] == 1) {
                                    $page = 0;
                                } else {
                                    $page = ($_GET['page'] * $numRec) - $numRec;
                                }
                                $data = $override->getWithLimit1('user', 'status', 1, 'type', $_GET['type'], $page, $numRec);
                            } else {
                                $pagNum = 0;
                                $pagNum = $override->countData('user', 'status', 1, 'type', $_GET['type']);

                                $pages = ceil($pagNum / $numRec);
                                if (!$_GET['page'] || $_GET['page'] == 1) {
                                    $page = 0;
                                } else {
                                    $page = ($_GET['page'] * $numRec) - $numRec;
                                }
                                $data = $override->getWithLimit1('user', 'status', 1, 'type', $_GET['type'], $page, $numRec);
                            }
                        } else {
                            if ($user->data()->accessLevel == 1 || $user->data()->accessLevel == 3) {
                                $pagNum = 0;
                                $pagNum = $override->countData('user', 'status', 1, 'type', $_GET['type']);

                                $pages = ceil($pagNum / $numRec);
                                if (!$_GET['page'] || $_GET['page'] == 1) {
                                    $page = 0;
                                } else {
                                    $page = ($_GET['page'] * $numRec) - $numRec;
                                }
                                $data = $override->getWithLimit1('user', 'status', 1, 'type', $_GET['type'], $page, $numRec);
                            } else {
                                $pagNum = 0;
                                $pagNum = $override->countData('user', 'status', 1, 'type', $_GET['type']);

                                $pages = ceil($pagNum / $numRec);
                                if (!$_GET['page'] || $_GET['page'] == 1) {
                                    $page = 0;
                                } else {
                                    $page = ($_GET['page'] * $numRec) - $numRec;
                                }
                                $data = $override->getWithLimit1('user', 'status', 1, 'type', $_GET['type'], $page, $numRec);
                            }
                        }

                        ?>
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Table for <?= $override->getNews('user', 'status', 1, 'id', $_GET['type'])[0]['name'] ?> Comptences</h5>

                                <!-- Table with stripped rows -->
                                <table class="table table-bordered table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Name</th>
                                            <th scope="col">Position</th>
                                            <th scope="col">Site</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $x = 1;
                                        foreach ($data as $value) {
                                            $position = $override->getNews('position', 'status', 1, 'id', $value['position'])[0];
                                            $site = $override->getNews('sites', 'status', 1, 'id', $value['site_id'])[0];
                                            $category = $override->getNews('category', 'status', 1, 'id', $value['disease'])[0];
                                            $type = $override->getNews('type', 'status', 1, 'id', $value['type'])[0];
                                        ?>
                                            <tr>
                                                <th scope="row"><?= $x; ?></th>
                                                <td class="table-user">
                                                    <?= $value['firstname'] . ' - ' . $value['lastname']; ?>
                                                </td>
                                                <td class="table-user">
                                                    <?= $position['name'] . ' ( ' . $type['name'] . ' ) '; ?>
                                                </td>
                                                <td class="table-user">
                                                    <?= $site['name']; ?>
                                                </td>
                                                <td class="text-center">
                                                    <?php if ($user->data()->accessLevel == 1) { ?>
                                                        <a href="profile.php?id=1&user_id=<?= $value['id'] ?>&disease=<?= $value['disease'] ?>" class="btn btn-info">Update</a>
                                                    <?php } ?>
                                                    <?php if ($user->data()->accessLevel == 1) { ?>

                                                        <a href="profile.php?id=1&user_id=<?= $value['id'] ?>&disease=<?= $value['disease'] ?>" class="btn btn-success">View</a>

                                                        <a href="#delete<?= $value['id'] ?>" role="button" class="btn btn-danger" data-toggle="modal">Delete</a>

                                                    <?php } ?>

                                                    <?php if ($user->data()->accessLevel == 1 || $user->data()->accessLevel == 2) { ?>
                                                        <a href="info.php?id=5&user_id=<?= $value['id'] ?>" class="btn btn-info">Mentorships</a><br><br>
                                                    <?php } ?>

                                                </td>
                                            </tr>
                                            </tr>
                                        <?php
                                            $x++;
                                        }
                                        ?>
                                    </tbody>
                                </table>
                                <!-- End Table with stripped rows -->

                            </div>
                        </div>
                        <div class="card-footer clearfix">
                            <ul class="pagination pagination-sm m-0 float-right">
                                <li class="page-item">
                                    <a class="page-link" href="info.php?id=3&page=<?php if (($_GET['page'] - 1) > 0) {
                                                                                        echo $_GET['page'] - 1;
                                                                                    } else {
                                                                                        echo 1;
                                                                                    } ?>">&laquo;
                                    </a>
                                </li>
                                <?php for ($i = 1; $i <= $pages; $i++) { ?>
                                    <li class="page-item">
                                        <a class="page-link <?php if ($i == $_GET['page']) {
                                                                echo 'active';
                                                            } ?>" href="info.php?id=3&page=<?= $i ?>"><?= $i ?>
                                        </a>
                                    </li>
                                <?php } ?>
                                <li class="page-item">
                                    <a class="page-link" href="info.php?id=3&page=<?php if (($_GET['page'] + 1) <= $pages) {
                                                                                        echo $_GET['page'] + 1;
                                                                                    } else {
                                                                                        echo $i - 1;
                                                                                    } ?>">&raquo;
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                <?php } elseif ($_GET['id'] == 4) { ?>
                    <div class="col-lg-12">
                        <?php if ($user->data()->accessLevel == 1 || $user->data()->accessLevel == 2) { ?>

                            <!-- <a href="add.php?disease=<?= $_GET['disease'] ?>" class="btn btn-info">Add New Patient Visit</a> -->
                        <?php } ?>

                        <?php
                        if ($_GET['type']) {
                            if ($user->data()->accessLevel == 1 || $user->data()->accessLevel == 3) {
                                $pagNum = 0;
                                $pagNum = $override->countData('user', 'status', 1, 'type', $_GET['type']);

                                $pages = ceil($pagNum / $numRec);
                                if (!$_GET['page'] || $_GET['page'] == 1) {
                                    $page = 0;
                                } else {
                                    $page = ($_GET['page'] * $numRec) - $numRec;
                                }
                                $data = $override->getWithLimit1('user', 'status', 1, 'type', $_GET['type'], $page, $numRec);

                                $site_data = $override->getData('sites');
                                $Total = $override->getCount('mentorship', 'status', 1);
                                $data_enrolled = $override->getCount('mentorship', 'status', 1);

                                $successMessage = 'Report Successful Created';
                            } else {
                                $pagNum = 0;
                                $pagNum = $override->countData('user', 'status', 1, 'type', $_GET['type']);

                                $pages = ceil($pagNum / $numRec);
                                if (!$_GET['page'] || $_GET['page'] == 1) {
                                    $page = 0;
                                } else {
                                    $page = ($_GET['page'] * $numRec) - $numRec;
                                }
                                $data = $override->getWithLimit1('user', 'status', 1, 'type', $_GET['type'], $page, $numRec);

                                $site_data = $override->getData('sites');
                                $Total = $override->getCount('mentorship', 'status', 1);
                                $data_enrolled = $override->getCount('mentorship', 'status', 1);

                                $successMessage = 'Report Successful Created';
                            }
                        } else {
                            if ($user->data()->accessLevel == 1 || $user->data()->accessLevel == 3) {
                                $pagNum = 0;
                                $pagNum = $override->countData('user', 'status', 1, 'type', $_GET['type']);

                                $pages = ceil($pagNum / $numRec);
                                if (!$_GET['page'] || $_GET['page'] == 1) {
                                    $page = 0;
                                } else {
                                    $page = ($_GET['page'] * $numRec) - $numRec;
                                }
                                $data = $override->getWithLimit1('user', 'status', 1, 'type', $_GET['type'], $page, $numRec);

                                $site_data = $override->getData('sites');
                                $Total = $override->getCount('mentorship', 'status', 1);
                                $data_enrolled = $override->getCount('mentorship', 'status', 1);

                                $successMessage = 'Report Successful Created';
                            } else {
                                $pagNum = 0;
                                $pagNum = $override->countData('user', 'status', 1, 'type', $_GET['type']);

                                $pages = ceil($pagNum / $numRec);
                                if (!$_GET['page'] || $_GET['page'] == 1) {
                                    $page = 0;
                                } else {
                                    $page = ($_GET['page'] * $numRec) - $numRec;
                                }
                                $data = $override->getWithLimit1('user', 'status', 1, 'type', $_GET['type'], $page, $numRec);

                                $site_data = $override->getData('sites');
                                $Total = $override->getCount('mentorship', 'status', 1);
                                $data_enrolled = $override->getCount('mentorship', 'status', 1);

                                $successMessage = 'Report Successful Created';
                            }
                        }

                        ?>
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Table for <?= $override->getNews('user', 'status', 1, 'id', $_GET['type'])[0]['name'] ?> Comptences</h5>

                                <!-- Table with stripped rows -->
                                <table class="table table-bordered table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Site</th>
                                            <th scope="col">Total</th>
                                            <th scope="col">Winstone</th>
                                            <th scope="col">Carol</th>
                                            <!-- <th class="text-center">Action</th> -->
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $x = 1;
                                        $i = 1;
                                        foreach ($site_data as $row) {
                                            // $position = $override->getNews('position', 'status', 1, 'id', $value['position'])[0];
                                            // $site = $override->getNews('sites', 'status', 1, 'id', $value['site_id'])[0];
                                            // $category = $override->getNews('category', 'status', 1, 'id', $value['disease'])[0];
                                            // $type = $override->getNews('type', 'status', 1, 'id', $value['type'])[0];

                                            $registered = $override->countData('mentorship', 'status', 1, 'site_id', $row['id']);
                                            $registered_Total = $override->getCount('mentorship', 'status', 1);
                                            $screened = $override->countData2('mentorship', 'status', 1, 'mentor', 1, 'site_id', $row['id']);
                                            $screened_Total = $override->countData('mentorship', 'status', 1, 'mentor', 1);
                                            $sickle_cell = $override->countData2('mentorship', 'status', 1, 'mentor', 2, 'site_id', $row['id']);
                                            $sickle_cell_Total = $override->countData('mentorship', 'status', 1, 'mentor', 2);
                                            // $cardiac = $override->countData2('clients', 'status', 1, 'cardiac', 1, 'site_id', $row['id']);
                                            // $cardiac_Total = $override->countData('clients', 'status', 1, 'cardiac', 1);
                                            // $diabetes = $override->countData2('clients', 'status', 1, 'diabetes', 1, 'site_id', $row['id']);
                                            // $diabetes_Total = $override->countData('clients', 'status', 1, 'diabetes', 1);
                                            // $eligible = $override->countData2('clients', 'status', 1, 'eligible', 1, 'site_id', $row['id']);
                                            // $eligible_Total = $override->countData('clients', 'status', 1, 'eligible', 1);
                                            // $enrolled = $override->countData2('clients', 'status', 1, 'enrolled', 1, 'site_id', $row['id']);
                                            // $enrolled_Total = $override->countData('clients', 'status', 1, 'enrolled', 1);
                                            // $end_study = $override->countData2('clients', 'status', 1, 'end_study', 1, 'site_id', $row['id']);
                                            // $end_study_Total = $override->countData('clients', 'status', 1, 'end_study', 1);

                                        ?>
                                            <tr>
                                                <td scope="row" class="text-center"><?php echo $i++; ?></td>
                                                <td class="table-user"><?php echo $row['name'] ?></td>
                                                <td class="">
                                                    <p class="m-0 truncate-1"><?php echo $registered ?></p>
                                                </td>
                                                <td class="">
                                                    <p class="m-0 truncate-1"><?php echo $screened ?></p>
                                                </td>
                                                <td class="">
                                                    <p class="m-0 truncate-1"><?php echo $cardiac ?></p>
                                                </td>
                                                <!-- <td class="">
                                                    <p class="m-0 truncate-1"><?php echo $diabetes ?></p>
                                                </td>
                                                <td class="">
                                                    <p class="m-0 truncate-1"><?php echo $sickle_cell ?></p>
                                                </td>
                                                <td class="">
                                                    <p class="m-0 truncate-1"><?php echo $other ?></p>
                                                </td>
                                                <td class="">
                                                    <p class="m-0 truncate-1"><?php echo $eligible ?></p>
                                                </td>
                                                <td class="">
                                                    <p class="m-0 truncate-1"><?php echo $enrolled ?></p>
                                                </td>

                                                <td class="">
                                                    <p class="m-0 truncate-1"><?php echo $end_study ?></p> -->
                                                </td>
                                            </tr>
                                        <?php
                                            $x++;
                                        }
                                        ?>
                                        <tr>
                                            <td class="text-center"></td>
                                            <td class="">TOTAL</td>
                                            <td class="">
                                                <p class="m-0 truncate-1"><?php echo $registered_Total ?></p>
                                            </td>
                                            <td class="">
                                                <p class="m-0 truncate-1"><?php echo $screened_Total ?></p>
                                            </td>
                                            <td class="">
                                                <p class="m-0 truncate-1"><?php echo $cardiac_Total ?></p>
                                            </td>
                                            <!-- <td class="">
                                                <p class="m-0 truncate-1"><?php echo $diabetes_Total ?></p>
                                            </td>
                                            <td class="">
                                                <p class="m-0 truncate-1"><?php echo $sickle_cell_Total ?></p>
                                            </td>
                                            <td class="">
                                                <p class="m-0 truncate-1"><?php echo $other_Total ?></p>
                                            </td>
                                            <td class="">
                                                <p class="m-0 truncate-1"><?php echo $eligible_Total ?></p>
                                            </td>
                                            <td class="">
                                                <p class="m-0 truncate-1"><?php echo $enrolled_Total ?></p>
                                            </td>

                                            <td class="">
                                                <p class="m-0 truncate-1"><?php echo $end_study_Total ?></p> -->
                                            <!-- </td> -->
                                        </tr>
                                    </tbody>
                                </table>
                                <!-- End Table with stripped rows -->

                            </div>
                        </div>
                        <div class="card-footer clearfix">
                            <ul class="pagination pagination-sm m-0 float-right">
                                <li class="page-item">
                                    <a class="page-link" href="info.php?id=3&page=<?php if (($_GET['page'] - 1) > 0) {
                                                                                        echo $_GET['page'] - 1;
                                                                                    } else {
                                                                                        echo 1;
                                                                                    } ?>">&laquo;
                                    </a>
                                </li>
                                <?php for ($i = 1; $i <= $pages; $i++) { ?>
                                    <li class="page-item">
                                        <a class="page-link <?php if ($i == $_GET['page']) {
                                                                echo 'active';
                                                            } ?>" href="info.php?id=3&page=<?= $i ?>"><?= $i ?>
                                        </a>
                                    </li>
                                <?php } ?>
                                <li class="page-item">
                                    <a class="page-link" href="info.php?id=3&page=<?php if (($_GET['page'] + 1) <= $pages) {
                                                                                        echo $_GET['page'] + 1;
                                                                                    } else {
                                                                                        echo $i - 1;
                                                                                    } ?>">&raquo;
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                <?php } elseif ($_GET['id'] == 5) { ?>
                    <div class="col-lg-12 float-none">
                        <?php if ($user->data()->accessLevel == 1 || $user->data()->accessLevel == 2) { ?>
                            <hr>

                            <!-- <div> -->
                            <div class="float-start">
                                <a href="add.php?id=2&mentorship_id=<?= $_GET['mentorship_id'] ?>&disease=<?= $_GET['disease'] ?>" class="btn btn-info">Add New Assessments</a>
                            </div>
                            <div class="float-end">
                                <a href="mentees.php?id=5&user_id=<?= $_GET['user_id'] ?>" class="btn btn-success">Mentorships</a><br><br>
                            </div>
                            <!-- </div> -->
                            <hr>
                            <br>
                        <?php } ?>

                        <?php
                        $pagNum = 0;
                        $pagNum = $override->getCount1('logs', 'status', 1, 'mentee', $_GET['user_id']);

                        $pages = ceil($pagNum / $numRec);
                        if (!$_GET['page'] || $_GET['page'] == 1) {
                            $page = 0;
                        } else {
                            $page = ($_GET['page'] * $numRec) - $numRec;
                        }
                        $data = $override->getWithLimit1('logs', 'status', 1, 'mentee', $_GET['user_id'], $page, $numRec);

                        ?>
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Table for <?= $override->getNews('category', 'status', 1, 'id', $_GET['disease'])[0]['name'] ?> Comptences</h5>

                                <!-- Table with stripped rows -->
                                <table class="table table-bordered table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Visit Date</th>
                                            <th scope="col">Mentee Name</th>
                                            <th scope="col">Menter Name</th>
                                            <th scope="col">PID</th>
                                            <th scope="col">Site</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $x = 1;
                                        foreach ($data as $value) {
                                            $mentor = $override->getNews('user', 'status', 1, 'id', $value['mentor'])[0];
                                            $mentee = $override->getNews('user', 'status', 1, 'id', $value['mentee'])[0];
                                            $site = $override->getNews('sites', 'status', 1, 'id', $value['site_id'])[0];
                                        ?>
                                            <tr>
                                                <th scope="row"><?= $x; ?></th>
                                                <td class="table-user">
                                                    <?= $value['visit_date']; ?>
                                                </td>
                                                <td class="table-user">
                                                    <?= $mentee['firstname'] . ' - ' . $mentee['lastname']; ?>
                                                </td>
                                                <td class="table-user">
                                                    <?= $mentor['firstname'] . ' - ' . $mentor['lastname']; ?>
                                                </td>
                                                <td class="table-user">
                                                    <?= $value['pids']; ?>
                                                </td>
                                                <td class="table-user">
                                                    <?= $site['name']; ?>
                                                </td>
                                                <td class="text-center">
                                                    <?php if ($user->data()->accessLevel == 1 || $user->data()->accessLevel == 2) { ?>
                                                        <a href="add.php?&id=2&mentorship_id=<?= $_GET['mentorship_id'] ?>&disease=<?= $_GET['disease'] ?>&log_id=<?= $value['id'] ?>&disease=<?= $value['disease'] ?>" class="btn btn-info">Update</a>
                                                    <?php } ?>

                                                    <?php if ($user->data()->accessLevel == 1 || $user->data()->accessLevel == 2 || $user->data()->accessLevel == 3) { ?>
                                                        <a href="add.php?&id=2&mentorship_id=<?= $_GET['mentorship_id'] ?>&disease=<?= $_GET['disease'] ?>&log_id=<?= $value['id'] ?>&disease=<?= $value['disease'] ?>" class="btn btn-success">View</a>
                                                    <?php } ?>

                                                    <?php if ($user->data()->accessLevel == 1 || $user->data()->accessLevel == 2) { ?>
                                                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#delete<?= $value['id'] ?>">
                                                            Delete
                                                        </button>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                            <!-- Basic Modal -->
                                            <div class="modal fade" id="delete<?= $value['id'] ?>" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form method="POST">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Delete</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                Are you sure you want to delete this Assessment ?
                                                            </div>
                                                            <div class="modal-footer">
                                                                <input type="hidden" name="id" value="<?= $value['id'] ?>">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                <input type="submit" name="delete_competence" value="Save Changes" class="btn btn-danger btn-block">
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- End Basic Modal-->
                                        <?php
                                            $x++;
                                        }
                                        ?>
                                    </tbody>
                                </table>
                                <!-- End Table with stripped rows -->

                            </div>
                        </div>
                        <div class="card-footer clearfix">
                            <ul class="pagination pagination-sm m-0 float-right">
                                <li class="page-item">
                                    <a class="page-link" href="info.php?id=1&page=<?php if (($_GET['page'] - 1) > 0) {
                                                                                        echo $_GET['page'] - 1;
                                                                                    } else {
                                                                                        echo 1;
                                                                                    } ?>">&laquo;
                                    </a>
                                </li>
                                <?php for ($i = 1; $i <= $pages; $i++) { ?>
                                    <li class="page-item">
                                        <a class="page-link <?php if ($i == $_GET['page']) {
                                                                echo 'active';
                                                            } ?>" href="info.php?id=1&page=<?= $i ?>"><?= $i ?>
                                        </a>
                                    </li>
                                <?php } ?>
                                <li class="page-item">
                                    <a class="page-link" href="info.php?id=1&page=<?php if (($_GET['page'] + 1) <= $pages) {
                                                                                        echo $_GET['page'] + 1;
                                                                                    } else {
                                                                                        echo $i - 1;
                                                                                    } ?>">&raquo;
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                <?php } elseif ($_GET['id'] == 6) { ?>
                    <div class="col-lg-12">
                        <?php if ($user->data()->accessLevel == 1 || $user->data()->accessLevel == 2) { ?>

                            <a href="add.php?disease=<?= $_GET['disease'] ?>" class="btn btn-info">Add New Patient Visit</a>
                        <?php } ?>

                        <?php
                        if ($_GET['disease']) {
                            if ($user->data()->accessLevel == 1 || $user->data()->accessLevel == 3) {
                                $pagNum = 0;
                                $pagNum = $override->countData('logs', 'status', 1, 'disease', $_GET['disease']);

                                $pages = ceil($pagNum / $numRec);
                                if (!$_GET['page'] || $_GET['page'] == 1) {
                                    $page = 0;
                                } else {
                                    $page = ($_GET['page'] * $numRec) - $numRec;
                                }
                                $data = $override->getWithLimit1('logs', 'status', 1, 'disease', $_GET['disease'], $page, $numRec);
                            } else {
                                $pagNum = 0;
                                $pagNum = $override->countData2('logs', 'status', 1, 'disease', $_GET['disease'], 'mentor', $user->data()->id);

                                $pages = ceil($pagNum / $numRec);
                                if (!$_GET['page'] || $_GET['page'] == 1) {
                                    $page = 0;
                                } else {
                                    $page = ($_GET['page'] * $numRec) - $numRec;
                                }
                                $data = $override->getWithLimit3('logs', 'status', 1, 'disease', $_GET['disease'], 'mentor', $user->data()->id, $page, $numRec);
                            }
                        } else {
                            if ($user->data()->accessLevel == 1 || $user->data()->accessLevel == 3) {
                                $pagNum = 0;
                                $pagNum = $override->getCount('logs', 'status', 1);

                                $pages = ceil($pagNum / $numRec);
                                if (!$_GET['page'] || $_GET['page'] == 1) {
                                    $page = 0;
                                } else {
                                    $page = ($_GET['page'] * $numRec) - $numRec;
                                }
                                $data = $override->getWithLimit('logs', 'status', 1, $page, $numRec);
                            } else {
                                $pagNum = 0;
                                $pagNum = $override->getCount('logs', 'status', 1);

                                $pages = ceil($pagNum / $numRec);
                                if (!$_GET['page'] || $_GET['page'] == 1) {
                                    $page = 0;
                                } else {
                                    $page = ($_GET['page'] * $numRec) - $numRec;
                                }
                                $data = $override->getWithLimit('logs', 'status', 1, $page, $numRec);
                            }
                        }

                        ?>
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Table for <?= $override->getNews('category', 'status', 1, 'id', $_GET['disease'])[0]['name'] ?> Comptences</h5>

                                <!-- Table with stripped rows -->
                                <table class="table table-bordered table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Visit Date</th>
                                            <th scope="col">Mentee Name</th>
                                            <th scope="col">Menter Name</th>
                                            <th scope="col">PID</th>
                                            <th scope="col">Site</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $x = 1;
                                        foreach ($data as $value) {
                                            $mentor = $override->getNews('user', 'status', 1, 'id', $value['mentor'])[0];
                                            $mentee = $override->getNews('user', 'status', 1, 'id', $value['mentee'])[0];
                                            $site = $override->getNews('sites', 'status', 1, 'id', $value['site_id'])[0];
                                        ?>
                                            <tr>
                                                <th scope="row"><?= $x; ?></th>
                                                <td class="table-user">
                                                    <?= $value['visit_date']; ?>
                                                </td>
                                                <td class="table-user">
                                                    <?= $mentee['firstname'] . ' - ' . $mentee['lastname']; ?>
                                                </td>
                                                <td class="table-user">
                                                    <?= $mentor['firstname'] . ' - ' . $mentor['lastname']; ?>
                                                </td>
                                                <td class="table-user">
                                                    <?= $value['pids']; ?>
                                                </td>
                                                <td class="table-user">
                                                    <?= $site['name']; ?>
                                                </td>
                                                <td class="text-center">
                                                    <?php if ($user->data()->accessLevel == 1 || $user->data()->accessLevel == 2) { ?>
                                                        <a href="add.php?id=<?= $value['id'] ?>&disease=<?= $value['disease'] ?>" class="btn btn-info">Update</a>
                                                    <?php } ?>
                                                    <?php if ($user->data()->accessLevel == 1 || $user->data()->accessLevel == 2 || $user->data()->accessLevel == 3) { ?>

                                                        <a href="add.php?id=<?= $value['id'] ?>&disease=<?= $value['disease'] ?>" class="btn btn-success">View</a>
                                                    <?php } ?>

                                                    <?php if ($user->data()->accessLevel == 1 || $user->data()->accessLevel == 2) { ?>

                                                        <a href="#delete<?= $value['id'] ?>" role="button" class="btn btn-danger" data-toggle="modal">Delete</a>
                                                    <?php } ?>

                                                </td>
                                            </tr>
                                            </tr>
                                        <?php
                                            $x++;
                                        }
                                        ?>
                                    </tbody>
                                </table>
                                <!-- End Table with stripped rows -->

                            </div>
                        </div>
                        <div class="card-footer clearfix">
                            <ul class="pagination pagination-sm m-0 float-right">
                                <li class="page-item">
                                    <a class="page-link" href="info.php?id=3&page=<?php if (($_GET['page'] - 1) > 0) {
                                                                                        echo $_GET['page'] - 1;
                                                                                    } else {
                                                                                        echo 1;
                                                                                    } ?>">&laquo;
                                    </a>
                                </li>
                                <?php for ($i = 1; $i <= $pages; $i++) { ?>
                                    <li class="page-item">
                                        <a class="page-link <?php if ($i == $_GET['page']) {
                                                                echo 'active';
                                                            } ?>" href="info.php?id=3&page=<?= $i ?>"><?= $i ?>
                                        </a>
                                    </li>
                                <?php } ?>
                                <li class="page-item">
                                    <a class="page-link" href="info.php?id=3&page=<?php if (($_GET['page'] + 1) <= $pages) {
                                                                                        echo $_GET['page'] + 1;
                                                                                    } else {
                                                                                        echo $i - 1;
                                                                                    } ?>">&raquo;
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                <?php } ?>

            </div>
        </section>

    </main><!-- End #main -->

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