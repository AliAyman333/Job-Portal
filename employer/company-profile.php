<?php
require("../config/conn.php");
session_start();
if ($_SESSION['role'] != "employer") {
    header("Location: ../auth/login.php");
    exit();
}
$emp_id = $_SESSION['user_id'];

$stmt = mysqli_prepare($conc, "SELECT * FROM companies WHERE user_id=?");
mysqli_stmt_bind_param($stmt, "i", $emp_id);
mysqli_stmt_execute($stmt);
$q = mysqli_stmt_get_result($stmt);
$company = mysqli_fetch_assoc($q);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Profile</title>
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
        <div class="row justify-content-center">
            <div class="col-md-7">
                <div class="card form-card">
                    <div class="card-body">
                        <h2 class="card-title mb-4">Company Profile</h2>

                        <form method="post">
                            <div class="form-group mb-3">
                                <label class="form-label">Company Name</label>
                                <input type="text" name="name" class="form-control" placeholder="Enter company name" value="<?= htmlspecialchars($company['company_name'] ?? '') ?>" required>
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label">Location</label>
                                <input type="text" name="location" class="form-control" placeholder="e.g., San Francisco, CA" value="<?= htmlspecialchars($company['location'] ?? '') ?>" required>
                            </div>

                            <div class="form-group mb-4">
                                <label class="form-label">Company Description</label>
                                <textarea name="desc" class="form-control" rows="5" placeholder="Describe your company, mission, culture, and values..."><?= htmlspecialchars($company['description'] ?? '') ?></textarea>
                            </div>

                            <button type="submit" name="btnsave" class="btn btn-success btn-lg w-100">Save Profile</button>
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
if (isset($_POST['btnsave'])) {
    $name = trim($_POST['name']);
    $desc = trim($_POST['desc']);
    $loc = trim($_POST['location']);

    if ($company) {
        $stmt = mysqli_prepare($conc, "UPDATE companies SET company_name=?, description=?, location=? WHERE user_id=?");
        mysqli_stmt_bind_param($stmt, "sssi", $name, $desc, $loc, $emp_id);
        mysqli_stmt_execute($stmt);
    } else {
        $stmt = mysqli_prepare($conc, "INSERT INTO companies(user_id,company_name,description,location) VALUES(?,?,?,?)");
        mysqli_stmt_bind_param($stmt, "isss", $emp_id, $name, $desc, $loc);
        mysqli_stmt_execute($stmt);
    }

    echo "<script>alert('Profile Saved');window.location='company-profile.php';</script>";
}
?>
