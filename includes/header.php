<?php
if (!isset($_SESSION)) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'Job Portal' ?></title>
    <link rel="icon" type="image/svg+xml" href="<?= isset($favicon_path) ? $favicon_path : '../css/favicon.svg' ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= isset($css_path) ? $css_path : '../css/styles.css' ?>">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= isset($home_link) ? $home_link : '../index.php' ?>">
                <img src="<?= isset($logo_path) ? $logo_path : '../css/logo.svg' ?>" alt="JobPortal" class="brand-logo">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'seeker'): ?>
                        <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="../about.php">About</a></li>
                        <li class="nav-item"><a class="nav-link" href="profile.php">Profile</a></li>
                        <li class="nav-item"><a class="nav-link" href="jobs.php">Browse Jobs</a></li>
                        <li class="nav-item"><a class="nav-link" href="applications.php">My Applications</a></li>
                        <li class="nav-item"><a class="nav-link" href="../auth/logout.php">Logout</a></li>
                    <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] == 'employer'): ?>
                        <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="company-profile.php">Company Profile</a></li>
                        <li class="nav-item"><a class="nav-link" href="post-job.php">Post Job</a></li>
                        <li class="nav-item"><a class="nav-link" href="my-jobs.php">My Jobs</a></li>
                        <li class="nav-item"><a class="nav-link" href="applicants.php">Applicants</a></li>
                        <li class="nav-item"><a class="nav-link" href="../auth/logout.php">Logout</a></li>
                    <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                        <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="manage-users.php">Manage Users</a></li>
                        <li class="nav-item"><a class="nav-link" href="manage-jobs.php">Manage Jobs</a></li>
                        <li class="nav-item"><a class="nav-link" href="companies.php">Companies</a></li>
                        <li class="nav-item"><a class="nav-link" href="categories.php">Categories</a></li>
                        <li class="nav-item"><a class="nav-link" href="locations.php">Locations</a></li>
                        <li class="nav-item"><a class="nav-link" href="activity-log.php">Activity Log</a></li>
                        <li class="nav-item"><a class="nav-link" href="../auth/logout.php">Logout</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="../auth/login.php">Login</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
