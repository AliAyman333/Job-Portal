<?php
require("../config/conn.php");
session_start();
if ($_SESSION['role'] != "employer") {
    header("Location: ../auth/login.php");
    exit();
}
$emp_id = $_SESSION['user_id'];

// Handle delete
if (isset($_GET['del'])) {
    $job_id = intval($_GET['del']);
    $stmt = mysqli_prepare($conc, "DELETE FROM jobs WHERE id=? AND employer_id=?");
    mysqli_stmt_bind_param($stmt, "ii", $job_id, $emp_id);
    mysqli_stmt_execute($stmt);
    header("Location: my-jobs.php");
    exit();
}

// Handle toggle status - Pending -> Open -> Closed -> Open
if (isset($_GET['toggle']) && isset($_GET['status'])) {
    $job_id = intval($_GET['toggle']);
    $current_status = $_GET['status'];
    
    if ($current_status === 'Pending') {
        $new_status = 'Open';
    } elseif ($current_status === 'Open') {
        $new_status = 'Closed';
    } else {
        $new_status = 'Open';
    }
    
    $stmt = mysqli_prepare($conc, "UPDATE jobs SET status=? WHERE id=? AND employer_id=?");
    mysqli_stmt_bind_param($stmt, "sii", $new_status, $job_id, $emp_id);
    mysqli_stmt_execute($stmt);
    header("Location: my-jobs.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Jobs</title>
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
                    <li class="nav-item"><a class="nav-link" href="applicants.php">Applicants</a></li>
                    <li class="nav-item"><a class="nav-link" href="my-jobs.php">My Jobs</a></li>
                    <li class="nav-item"><a class="nav-link" href="../auth/logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="mb-2">My Job Posts</h1>
                <p class="text-muted">Manage all your job postings</p>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Job Title</th>
                        <th>Location</th>
                        <th>Salary</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $q = mysqli_query($conc, "SELECT * FROM jobs WHERE employer_id='$emp_id' ORDER BY id DESC");
                    if (mysqli_num_rows($q) > 0) {
                        while ($r = mysqli_fetch_assoc($q)) {
                            $status = $r['status'];
                            
                            if ($status === 'Pending') {
                                $badge = '<span class="badge bg-info">Pending</span>';
                                $btn_text = 'Open Job';
                                $btn_color = 'btn-success';
                            } elseif ($status === 'Open') {
                                $badge = '<span class="badge bg-success">Open</span>';
                                $btn_text = 'Close Job';
                                $btn_color = 'btn-warning';
                            } else {
                                $badge = '<span class="badge bg-danger">Closed</span>';
                                $btn_text = 'Open Job';
                                $btn_color = 'btn-success';
                            }
                            
                            echo "<tr>
                                    <td><strong>" . htmlspecialchars($r['title']) . "</strong></td>
                                    <td>" . htmlspecialchars($r['location']) . "</td>
                                    <td>$" . htmlspecialchars($r['salary']) . "</td>
                                    <td>$badge</td>
                                    <td>
                                        <a href='applicants.php?job=" . $r['id'] . "' class='btn btn-sm btn-info'>View Applicatons</a>
                                        <a href='?toggle=" . $r['id'] . "&status=" . urlencode($status) . "' class='btn btn-sm $btn_color'>$btn_text</a>
                                        <a href='?del=" . $r['id'] . "' class='btn btn-sm btn-danger' onclick='return confirm(\"Delete this job?\")'>Delete</a>
                                    </td>
                                </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' class='text-center py-4'><p class='text-muted'>No jobs posted yet. <a href='post-job.php'>Post one now</a></p></td></tr>";
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
