<?php
require("../config/conn.php");
session_start();
if ($_SESSION['role'] != "employer") {
    header("Location: ../auth/login.php");
    exit();
}
$emp_id = $_SESSION['user_id'];
$job_id = intval($_GET['job'] ?? 0);

// Handle status update
if (isset($_GET['update']) && isset($_GET['status'])) {
    $app_id = intval($_GET['update']);
    $allowed_statuses = ['Applied', 'Shortlisted', 'Accepted', 'Rejected'];
    $new_status = $_GET['status'];
    if (!in_array($new_status, $allowed_statuses, true)) {
        header("Location: applicants.php?job=$job_id");
        exit();
    }
    
    // Verify job belongs to this employer
    $stmt = mysqli_prepare($conc, "SELECT a.id FROM applications a 
    JOIN jobs j ON a.job_id = j.id 
    WHERE a.id=? AND j.employer_id=?");
    mysqli_stmt_bind_param($stmt, "ii", $app_id, $emp_id);
    mysqli_stmt_execute($stmt);
    $verify = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($verify) > 0) {
        $stmt = mysqli_prepare($conc, "UPDATE applications SET status=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, "si", $new_status, $app_id);
        mysqli_stmt_execute($stmt);
    }
    header("Location: applicants.php?job=$job_id");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Applicants</title>
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
                    <li class="nav-item"><a class="nav-link" href="company-profile.php">Company Profile</a></li>
                    <li class="nav-item"><a class="nav-link" href="post-job.php">Post Job</a></li>
                    <li class="nav-item"><a class="nav-link" href="my-jobs.php">My Jobs</a></li>
                    <li class="nav-item"><a class="nav-link" href="applicants.php">Applicants</a></li>
                    <li class="nav-item"><a class="nav-link" href="../auth/logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="mb-2">Job Applicants</h1>
                <p class="text-muted">Review and manage applications for this job posting</p>
            </div>
        </div>

        <?php
        // Fetch job details to verify it exists and belongs to employer
        $stmt = mysqli_prepare($conc, "SELECT * FROM jobs WHERE id=? AND employer_id=?");
        mysqli_stmt_bind_param($stmt, "ii", $job_id, $emp_id);
        mysqli_stmt_execute($stmt);
        $job_verify = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($job_verify) > 0) {
            $job_info = mysqli_fetch_assoc($job_verify);
            echo "<div class='alert alert-info mb-4'>
                    <strong><i class='fas fa-briefcase'></i> Job:</strong> " . htmlspecialchars($job_info['title']) . "
                  </div>";
        } else {
            echo "<div class='alert alert-danger'>
                    <i class='fas fa-exclamation-circle'></i> Invalid job ID or access denied.
                  </div>";
            echo "<script>setTimeout(() => window.location='my-jobs.php', 2000);</script>";
        }
        ?>

        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>Applicant Name</th>
                        <th>Email</th>
                        <th>Resume</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($job_id > 0) {
                        $stmt = mysqli_prepare($conc, "SELECT a.*, u.name, u.email, s.skills, s.education FROM applications a 
                        JOIN users u ON a.seeker_id=u.id
                        LEFT JOIN seekers s ON u.id=s.user_id
                        WHERE a.job_id=? ORDER BY a.applied_at DESC");
                        mysqli_stmt_bind_param($stmt, "i", $job_id);
                        mysqli_stmt_execute($stmt);
                        $q = mysqli_stmt_get_result($stmt);

                        if (mysqli_num_rows($q) > 0) {
                            while ($r = mysqli_fetch_assoc($q)) {
                                $status = trim($r['status']);
                                
                                // Determine badge color and icon based on status - EXACT MATCH ONLY
                                if ($status === 'Accepted') {
                                    $badge_class = 'bg-success';
                                    $status_text = '✅ Accepted';
                                } elseif ($status === 'Shortlisted') {
                                    $badge_class = 'bg-info';
                                    $status_text = '📋 Shortlisted';
                                } elseif ($status === 'Rejected') {
                                    $badge_class = 'bg-danger';
                                    $status_text = '❌ Rejected';
                                } else {
                                    // Default - anything else is Applied
                                    $badge_class = 'bg-warning';
                                    $status_text = '📝 Applied';
                                }
                                
                                echo "<tr>
                                        <td><strong>" . htmlspecialchars($r['name']) . "</strong></td>
                                        <td>" . htmlspecialchars($r['email']) . "</td>
                                        <td><a href='../uploads/resumes/" . htmlspecialchars($r['resume']) . "' target='_blank' class='btn btn-sm btn-outline-secondary'>
                                            <i class='fas fa-file-pdf'></i> View Resume</a></td>
                                        <td><span class='badge " . $badge_class . " fs-6'>" . $status_text . "</span></td>
                                        <td style='white-space: nowrap;'>
                                            <a href='?job=" . $job_id . "&update=" . $r['id'] . "&status=Shortlisted' class='btn btn-sm btn-info' onclick='return confirm(\"Mark as Shortlisted?\")'>Shortlist</a>
                                            <a href='?job=" . $job_id . "&update=" . $r['id'] . "&status=Accepted' class='btn btn-sm btn-success' onclick='return confirm(\"Mark as Accepted?\")'>Accept</a>
                                            <a href='?job=" . $job_id . "&update=" . $r['id'] . "&status=Rejected' class='btn btn-sm btn-danger' onclick='return confirm(\"Mark as Rejected?\")'>Reject</a>
                                        </td>
                                    </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' class='text-center py-4'><p class='text-muted'>No applications for this job yet.</p></td></tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' class='text-center py-4'><p class='text-muted text-danger'><i class='fas fa-exclamation-triangle'></i> Please select a job from My Jobs.</p></td></tr>";
                    }
                    ?>
                </tbody>
            </table>
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
