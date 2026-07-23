<?php
require("../config/conn.php");
session_start();
if ($_SESSION['role'] != "admin") {
    header("Location: ../auth/login.php");
    exit();
}

$job_id = intval($_GET['id'] ?? 0);
$stmt = mysqli_prepare($conc, "SELECT j.*, u.name AS employer_name, u.email AS employer_email, c.company_name, c.description AS company_description, c.location AS company_location, cat.category_name FROM jobs j LEFT JOIN users u ON j.employer_id=u.id LEFT JOIN companies c ON j.employer_id=c.user_id LEFT JOIN categories cat ON j.category_id=cat.id WHERE j.id=?");
mysqli_stmt_bind_param($stmt, "i", $job_id);
mysqli_stmt_execute($stmt);
$job = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$job) {
    echo "<script>alert('Job not found');window.location='manage-jobs.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Details</title>
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
                <h1 class="mb-1"><?= htmlspecialchars($job['title']) ?></h1>
                <p class="text-muted mb-0">Complete job posting details</p>
            </div>
            <a href="manage-jobs.php" class="btn btn-outline-primary">Back to Jobs</a>
        </div>

        <div class="row">
            <div class="col-lg-7 mb-4">
                <div class="card h-100">
                    <div class="card-header"><strong>Job Information</strong></div>
                    <div class="card-body">
                        <p><strong>Category:</strong> <?= htmlspecialchars($job['category_name'] ?? 'Uncategorized') ?></p>
                        <p><strong>Location:</strong> <?= htmlspecialchars($job['location']) ?></p>
                        <p><strong>Salary:</strong> <?= htmlspecialchars($job['salary']) ?></p>
                        <p><strong>Type:</strong> <?= htmlspecialchars($job['job_type']) ?></p>
                        <p><strong>Expiry Date:</strong> <?= htmlspecialchars($job['expiry_date']) ?></p>
                        <p><strong>Status:</strong> <?= htmlspecialchars($job['status']) ?></p>
                        <p><strong>Description:</strong><br><?= nl2br(htmlspecialchars($job['description'])) ?></p>
                    </div>
                </div>
            </div>
            <div class="col-lg-5 mb-4">
                <div class="card h-100">
                    <div class="card-header"><strong>Employer / Company</strong></div>
                    <div class="card-body">
                        <p><strong>Employer:</strong> <?= htmlspecialchars($job['employer_name']) ?></p>
                        <p><strong>Email:</strong> <?= htmlspecialchars($job['employer_email']) ?></p>
                        <p><strong>Company:</strong> <?= htmlspecialchars($job['company_name'] ?? 'Not added') ?></p>
                        <p><strong>Company Location:</strong> <?= htmlspecialchars($job['company_location'] ?? 'Not added') ?></p>
                        <p><strong>Company Description:</strong><br><?= nl2br(htmlspecialchars($job['company_description'] ?? 'Not added')) ?></p>
                        <a href="user-details.php?id=<?= $job['employer_id'] ?>" class="btn btn-primary btn-sm">View Employer</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><strong>Applications</strong></div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead><tr><th>Applicant</th><th>Email</th><th>Status</th><th>Resume</th><th>Action</th></tr></thead>
                    <tbody>
                        <?php
                        $stmt = mysqli_prepare($conc, "SELECT a.*, u.name, u.email FROM applications a JOIN users u ON a.seeker_id=u.id WHERE a.job_id=? ORDER BY a.applied_at DESC");
                        mysqli_stmt_bind_param($stmt, "i", $job_id);
                        mysqli_stmt_execute($stmt);
                        $apps = mysqli_stmt_get_result($stmt);
                        if (mysqli_num_rows($apps) > 0) {
                            while ($app = mysqli_fetch_assoc($apps)) {
                                $resume = !empty($app['resume']) ? "<a href='../uploads/resumes/" . htmlspecialchars($app['resume']) . "' target='_blank'>View Resume</a>" : "Not uploaded";
                                echo "<tr><td>" . htmlspecialchars($app['name']) . "</td><td>" . htmlspecialchars($app['email']) . "</td><td>" . htmlspecialchars($app['status']) . "</td><td>$resume</td><td><a class='btn btn-sm btn-primary' href='user-details.php?id=" . $app['seeker_id'] . "'>View User</a></td></tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' class='text-center text-muted'>No applications found.</td></tr>";
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
