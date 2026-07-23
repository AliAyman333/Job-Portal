<?php
require("../config/conn.php");
session_start();
if ($_SESSION['role'] != "admin") {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = intval($_GET['id'] ?? 0);
$stmt = mysqli_prepare($conc, "SELECT * FROM users WHERE id=? AND role!='admin'");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$user) {
    echo "<script>alert('User not found');window.location='manage-users.php';</script>";
    exit();
}

$company = null;
$seeker = null;
if ($user['role'] === 'employer') {
    $stmt = mysqli_prepare($conc, "SELECT * FROM companies WHERE user_id=?");
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $company = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
} elseif ($user['role'] === 'seeker') {
    $stmt = mysqli_prepare($conc, "SELECT * FROM seekers WHERE user_id=?");
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $seeker = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Details</title>
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
                    <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
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


    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="mb-1">User Details</h1>
                <p class="text-muted mb-0">Complete profile and activity summary</p>
            </div>
            <a href="manage-users.php" class="btn btn-outline-primary">Back to Users</a>
        </div>

        <div class="row">
            <div class="col-lg-5 mb-4">
                <div class="card">
                    <div class="card-header"><strong>Account</strong></div>
                    <div class="card-body">
                        <p><strong>Name:</strong> <?= htmlspecialchars($user['name']) ?></p>
                        <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
                        <p><strong>Role:</strong> <?= htmlspecialchars(ucfirst($user['role'])) ?></p>
                        <p><strong>Status:</strong> <?= $user['status'] ? 'Active' : 'Blocked' ?></p>
                    </div>
                </div>
            </div>

            <div class="col-lg-7 mb-4">
                <div class="card">
                    <div class="card-header"><strong><?= $user['role'] === 'employer' ? 'Company Details' : 'Seeker Profile' ?></strong></div>
                    <div class="card-body">
                        <?php if ($user['role'] === 'employer'): ?>
                            <?php if ($company): ?>
                                <p><strong>Company:</strong> <?= htmlspecialchars($company['company_name']) ?></p>
                                <p><strong>Location:</strong> <?= htmlspecialchars($company['location']) ?></p>
                                <p><strong>Description:</strong><br><?= nl2br(htmlspecialchars($company['description'])) ?></p>
                            <?php else: ?>
                                <p class="text-muted mb-0">No company profile found.</p>
                            <?php endif; ?>
                        <?php else: ?>
                            <?php if ($seeker): ?>
                                <p><strong>Skills:</strong> <?= htmlspecialchars($seeker['skills']) ?></p>
                                <p><strong>Education:</strong> <?= htmlspecialchars($seeker['education']) ?></p>
                                <p><strong>Experience:</strong> <?= htmlspecialchars($seeker['years_of_experience']) ?></p>
                                <p><strong>Resume:</strong>
                                    <?php if (!empty($seeker['resume'])): ?>
                                        <a href="../uploads/resumes/<?= htmlspecialchars($seeker['resume']) ?>" target="_blank">View Resume</a>
                                    <?php else: ?>
                                        Not uploaded
                                    <?php endif; ?>
                                </p>
                            <?php else: ?>
                                <p class="text-muted mb-0">No seeker profile found.</p>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($user['role'] === 'employer'): ?>
            <div class="card mb-4">
                <div class="card-header"><strong>Posted Jobs</strong></div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead><tr><th>Title</th><th>Location</th><th>Status</th><th>Action</th></tr></thead>
                        <tbody>
                            <?php
                            $stmt = mysqli_prepare($conc, "SELECT * FROM jobs WHERE employer_id=? ORDER BY id DESC");
                            mysqli_stmt_bind_param($stmt, "i", $user_id);
                            mysqli_stmt_execute($stmt);
                            $jobs = mysqli_stmt_get_result($stmt);
                            if (mysqli_num_rows($jobs) > 0) {
                                while ($job = mysqli_fetch_assoc($jobs)) {
                                    echo "<tr><td>" . htmlspecialchars($job['title']) . "</td><td>" . htmlspecialchars($job['location']) . "</td><td>" . htmlspecialchars($job['status']) . "</td><td><a class='btn btn-sm btn-primary' href='job-details.php?id=" . $job['id'] . "'>View Details</a></td></tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4' class='text-center text-muted'>No jobs posted.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php else: ?>
            <div class="card mb-4">
                <div class="card-header"><strong>Applications</strong></div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead><tr><th>Job</th><th>Status</th><th>Applied At</th><th>Action</th></tr></thead>
                        <tbody>
                            <?php
                            $stmt = mysqli_prepare($conc, "SELECT a.*, j.title FROM applications a JOIN jobs j ON a.job_id=j.id WHERE a.seeker_id=? ORDER BY a.applied_at DESC");
                            mysqli_stmt_bind_param($stmt, "i", $user_id);
                            mysqli_stmt_execute($stmt);
                            $apps = mysqli_stmt_get_result($stmt);
                            if (mysqli_num_rows($apps) > 0) {
                                while ($app = mysqli_fetch_assoc($apps)) {
                                    echo "<tr><td>" . htmlspecialchars($app['title']) . "</td><td>" . htmlspecialchars($app['status']) . "</td><td>" . htmlspecialchars($app['applied_at']) . "</td><td><a class='btn btn-sm btn-primary' href='job-details.php?id=" . $app['job_id'] . "'>View Job</a></td></tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4' class='text-center text-muted'>No applications found.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container text-center"><p>&copy; 2026 Job Portal. All rights reserved by Chetan Pawar.</p></div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
