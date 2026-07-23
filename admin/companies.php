<?php
require("../config/conn.php");
session_start();
if ($_SESSION['role'] != "admin") {
    header("Location: ../auth/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Companies</title>
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
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="mb-2">Companies</h1>
                <p class="text-muted">View employer company profiles and related details</p>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Company</th>
                        <th>Employer</th>
                        <th>Email</th>
                        <th>Location</th>
                        <th>Jobs</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $q = mysqli_query($conc, "SELECT c.*, u.name AS employer_name, u.email, COUNT(j.id) AS job_count FROM companies c JOIN users u ON c.user_id=u.id LEFT JOIN jobs j ON c.user_id=j.employer_id GROUP BY c.id, u.name, u.email ORDER BY c.id DESC");
                    if (mysqli_num_rows($q) > 0) {
                        while ($r = mysqli_fetch_assoc($q)) {
                            echo "<tr>
                                <td><strong>" . htmlspecialchars($r['company_name']) . "</strong></td>
                                <td>" . htmlspecialchars($r['employer_name']) . "</td>
                                <td>" . htmlspecialchars($r['email']) . "</td>
                                <td>" . htmlspecialchars($r['location']) . "</td>
                                <td>" . htmlspecialchars($r['job_count']) . "</td>
                                <td><a href='company-details.php?id=" . $r['id'] . "' class='btn btn-sm btn-primary'>View Details</a></td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6' class='text-center py-4'><p class='text-muted'>No companies found.</p></td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container text-center"><p>&copy; 2026 Job Portal. All rights reserved by Chetan Pawar.</p></div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
