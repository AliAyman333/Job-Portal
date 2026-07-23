<?php
require("../config/conn.php");
session_start();
if ($_SESSION['role'] != "admin") {
    header("Location: ../auth/login.php");
    exit();
}

// Handle approve and delete operations
if (isset($_GET['approve'])) {
    $id = intval($_GET['approve']);
    $stmt = mysqli_prepare($conc, "UPDATE jobs SET status='Open' WHERE id=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    header("Location: manage-jobs.php");
    exit();
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = mysqli_prepare($conc, "DELETE FROM jobs WHERE id=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    header("Location: manage-jobs.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Jobs</title>
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
                    <li class="nav-item"><a class="nav-link" href="skills.php">Skills</a></li>
                    <li class="nav-item"><a class="nav-link" href="activity-log.php">Activity Log</a></li>
                    <li class="nav-item"><a class="nav-link" href="../auth/logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="mb-2">Manage Job Posts</h1>
                <p class="text-muted">Review and approve job postings</p>
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
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $q = mysqli_query($conc, "SELECT * FROM jobs ORDER BY id DESC");
                    if (mysqli_num_rows($q) > 0) {
                        while ($r = mysqli_fetch_assoc($q)) {
                            $status = $r['status'];
                            if ($status === 'Open') {
                                $status_badge = '<span class="badge bg-success">Open</span>';
                            } elseif ($status === 'Closed') {
                                $status_badge = '<span class="badge bg-danger">Closed</span>';
                            } else {
                                $status_badge = '<span class="badge bg-warning">Pending</span>';
                            }
                            echo "<tr>
                                    <td><strong>" . htmlspecialchars($r['title']) . "</strong></td>
                                    <td>" . htmlspecialchars($r['location']) . "</td>
                                    <td>" . htmlspecialchars($r['salary']) . "</td>
                                    <td>$status_badge</td>
                                    <td>";
                                    echo "<a href='job-details.php?id=" . $r['id'] . "' class='btn btn-sm btn-primary'>View Details</a> ";
                                    if ($status !== 'Open') {
                                        echo "<a href='?approve=" . $r['id'] . "' class='btn btn-sm btn-success'>Approve</a> ";
                                    }
                                    echo "<a href='?delete=" . $r['id'] . "' class='btn btn-sm btn-danger' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                                    </td>
                                </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' class='text-center py-4'><p class='text-muted'>No jobs found.</p></td></tr>";
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
