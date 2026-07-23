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
?>

<?php
// Check if already applied
$stmt = mysqli_prepare($conc, "SELECT id FROM applications WHERE seeker_id=? AND job_id=?");
mysqli_stmt_bind_param($stmt, "ii", $seeker_id, $job_id);
mysqli_stmt_execute($stmt);
$check = mysqli_stmt_get_result($stmt);
if (mysqli_num_rows($check) > 0) {
    echo "<script>alert('You already applied');window.location='jobs.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply for Job</title>
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
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card form-card">
                    <div class="card-body">
                        <h2 class="card-title mb-4">Submit Application</h2>
                        <p class="text-muted mb-4">Upload your resume to apply for this position</p>

                        <form method="post" enctype="multipart/form-data">
                            <div class="form-group mb-4">
                                <label class="form-label">Upload Resume</label>
                                <div class="input-group">
                                    <input type="file" name="resume" class="form-control" accept=".pdf,.doc,.docx" required>
                                </div>
                                <small class="form-text text-muted d-block mt-2">
                                    <i class="fas fa-info-circle"></i> Accepted formats: PDF, DOC, DOCX (Max 5MB)
                                </small>
                            </div>
                            <button type="submit" name="btnapply" class="btn btn-success btn-lg w-100">
                                <i class="fas fa-paper-plane"></i> Submit Application
                            </button>
                        </form>
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

<?php
if (isset($_POST['btnapply'])) {
    $error = '';
    $resume = save_resume_upload($_FILES['resume'], $error);

    if (!$resume) {
        echo "<script>alert('" . addslashes($error) . "');window.location='apply-job.php?job=$job_id';</script>";
        exit();
    }

    $stmt = mysqli_prepare($conc, "INSERT INTO applications(job_id,seeker_id,resume,status) VALUES(?,?,?,'Applied')");
    mysqli_stmt_bind_param($stmt, "iis", $job_id, $seeker_id, $resume);
    mysqli_stmt_execute($stmt);

    echo "<script>alert('Applied Successfully');window.location='applications.php';</script>";
}
?>
