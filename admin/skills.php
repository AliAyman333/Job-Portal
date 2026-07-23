<?php
require("../config/conn.php");
session_start();

// التأكد من صلاحية المشرف
if ($_SESSION['role'] != "admin") {
    header("Location: ../auth/login.php");
    exit();
}

// معالجة إضافة مهارة جديدة
if (isset($_POST['add_skill'])) {
    $skill_name = trim($_POST['skill_name']);
    if (!empty($skill_name)) {
        $stmt = mysqli_prepare($conc, "INSERT INTO skills (skill_name) VALUES (?)");
        mysqli_stmt_bind_param($stmt, "s", $skill_name);
        if (mysqli_stmt_execute($stmt)) {
            $msg = "Skill added successfully.";
        } else {
            $msg = "Error: " . mysqli_error($conc);
        }
    } else {
        $msg = "Skill name cannot be empty.";
    }
}

// معالجة حذف مهارة
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = mysqli_prepare($conc, "DELETE FROM skills WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    if (mysqli_stmt_execute($stmt)) {
        $msg = "Skill deleted successfully.";
    } else {
        $msg = "Error deleting skill.";
    }
}

// جلب جميع المهارات لعرضها
$skills_result = mysqli_query($conc, "SELECT * FROM skills ORDER BY skill_name ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Skills</title>
    <link rel="icon" type="image/svg+xml" href="../css/favicon.svg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <!-- شريط التنقل للمشرف -->
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
                    <li class="nav-item"><a class="nav-link " href="skills.php">Skills</a></li>
                    <li class="nav-item"><a class="nav-link" href="activity-log.php">Activity Log</a></li>
                    <li class="nav-item"><a class="nav-link" href="../auth/logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>


    <div class="container py-5">
        <div class="row">
            <div class="col-md-6 mx-auto">
                <div class="card form-card mb-4">
                    <div class="card-body">
                        <h3 class="card-title mb-4">Add New Skill</h3>
                        <?php if (isset($msg)): ?>
                            <div class="alert alert-info"><?= htmlspecialchars($msg) ?></div>
                        <?php endif; ?>
                        <form method="post">
                            <div class="mb-3">
                                <label class="form-label">Skill Name</label>
                                <input type="text" name="skill_name" class="form-control" placeholder="e.g., PHP, React" required>
                            </div>
                            <button type="submit" name="add_skill" class="btn btn-primary w-100">Add Skill</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card form-card">
                    <div class="card-body">
                        <h3 class="card-title mb-4">Existing Skills</h3>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Skill Name</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($skill = mysqli_fetch_assoc($skills_result)): ?>
                                    <tr>
                                        <td><?= $skill['id'] ?></td>
                                        <td><?= htmlspecialchars($skill['skill_name']) ?></td>
                                        <td>
                                            <a href="manage-skills.php?delete=<?= $skill['id'] ?>" 
                                               class="btn btn-danger btn-sm"
                                               onclick="return confirm('Are you sure you want to delete this skill?')">
                                               Delete
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                    <?php if (mysqli_num_rows($skills_result) == 0): ?>
                                    <tr>
                                        <td colspan="3" class="text-center">No skills found.</td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container text-center">
            <p>&copy; 2026 Job Portal. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>