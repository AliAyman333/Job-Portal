<?php
require("../config/conn.php");
require("../includes/matching_helper.php");
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != "seeker") {
    header("Location: ../auth/login.php");
    exit();
}
$seeker_id = $_SESSION['user_id'];

$stmt = mysqli_prepare($conc, "SELECT * FROM seekers WHERE user_id=?");
mysqli_stmt_bind_param($stmt, "i", $seeker_id);
mysqli_stmt_execute($stmt);
$seeker = mysqli_stmt_get_result($stmt)->fetch_assoc() ?: [];

$jobs_result = mysqli_query($conc, "SELECT * FROM jobs WHERE status='Open' ORDER BY expiry_date ASC");
$jobs = [];
while ($j = mysqli_fetch_assoc($jobs_result)) {
    $j['match'] = calculateMatchScore($seeker, $j);
    $jobs[] = $j;
}

// ترتيب الوظائف حسب الأنسب للباحث أولاً
usort($jobs, fn($a, $b) => $b['match']['total'] <=> $a['match']['total']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Browse Jobs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
                    <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="../about.php">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="profile.php">Profile</a></li>
                    <li class="nav-item"><a class="nav-link" href="jobs.php">Browse Jobs</a></li>
                    <li class="nav-item"><a class="nav-link" href="applications.php">My Applications</a></li>
                    <li class="nav-item"><a class="nav-link" href="my-interviews.php">My Interviews</a></li>
                    <li class="nav-item"><a class="nav-link" href="../auth/logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

<div class="container py-5">
    <h2 class="mb-4">Browse Jobs</h2>
    <div class="row g-3">
        <?php foreach ($jobs as $job): ?>
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <h5 class="card-title"><?= htmlspecialchars($job['title']) ?></h5>
                            <span class="badge <?= matchScoreBadgeClass($job['match']['total']) ?>">
                                <?= $job['match']['total'] ?>% Match
                            </span>
                        </div>
                        <p class="card-text text-muted mb-1"><?= htmlspecialchars($job['location']) ?> · <?= htmlspecialchars($job['job_type']) ?></p>
                        <p class="card-text"><?= htmlspecialchars(mb_strimwidth($job['description'], 0, 120, '...')) ?></p>
                        <a href="job-details.php?job=<?= $job['id'] ?>" class="btn btn-sm btn-primary">View / Apply</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
</body>
</html>