<?php
require("../config/conn.php");
require("../includes/upload_helper.php");
session_start();
if ($_SESSION['role'] != "seeker") {
    header("Location: ../auth/login.php");
    exit();
}

$seeker_id = $_SESSION['user_id'];
$job_id = intval($_GET['job'] ?? 0);

// Handle job application
if (isset($_POST['btnapply'])) {
    if (!empty($_FILES['resume']['name'])) {
        $error = '';
        $resume = save_resume_upload($_FILES['resume'], $error);
        if (!$resume) {
            echo "<script>alert('" . addslashes($error) . "');</script>";
        } else {
            $stmt = mysqli_prepare($conc, "INSERT INTO applications(job_id,seeker_id,resume,status) VALUES(?,?,?,'Applied')");
            mysqli_stmt_bind_param($stmt, "iis", $job_id, $seeker_id, $resume);
            mysqli_stmt_execute($stmt);

            echo "<script>alert('Applied Successfully');window.location='applications.php';</script>";
            exit();
        }
    } else {
        echo "<script>alert('Please upload a resume');</script>";
    }
}

// Fetch job details and verify availability before showing the page.
$stmt = mysqli_prepare($conc, "SELECT j.*, 
                        COALESCE(c.company_name, u.name) as company_name, 
                        COALESCE(c.description, 'No company description available') AS company_desc, 
                        COALESCE(c.location, j.location) AS company_loc
                        FROM jobs j
                        LEFT JOIN companies c ON j.employer_id=c.user_id
                        LEFT JOIN users u ON j.employer_id=u.id
                        WHERE j.id=?");
mysqli_stmt_bind_param($stmt, "i", $job_id);
mysqli_stmt_execute($stmt);
$q = mysqli_stmt_get_result($stmt);
$job = mysqli_fetch_assoc($q);

if (!$job) {
    echo "<script>alert('Job not found');window.location='jobs.php';</script>";
    exit();
}

// Check if job is published
if ($job['status'] != 'Open') {
    echo "<script>alert('This job is not available');window.location='jobs.php';</script>";
    exit();
}

// Check if already applied
$stmt = mysqli_prepare($conc, "SELECT id FROM applications WHERE job_id=? AND seeker_id=?");
mysqli_stmt_bind_param($stmt, "ii", $job_id, $seeker_id);
mysqli_stmt_execute($stmt);
$applied = mysqli_num_rows(mysqli_stmt_get_result($stmt));
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

    <div class="container py-5">
        <div class="row">
            <div class="col-lg-8">
                <div class="card form-card mb-4">
                    <div class="card-body">
                        <h2 class="card-title mb-3"><?= htmlspecialchars($job['title']) ?></h2>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <p class="text-muted"><i class="fas fa-map-marker-alt"></i> <strong>Location:</strong>
                                </p>
                                <p><?= htmlspecialchars($job['location']) ?></p>
                            </div>
                            <div class="col-md-6">
                                <p class="text-muted"><i class="fas fa-dollar-sign"></i> <strong>Salary:</strong></p>
                                <p class="h5 text-success"><?= htmlspecialchars($job['salary']) ?></p>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <p class="text-muted"><i class="fas fa-briefcase"></i> <strong>Job Type:</strong></p>
                                <p><?= htmlspecialchars($job['job_type']) ?></p>
                            </div>
                            <div class="col-md-6">
                                <p class="text-muted"><i class="fas fa-calendar"></i> <strong>Expiry Date:</strong></p>
                                <p><?= date('M d, Y', strtotime($job['expiry_date'])) ?></p>
                            </div>
                        </div>

                        <hr>
                        <h4 class="mb-3">Job Description</h4>
                        <p class="text-muted"><?= nl2br(htmlspecialchars($job['description'])) ?></p>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card form-card mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Company Information</h5>
                        <h6><?= htmlspecialchars($job['company_name']) ?></h6>
                        <p class="text-muted small"><?= htmlspecialchars($job['company_desc']) ?></p>
                        <p class="text-muted"><i class="fas fa-map-marker-alt"></i>
                            <?= htmlspecialchars($job['company_loc']) ?></p>

                        <hr>

                        <?php if ($applied > 0) { ?>
                            <button class="btn btn-secondary w-100" disabled>
                                <i class="fas fa-check-circle"></i> Already Applied
                            </button>
                        <?php } else { ?>
                            <form method="post" enctype="multipart/form-data">
                                <div class="form-group mb-3">
                                    <label class="form-label">Upload Resume</label>
                                    <input type="file" name="resume" class="form-control" accept=".pdf,.doc,.docx" required>
                                    <small class="form-text text-muted">PDF or DOC format</small>
                                </div>
                                <button type="submit" name="btnapply" class="btn btn-success w-100">
                                    <i class="fas fa-paper-plane"></i> Apply Now
                                </button>
                            </form>
                        <?php } ?>
                    </div>
                </div>
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
