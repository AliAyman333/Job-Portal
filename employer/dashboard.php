<?php
require("../config/conn.php");
session_start();
if ($_SESSION['role'] != "employer") {
    header("Location: ../auth/login.php");
}
$emp_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employer Dashboard</title>
    <link rel="icon" type="image/svg+xml" href="../css/favicon.svg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="../index.php">
                <img src="../css/logo.svg" alt="JobPortal" class="brand-logo">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="company-profile.php">Company Profile</a></li>
                    <li class="nav-item"><a class="nav-link" href="post-job.php">Post Job</a></li>
                    <li class="nav-item"><a class="nav-link" href="my-jobs.php">My Jobs</a></li>
                    <li class="nav-item"><a class="nav-link" href="applicants.php">Applicants</a></li>
                    <li class="nav-item"><a class="nav-link" href="../auth/logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container py-5">
        <h2 class="mb-4">Welcome, Employer!</h2>

        <?php
        $totalJobs = mysqli_fetch_row(mysqli_query($conc, "SELECT COUNT(*) FROM jobs WHERE employer_id='$emp_id'"))[0];
        $totalApplicants = mysqli_fetch_row(mysqli_query($conc, "SELECT COUNT(*) FROM applications a JOIN jobs j ON a.job_id=j.id WHERE j.employer_id='$emp_id'"))[0];
        ?>

        <!-- Stats -->
        <div class="row mb-5">
            <div class="col-md-6">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="stat-icon">📢</div>
                        <h5 class="card-title">Total Jobs Posted</h5>
                        <div class="stat-value"><?= $totalJobs ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card stat-card info">
                    <div class="card-body">
                        <div class="stat-icon">👥</div>
                        <h5 class="card-title">Total Applicants</h5>
                        <div class="stat-value"><?= $totalApplicants ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Company Profile</h5>
                        <p class="card-text">Manage your company information</p>
                        <a href="company-profile.php" class="btn btn-primary">Edit Profile</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Post New Job</h5>
                        <p class="card-text">Create and post a new job listing</p>
                        <a href="post-job.php" class="btn btn-success">Post Job</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">View Applicants</h5>
                        <p class="card-text">Review all job applications</p>
                        <a href="applicants.php" class="btn btn-info text-white">View Applicants</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Manage Jobs</h5>
                        <p class="card-text">Edit and manage your job postings</p>
                        <a href="my-jobs.php" class="btn btn-primary">My Jobs</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
