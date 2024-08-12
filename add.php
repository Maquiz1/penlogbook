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
$numRec = 12;

if ($user->isLoggedIn()) {
    if (Input::exists('post')) {
        if (Input::get('add_user')) {
            $staff = $override->getNews('user', 'status', 1, 'id', $_GET['staff_id']);
            if ($staff) {
                $validate = $validate->check($_POST, array(
                    'firstname' => array(
                        'required' => true,
                    ),
                    'middlename' => array(
                        'required' => true,
                    ),
                    'lastname' => array(
                        'required' => true,
                    ),
                    'position' => array(
                        'required' => true,
                    ),
                    'site_id' => array(
                        'required' => true,
                    ),
                ));
            } else {
                $validate = $validate->check($_POST, array(
                    'firstname' => array(
                        'required' => true,
                    ),
                    'middlename' => array(
                        'required' => true,
                    ),
                    'lastname' => array(
                        'required' => true,
                    ),
                    'position' => array(
                        'required' => true,
                    ),
                    'site_id' => array(
                        'required' => true,
                    ),
                    'username' => array(
                        'required' => true,
                        'unique' => 'user'
                    ),
                    'phone_number' => array(
                        'required' => true,
                        'unique' => 'user'
                    ),
                    'email_address' => array(
                        'unique' => 'user'
                    ),
                ));
            }
            if ($validate->passed()) {
                $salt = $random->get_rand_alphanumeric(32);
                $password = '12345678';
                switch (Input::get('position')) {
                    case 1:
                        $accessLevel = 1;
                        break;
                    case 2:
                        $accessLevel = 1;
                        break;
                    case 3:
                        $accessLevel = 2;
                        break;
                    case 4:
                        $accessLevel = 3;
                        break;
                    case 5:
                        $accessLevel = 3;
                        break;
                    case 6:
                        $accessLevel = 3;
                        break;
                    case 7:
                        $accessLevel = 3;
                        break;
                    case 8:
                        $accessLevel = 3;
                        break;
                }
                try {

                    $staff = $override->getNews('user', 'status', 1, 'id', $_GET['staff_id']);

                    if ($staff) {
                        $user->updateRecord('user', array(
                            'firstname' => Input::get('firstname'),
                            'middlename' => Input::get('middlename'),
                            'lastname' => Input::get('lastname'),
                            'username' => Input::get('username'),
                            'phone_number' => Input::get('phone_number'),
                            'phone_number2' => Input::get('phone_number2'),
                            'email_address' => Input::get('email_address'),
                            'sex' => Input::get('sex'),
                            'position' => Input::get('position'),
                            'accessLevel' => Input::get('accessLevel'),
                            'power' => Input::get('power'),
                            // 'password' => Hash::make($password, $salt),
                            // 'salt' => $salt,
                            'site_id' => Input::get('site_id'),
                        ), $_GET['staff_id']);

                        $successMessage = 'Account Updated Successful';
                    } else {
                        $user->createRecord('user', array(
                            'firstname' => Input::get('firstname'),
                            'middlename' => Input::get('middlename'),
                            'lastname' => Input::get('lastname'),
                            'username' => Input::get('username'),
                            'phone_number' => Input::get('phone_number'),
                            'phone_number2' => Input::get('phone_number2'),
                            'email_address' => Input::get('email_address'),
                            'sex' => Input::get('sex'),
                            'position' => Input::get('position'),
                            'accessLevel' => $accessLevel,
                            'power' => Input::get('power'),
                            'password' => Hash::make($password, $salt),
                            'salt' => $salt,
                            'create_on' => date('Y-m-d'),
                            'last_login' => '',
                            'status' => 1,
                            'user_id' => $user->data()->id,
                            'site_id' => Input::get('site_id'),
                            'count' => 0,
                            'pswd' => 0,
                        ));
                        $successMessage = 'Account Created Successful';
                    }

                    Redirect::to('info.php?id=1&status=1');
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_position')) {
            $validate = $validate->check($_POST, array(
                'name' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    $position = $override->getNews('position', 'status', 1, 'id', $_GET['position_id']);
                    if ($position) {
                        $user->updateRecord('position', array(
                            'name' => Input::get('name'),
                        ), $position[0]['id']);
                        $successMessage = 'Position Successful Updated';
                    } else {
                        $user->createRecord('position', array(
                            'name' => Input::get('name'),
                            'access_level' => 1,
                            'status' => 1,
                        ));
                        $successMessage = 'Position Successful Added';
                    }
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_site')) {
            $validate = $validate->check($_POST, array(
                'name' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    $user->createRecord('site', array(
                        'name' => Input::get('name'),
                    ));
                    $successMessage = 'Site Successful Added';
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_mentorship')) {
            $validate = $validate->check($_POST, array(
                'mentorship_date' => array(
                    'required' => true,
                    // 'unique' => 'mentorships'
                ),
                'site_id' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    $mentorship = $override->getNews('mentorships', 'status', 1, 'id', $_GET['mentorship_id'])[0];
                    if ($mentorship) {
                        $user->updateRecord('mentorships', array(
                            'mentorship_date' => Input::get('mentorship_date'),
                            'disease_id' => $_GET['disease_id'],
                            'notes' => Input::get('notes'),
                            'status' => 1,
                            'update_on' => date('Y-m-d H:i:s'),
                            'update_id' => $user->data()->id,
                            'site_id' => Input::get('site_id'),
                        ), $_GET['mentorship_id']);
                        $successMessage = 'Mentorship Visit Successful Updated';
                    } else {
                        $user->createRecord('mentorships', array(
                            'mentorship_date' => Input::get('mentorship_date'),
                            'disease_id' => $_GET['disease_id'],
                            'notes' => Input::get('notes'),
                            'status' => 1,
                            'create_on' => date('Y-m-d H:i:s'),
                            'create_id' => $user->data()->id,
                            'update_on' => date('Y-m-d H:i:s'),
                            'update_id' => $user->data()->id,
                            'site_id' => Input::get('site_id'),
                        ));
                        $successMessage = 'Mentorship Visit Successful Added';
                    }
                    Redirect::to('info.php?id=' . $_GET['id'] . '&create_id=' . $user->data()->id . '&disease_id=' . $_GET['disease_id'] . '&msg=' . $successMessage);
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_logs')) {
            $validate = $validate->check($_POST, array(
                // 'pids' => array(
                //     'required' => true,
                // ),
                // 'mentee' => array(
                //     'required' => true,
                // ),
                // 'mentor' => array(
                //     'required' => true,
                // ),
            ));
            if ($validate->passed()) {
                // print_r($_POST);
                try {
                    $mentorship = $override->getNews('mentorship', 'status', 1, 'id', $_GET['mentorship_id'])[0];
                    $log = $override->getNews('logs', 'status', 1, 'id', $_GET['log_id'])[0];
                    $competencies = implode(',', Input::get('competencies'));
                    $date = implode(',', Input::get('date'));
                    $score = implode(',', Input::get('score'));
                    $competencies2 = implode(',', Input::get('competencies2'));
                    $date2 = implode(',', Input::get('date2'));
                    $score2 = implode(',', Input::get('score2'));
                    $competencies3 = implode(',', Input::get('competencies3'));
                    $date3 = implode(',', Input::get('date3'));
                    $score3 = implode(',', Input::get('score3'));
                    if ($log) {
                        // foreach (Input::get('competencies') as $value) {
                        $user->updateRecord('logs', array(
                            'disease' => $_GET['disease'],
                            'mentorship_id' => $_GET['mentorship_id'],
                            'competencies' => $competencies,
                            'competencies2' => $competencies2,
                            'competencies3' => $competencies3,
                            'cases' => 1,
                            'pids' => Input::get('pids'),
                            'mentee' => Input::get('mentee'),
                            'mentor' => Input::get('mentor'),
                            'notes' => Input::get('notes'),
                            'site_id' => $mentorship['site_id'],
                            'status' => 1,
                            'update_on' => date('Y-m-d H:i:s'),
                            'update_id' => $user->data()->id,
                        ), $_GET['log_id']);
                        // }

                        // foreach (Input::get('date') as $value) {
                        //     $user->updateRecord(
                        //         'logs',
                        //         array(
                        //             'date'  => $value,
                        //         ),
                        //         $_GET['log_id']
                        //     );
                        // }

                        // foreach (Input::get('date2') as $value) {
                        //     $user->updateRecord(
                        //         'logs2',
                        //         array(
                        //             'date'  => $value,
                        //         ),
                        //         $_GET['log_id']
                        //     );
                        // }

                        // foreach (Input::get('date3') as $value) {
                        //     $user->updateRecord(
                        //         'logs',
                        //         array(
                        //             'date3'  => $value,
                        //         ),
                        //         $_GET['log_id']
                        //     );
                        // }

                        // foreach (Input::get('score') as $value) {
                        //     $user->updateRecord(
                        //         'logs',
                        //         array(
                        //             'score'  => $value,
                        //         ),
                        //         $_GET['log_id']
                        //     );
                        // }

                        // foreach (Input::get('score2') as $value) {
                        //     $user->updateRecord(
                        //         'logs',
                        //         array(
                        //             'score2'  => $value,
                        //         ),
                        //         $_GET['log_id']
                        //     );
                        // }

                        // foreach (Input::get('score3') as $value) {
                        //     $user->updateRecord(
                        //         'logs',
                        //         array(
                        //             'score3'  => $value,
                        //         ),
                        //         $_GET['log_id']
                        //     );
                        // }


                        $successMessage = 'Logbook Assessments Successful Updated';
                    } else {
                        // foreach (Input::get('competencies') as $value) {
                        $user->createRecord('logs', array(
                            'disease' => $_GET['disease'],
                            'mentorship_id' => $_GET['mentorship_id'],
                            'competencies' => $competencies,
                            'competencies2' => $competencies2,
                            'competencies3' => $competencies3,
                            'cases' => 1,
                            'pids' => Input::get('pids'),
                            'mentee' => Input::get('mentee'),
                            'mentor' => Input::get('mentor'),
                            'notes' => Input::get('notes'),
                            'site_id' => $mentorship['site_id'],
                            'status' => 1,
                            'create_on' => date('Y-m-d H:i:s'),
                            'staff_id' => $user->data()->id,
                            'update_on' => date('Y-m-d H:i:s'),
                            'update_id' => $user->data()->id,
                        ));
                        // }

                        // foreach (Input::get('date') as $value) {
                        //     $user->createRecord(
                        //         'logs',
                        //         array(
                        //             'date'  => $value,
                        //         )
                        //     );
                        // }

                        // foreach (Input::get('date2') as $value) {
                        //     $user->createRecord(
                        //         'logs',
                        //         array(
                        //             'date2'  => $value,
                        //         )
                        //     );
                        // }

                        // foreach (Input::get('date3') as $value) {
                        //     $user->createRecord(
                        //         'logs',
                        //         array(
                        //             'date3'  => $value,
                        //         )
                        //     );
                        // }

                        // foreach (Input::get('score') as $value) {
                        //     $user->createRecord(
                        //         'logs',
                        //         array(
                        //             'score'  => $value,
                        //         )
                        //     );
                        // }

                        // foreach (Input::get('score2') as $value) {
                        //     $user->createRecord(
                        //         'logs',
                        //         array(
                        //             'score2'  => $value,
                        //         )
                        //     );
                        // }

                        // foreach (Input::get('score3') as $value) {
                        //     $user->createRecord(
                        //         'logs',
                        //         array(
                        //             'score3'  => $value,
                        //         )
                        //     );
                        // }
                        $successMessage = 'Logbook Assessments Successful Added';
                    }
                    Redirect::to('info.php?id=' . $_GET['id'] . '&mentorship_id=' . $_GET['mentorship_id']  . '&disease=' . $_GET['disease'] . '&msg=' . $successMessage);
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_visit')) {
            $validate = $validate->check($_POST, array(
                'name' => array(
                    'required' => true,
                ),
                'code' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    $user->createRecord('schedule', array(
                        'name' => Input::get('name'),
                        'code' => Input::get('code'),
                    ));
                    $successMessage = 'Schedule Successful Added';
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_region')) {
            $validate = $validate->check($_POST, array(
                'name' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    $regions = $override->get('regions', 'id', $_GET['region_id']);
                    if ($regions) {
                        $user->updateRecord('regions', array(
                            'name' => Input::get('name'),
                        ), $_GET['region_id']);
                        $successMessage = 'Region Successful Updated';
                    } else {
                        $user->createRecord('regions', array(
                            'name' => Input::get('name'),
                            'status' => 1,
                        ));
                        $successMessage = 'Region Successful Added';
                    }
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_district')) {
            $validate = $validate->check($_POST, array(
                'region_id' => array(
                    'required' => true,
                ),
                'name' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    $districts = $override->get('districts', 'id', $_GET['district_id']);
                    if ($districts) {
                        $user->updateRecord('districts', array(
                            'region_id' => $_GET['region_id'],
                            'name' => Input::get('name'),
                        ), $_GET['district_id']);
                        $successMessage = 'District Successful Updated';
                    } else {
                        $user->createRecord('districts', array(
                            'region_id' => Input::get('region_id'),
                            'name' => Input::get('name'),
                            'status' => 1,
                        ));
                        $successMessage = 'District Successful Added';
                    }
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_ward')) {
            $validate = $validate->check($_POST, array(
                'region_id' => array(
                    'required' => true,
                ),
                'district_id' => array(
                    'required' => true,
                ),
                'name' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    $wards = $override->get('wards', 'id', $_GET['ward_id']);
                    if ($wards) {
                        $user->updateRecord('wards', array(
                            'region_id' => $_GET['region_id'],
                            'district_id' => $_GET['district_id'],
                            'name' => Input::get('name'),
                        ), $_GET['ward_id']);
                        $successMessage = 'Ward Successful Updated';
                    } else {
                        $user->createRecord('wards', array(
                            'region_id' => Input::get('region_id'),
                            'district_id' => Input::get('district_id'),
                            'name' => Input::get('name'),
                            'status' => 1,
                        ));
                        $successMessage = 'Ward Successful Added';
                    }
                } catch (Exception $e) {
                    die($e->getMessage());
                }
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

        <?php if ($_GET['id'] == 1) { ?>
            <?php
            $logs = $override->getNews('mentorships', 'status', 1, 'id', $_GET['mentorship_id'])[0];
            $mentee = $override->getNews('user', 'status', 1, 'id', $logs['mentee'])[0];
            $mentor = $override->getNews('user', 'status', 1, 'id', $logs['mentor'])[0];
            $site = $override->getNews('sites', 'status', 1, 'id', $logs['site_id'])[0];

            ?>
            <div class="pagetitle">
                <h1>Form Elements</h1>
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                        <li class="breadcrumb-item">Forms</li>
                        <li class="breadcrumb-item active">Elements</li>
                    </ol>
                </nav>
            </div><!-- End Page Title -->

            <section class="section">
                <div class="row">
                    <div class="col-lg-12">

                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">General Form Elements</h5>

                                <!-- General Form Elements -->
                                <form method="post">
                                    <div class="row mb-3">
                                        <label for="inputText" class="col-sm-2 col-form-label">Mentorship Date</label>
                                        <div class="col-sm-10">
                                            <input type="date" name="mentorship_date" id="mentorship_date" class="form-control" value="<?= $logs['mentorship_date']; ?>" placeholder="Mentorship Date" required>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label class="col-sm-2 col-form-label">Site</label>
                                        <div class="col-sm-10">
                                            <select class="form-select" name="site_id" id="site_id" aria-label="Default select example">
                                                <option value="<?= $site['id'] ?>">
                                                    <?php if ($logs['site_id']) {
                                                        print_r($site['name']);
                                                    } else {
                                                        echo 'Select';
                                                    } ?>
                                                </option>
                                                <?php foreach ($override->get('sites', 'status', 1) as $value) { ?>
                                                    <option value="<?= $value['id']; ?>"><?= $value['name']; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="inputPassword" class="col-sm-2 col-form-label">Notes</label>
                                        <div class="col-sm-10">
                                            <textarea class="form-control" name="notes" style="height: 100px" placeholder="Enter Notes Here" value="<?= $logs['notes']; ?>">
                                            <?= $logs['notes']; ?>
                                        </textarea>
                                        </div>
                                    </div>


                                    <div class="row mb-3">
                                        <div class="col-sm-10">
                                            <?php if ($user->data()->accessLevel == 1 || $user->data()->accessLevel == 2) { ?>
                                                <input type="submit" value="Submit" name="add_mentorship" class="btn btn-primary btn-block">
                                            <?php } ?>
                                        </div>
                                    </div>

                                </form>
                                <!-- End General Form Elements -->

                            </div>
                        </div>

                    </div>
                </div>
            </section>

        <?php } elseif ($_GET['id'] == 2) { ?>
            <?php
            // print_r($_POST);

            $logs = $override->getNews('logs', 'status', 1, 'id', $_GET['log_id'])[0];
            $mentor = $override->getNews1('user', 'status', 1, 'type', 3, 'id', $logs['mentor'])[0];
            $mentee = $override->getNews1('user', 'status', 1, 'type', 4, 'id', $logs['mentee'])[0];
            $site = $override->getNews('sites', 'status', 1, 'id', $logs['site_id'])[0];


            if ($_GET['disease'] == 1) {
                $competencies = 'diabetes';
            } elseif ($_GET['disease'] == 2) {
                $competencies = 'cardiac';
            } elseif ($_GET['disease'] == 3) {
                $competencies = 'sickle_cell';
            } elseif ($_GET['disease'] == 4) {
                $competencies = 'respiratory';
            } elseif ($_GET['disease'] == 5) {
                $competencies = 'hypertension';
            } elseif ($_GET['disease'] == 6) {
                $competencies = 'epilepsy';
            } elseif ($_GET['disease'] == 7) {
                $competencies = 'liver';
            } elseif ($_GET['disease'] == 8) {
                $competencies = 'kidney';
            }
            ?>

            <div class="pagetitle">
                <h1><?= $competencies ?> Form</h1>
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                        <li class="breadcrumb-item"><?= $competencies ?> Forms</li>
                        <li class="breadcrumb-item active">Elements</li>
                    </ol>
                </nav>
            </div><!-- End Page Title -->

            <section class="section">
                <div class="row">
                    <div class="col-lg-12">

                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><?= $competencies ?> Form</h5>

                                <!-- General Form Elements -->
                                <!-- <form method="post"> -->
                                <form method="post" class="row g-3">
                                    <!-- <div class="row"> -->

                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title">Competencies Table for <?= $competencies ?></h5>

                                            <!-- Default Table -->
                                            <table class="table table-bordered table-hover table-striped">
                                                <thead>
                                                    <tr>
                                                        <th scope="col">#</th>
                                                        <th scope="col">Competencies</th>
                                                        <th scope="col">Episode 1 </th>
                                                        <th scope="col">Episode 2 </th>
                                                        <th scope="col">Episode 3 </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php

                                                    $x = 1;
                                                    foreach ($override->get($competencies, 'status', 1) as $value) { ?>
                                                        <tr>

                                                            <th scope="row"><?= $x; ?></th>
                                                            <td>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox" name="competencies[]" id="competencies<?= $value['id']; ?>" value="<?= $value['id']; ?>" <?php foreach (explode(',', $logs['competencies']) as $competency) {
                                                                                                                                                                                                                    if ($competency == $value['id']) {
                                                                                                                                                                                                                        echo 'checked';
                                                                                                                                                                                                                    }
                                                                                                                                                                                                                } ?>>

                                                                    <label class="form-check-label" for="gridCheck1">
                                                                        <?= $value['name']; ?> </label>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <label for="inputPassword5" class="form-label">PID</label>

                                                                        <input type="text" name="pids[]" id="pids" class="form-control" value="<?= $logs['pids']; ?>" placeholder=" ">
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <label for="inputPassword5" class="form-label">Score</label>
                                                                        <input type="number" name="score[]" id="score" class="form-control" value="<?= $logs['score']; ?>" placeholder="">
                                                                    </div>
                                                                </div>

                                                            </td>

                                                            <td>
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <label for="inputPassword5" class="form-label">PID</label>

                                                                        <input type="text" name="pids[]" id="pids" class="form-control" value="<?= $logs['pids']; ?>" placeholder=" ">
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <label for="inputPassword5" class="form-label">Score</label>
                                                                        <input type="number" name="score[]" id="score" class="form-control" value="<?= $logs['score']; ?>" placeholder="">
                                                                    </div>
                                                                </div>
                                                            </td>


                                                            <td>
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <label for="inputPassword5" class="form-label">PID</label>

                                                                        <input type="text" name="pids[]" id="pids" class="form-control" value="<?= $logs['pids']; ?>" placeholder=" ">
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <label for="inputPassword5" class="form-label">Score</label>
                                                                        <input type="number" name="score[]" id="score" class="form-control" value="<?= $logs['score']; ?>" placeholder="">
                                                                    </div>
                                                                </div>
                                                            </td>


                                                        </tr>

                                                    <?php $x++;
                                                    } ?>
                                                </tbody>
                                            </table>
                                            <!-- End Default Table Example -->
                                        </div>
                                    </div>


                                    <div class="row mb-3">

                                        <div class="col-md-6">
                                            <label for="inputState" class="form-label">Mentee Name</label>
                                            <select class="form-select" name="mentee" id="mentee" aria-label="Default select example">
                                                <option value="<?= $mentee['id'] ?>">
                                                    <?php if ($logs['mentee']) {
                                                        print_r($mentee['firstname'] . ' ' . $mentee['lastname']);
                                                    } else {
                                                        echo 'Select';
                                                    } ?>
                                                </option>
                                                <?php foreach ($override->getNews('user', 'status', 1, 'type', 4) as $value) { ?>
                                                    <option value="<?= $value['id']; ?>"><?= $value['firstname'] . ' ' . $value['lastname']; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="inputState" class="form-label">Mentor Name</label>
                                            <select class="form-select" name="mentor" id="mentor" aria-label="Default select example">
                                                <option value="<?= $mentor['id'] ?>">
                                                    <?php if ($logs['mentor']) {
                                                        print_r($mentor['firstname'] . ' ' . $mentee['lastname']);
                                                    } else {
                                                        echo 'Select';
                                                    } ?>
                                                </option>
                                                <?php foreach ($override->getNews('user', 'status', 1, 'type', 3) as $value) { ?>
                                                    <option value="<?= $value['id']; ?>"><?= $value['firstname'] . ' ' . $value['lastname']; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>


                                    <div class="row mb-3">
                                        <label for="inputPassword" class="col-sm-2 col-form-label">Notes</label>
                                        <div class="col-sm-10">
                                            <textarea class="form-control" name="notes" style="height: 100px" placeholder="Enter Notes Here" value="<?= $logs['notes']; ?>" required>
                                            <?= $logs['notes']; ?>
                                        </textarea>
                                        </div>
                                    </div>


                                    <div class="row mb-3">
                                        <div class="col-sm-10">
                                            <?php if ($user->data()->accessLevel == 1 || $user->data()->accessLevel == 2) { ?>
                                                <input type="submit" value="Submit" name="add_logs" class="btn btn-primary btn-block">
                                            <?php } ?>
                                        </div>
                                    </div>

                                </form>
                                <!-- End General Form Elements -->

                            </div>
                        </div>

                    </div>
                </div>
            </section>
        <?php } elseif ($_GET['id'] == 3) { ?>
        <?php } ?>


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