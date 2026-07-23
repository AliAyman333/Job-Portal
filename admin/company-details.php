<?php
require("../config/conn.php");
session_start();
if ($_SESSION['role'] != "admin") {
    header("Location: ../auth/login.php");
    exit();
}

$company_id = intval($_GET['id'] ?? 0);
$stmt = mysqli_prepare($conc, "SELECT c.*, u.name AS employer_name, u.email, u.status FROM companies c JOIN users u ON c.user_id=u.id WHERE c.id=?");
mysqli_stmt_bind_param($stmt, "i", $company_id);
mysqli_stmt_execute($stmt);
$company = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$company) {
    echo "<script>alert('Company not found');window.location='companies.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Details</title>
    <link rel="icon" type="image/svg+xml" href="../css/favicon.svg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="../index.php"><img src="../css/logo.svg" alt="JobPortal" class="brand-logo"></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button>
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
                <h1 class="mb-1"><?= htmlspecialchars($company['company_name']) ?></h1>
                <p class="text-muted mb-0">Company profile, owner, jobs, and applicants</p>
            </div>
            <a href="companies.php" class="btn btn-outline-primary">Back to Companies</a>
        </div>

        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header"><strong>Company</strong></div>
                    <div class="card-body">
                        <p><strong>Name:</strong> <?= htmlspecialchars($company['company_name']) ?></p>
                        <p><strong>Location:</strong> <?= htmlspecialchars($company['location']) ?></p>
                        <p><strong>Description:</strong><br><?= nl2br(htmlspecialchars($company['description'])) ?></p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header"><strong>Owner</strong></div>
                    <div class="card-body">
                        <p><strong>Name:</strong> <?= htmlspecialchars($company['employer_name']) ?></p>
                        <p><strong>Email:</strong> <?= htmlspecialchars($company['email']) ?></p>
                        <p><strong>Status:</strong> <?= $company['status'] ? 'Active' : 'Blocked' ?></p>
                        <a href="user-details.php?id=<?= $company['user_id'] ?>" class="btn btn-primary btn-sm">View User</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><strong>Company Jobs</strong></div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead><tr><th>Job</th><th>Location</th><th>Status</th><th>Applications</th><th>Action</th></tr></thead>
                    <tbody>
                        <?php
                        $stmt = mysqli_prepare($conc, "SELECT j.*, COUNT(a.id) AS app_count FROM jobs j LEFT JOIN applications a ON j.id=a.job_id WHERE j.employer_id=? GROUP BY j.id ORDER BY j.id DESC");
                        mysqli_stmt_bind_param($stmt, "i", $company['user_id']);
                        mysqli_stmt_execute($stmt);
                        $jobs = mysqli_stmt_get_result($stmt);
                        if (mysqli_num_rows($jobs) > 0) {
                            while ($job = mysqli_fetch_assoc($jobs)) {
                                echo "<tr><td>" . htmlspecialchars($job['title']) . "</td><td>" . htmlspecialchars($job['location']) . "</td><td>" . htmlspecialchars($job['status']) . "</td><td>" . htmlspecialchars($job['app_count']) . "</td><td><a class='btn btn-sm btn-primary' href='job-details.php?id=" . $job['id'] . "'>View Details</a></td></tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' class='text-center text-muted'>No jobs found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container text-center"><p>&copy; 2026 Job Portal. All rights reserved by Chetan Pawar.</p></div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
