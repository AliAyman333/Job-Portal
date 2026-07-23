<?php
require("../config/conn.php");
session_start();
if ($_SESSION['role'] != "seeker") {
    header("Location: ../auth/login.php");
}
$seeker_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Seeker Dashboard</title>
    <link rel="icon" type="image/svg+xml" href="../css/favicon.svg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
                    <li class="nav-item"><a class="nav-link" href="../about.php">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="profile.php">Profile</a></li>
                    <li class="nav-item"><a class="nav-link" href="jobs.php">Browse Jobs</a></li>
                    <li class="nav-item"><a class="nav-link" href="applications.php">My Applications</a></li>
                    <li class="nav-item"><a class="nav-link" href="../auth/logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container py-5">
        <h2 class="mb-4">Welcome, Job Seeker!</h2>

        <?php
        $totalJobs = mysqli_fetch_row(mysqli_query($conc, "SELECT COUNT(*) FROM jobs WHERE status='Open'"))[0];
        $applied = mysqli_fetch_row(mysqli_query($conc, "SELECT COUNT(*) FROM applications WHERE seeker_id='$seeker_id'"))[0];
        ?>

        <!-- Stats -->
        <div class="row mb-5">
            <div class="col-md-6">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="stat-icon">📋</div>
                        <h5 class="card-title">Available Jobs</h5>
                        <div class="stat-value"><?= $totalJobs ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card stat-card success">
                    <div class="card-body">
                        <div class="stat-icon">✅</div>
                        <h5 class="card-title">Applications Submitted</h5>
                        <div class="stat-value"><?= $applied ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search History -->
        <?php
        $historyQ = mysqli_query($conc, "SELECT search_term, created_at FROM search_history 
            WHERE seeker_id='$seeker_id' ORDER BY created_at DESC LIMIT 3");
        if (mysqli_num_rows($historyQ) > 0) {
        ?>
        <div class="row mb-5">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-history"></i> آخر عمليات البحث</h5>
                        <ul class="list-group list-group-flush">
                            <?php while ($h = mysqli_fetch_assoc($historyQ)) { ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <a href="jobs.php?search=<?= urlencode($h['search_term']) ?>">
                                        <?= htmlspecialchars($h['search_term']) ?>
                                    </a>
                                    <small class="text-muted"><?= date('Y-m-d H:i', strtotime($h['created_at'])) ?></small>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>

        <!-- Quick Links -->
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">View Profile</h5>
                        <p class="card-text">Manage your profile information</p>
                        <a href="profile.php" class="btn btn-primary">Go to Profile</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Browse Jobs</h5>
                        <p class="card-text">Explore available job opportunities</p>
                        <a href="jobs.php" class="btn btn-primary">Browse Jobs</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">My Applications</h5>
                        <p class="card-text">Track your job applications</p>
                        <a href="applications.php" class="btn btn-primary">View Applications</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>