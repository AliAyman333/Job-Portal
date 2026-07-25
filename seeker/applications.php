<?php
require("../config/conn.php");
session_start();
if ($_SESSION['role'] != "seeker") {
    header("Location: ../auth/login.php");
}
$seeker_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Applications</title>
    <link rel="icon" type="image/svg+xml" href="../css/favicon.svg">
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
        <h2 class="mb-4">My Applications</h2>

        <?php
        $q = mysqli_query($conc, "SELECT a.*,j.title FROM applications a 
JOIN jobs j ON a.job_id=j.id WHERE a.seeker_id='$seeker_id'");
        $count = mysqli_num_rows($q);

        if ($count > 0) {
        ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Application ID</th>
                        <th>Job Title</th>
                        <th>Date Applied</th>
                        <th>Status</th>
                        <th>Resume</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($r = mysqli_fetch_assoc($q)) {
                        $status = $r['status'] ?? 'pending';
                        $badge_class = match($status) {
                            'approved' => 'badge-success',
                            'rejected' => 'badge-danger',
                            default => 'badge-warning'
                        };
                    ?>
                    <tr>
                        <td>#<?= $r['id'] ?></td>
                        <td><strong><?= htmlspecialchars($r['title']) ?></strong></td>
                        <td><?= date('M d, Y', strtotime($r['applied_date'] ?? 'now')) ?></td>
                        <td><span class="badge <?= $badge_class ?>"><?= ucfirst($status) ?></span></td>
                        <td><a href="../uploads/resumes/<?= $r['resume'] ?>" target="_blank" class="btn btn-sm btn-outline-primary">View</a></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <?php } else { ?>
        <div class="alert alert-info text-center py-5">
            <h5>No applications yet</h5>
            <p>Start applying for jobs to see your applications here.</p>
            <a href="jobs.php" class="btn btn-primary">Browse Jobs</a>
        </div>
        <?php } ?>
    </div>

    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container text-center">
            <p>&copy; 2026 Job Portal. All rights reserved by Chetan Pawar.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
