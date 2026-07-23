<?php
require("../config/conn.php");
session_start();
if ($_SESSION['role'] != "admin") {
    header("Location: ../auth/login.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="icon" type="image/svg+xml" href="../css/favicon.svg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
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
                    <li class="nav-item"><a class="nav-link" href="manage-users.php">Manage Users</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage-jobs.php">Manage Jobs</a></li>
                    <li class="nav-item"><a class="nav-link" href="companies.php">Companies</a></li>
                    <li class="nav-item"><a class="nav-link" href="categories.php">Categories</a></li>
                    <li class="nav-item"><a class="nav-link " href="skills.php">Skills</a></li>
                    <li class="nav-item"><a class="nav-link" href="activity-log.php">Activity Log</a></li>
                    <li class="nav-item"><a class="nav-link" href="../auth/logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid py-5">
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="mb-0">Admin Dashboard</h1>
                <p class="text-muted">Manage your job portal platform</p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="stat-card">
                    <div class="stat-card-header bg-primary">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-card-body">
                        <h5 class="stat-label">Total Users</h5>
                        <p class="stat-value"><?php echo mysqli_fetch_row(mysqli_query($conc, "SELECT COUNT(*) FROM users"))[0]; ?></p>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3 mb-4">
                <div class="stat-card">
                    <div class="stat-card-header bg-success">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    <div class="stat-card-body">
                        <h5 class="stat-label">Total Jobs</h5>
                        <p class="stat-value"><?php echo mysqli_fetch_row(mysqli_query($conc, "SELECT COUNT(*) FROM jobs"))[0]; ?></p>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3 mb-4">
                <div class="stat-card">
                    <div class="stat-card-header bg-info">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="stat-card-body">
                        <h5 class="stat-label">Total Applications</h5>
                        <p class="stat-value"><?php echo mysqli_fetch_row(mysqli_query($conc, "SELECT COUNT(*) FROM applications"))[0]; ?></p>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3 mb-4">
                <div class="stat-card">
                    <div class="stat-card-header bg-warning">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-card-body">
                        <h5 class="stat-label">Active Jobs</h5>
                        <p class="stat-value"><?php echo mysqli_fetch_row(mysqli_query($conc, "SELECT COUNT(*) FROM jobs WHERE DATE(expiry_date) >= CURDATE()"))[0]; ?></p>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3 mb-4">
                <div class="stat-card info">
                    <div class="stat-card-header bg-info">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="stat-card-body">
                        <h5 class="stat-label">Companies</h5>
                        <p class="stat-value"><?php echo mysqli_fetch_row(mysqli_query($conc, "SELECT COUNT(*) FROM companies"))[0]; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-12">
                <h3 class="mb-4">Management Tools</h3>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 col-lg-4 mb-4">
                <a href="manage-users.php" class="text-decoration-none">
                    <div class="card h-100 action-card">
                        <div class="card-body text-center">
                            <i class="fas fa-users fa-3x text-primary mb-3"></i>
                            <h5 class="card-title">Manage Users</h5>
                            <p class="card-text text-muted">View and manage all registered users</p>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-6 col-lg-4 mb-4">
                <a href="manage-jobs.php" class="text-decoration-none">
                    <div class="card h-100 action-card">
                        <div class="card-body text-center">
                            <i class="fas fa-briefcase fa-3x text-success mb-3"></i>
                            <h5 class="card-title">Manage Jobs</h5>
                            <p class="card-text text-muted">View and manage all job postings</p>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-6 col-lg-4 mb-4">
                <a href="activity-log.php" class="text-decoration-none">
                    <div class="card h-100 action-card">
                        <div class="card-body text-center">
                            <i class="fas fa-history fa-3x text-info mb-3"></i>
                            <h5 class="card-title">Activity Log</h5>
                            <p class="card-text text-muted">View system activity and logs</p>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-6 col-lg-4 mb-4">
                <a href="companies.php" class="text-decoration-none">
                    <div class="card h-100 action-card">
                        <div class="card-body text-center">
                            <i class="fas fa-building fa-3x text-info mb-3"></i>
                            <h5 class="card-title">Companies</h5>
                            <p class="card-text text-muted">View company profiles and employer details</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container text-center">
            <p>&copy; 2026 Job Portal. All rights reserved by Chetan Pawar.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
