<?php
require("../config/conn.php");
session_start();
if ($_SESSION['role'] != "admin") {
    header("Location: ../auth/login.php");
    exit();
}

// Handle form submission
if (isset($_POST['btnadd'])) {
    $category = trim($_POST['cat']);
    $stmt = mysqli_prepare($conc, "INSERT INTO categories(category_name) VALUES(?)");
    mysqli_stmt_bind_param($stmt, "s", $category);
    mysqli_stmt_execute($stmt);
    header("Location: categories.php");
    exit();
}

// Handle delete
if (isset($_GET['del'])) {
    $id = intval($_GET['del']);
    $stmt = mysqli_prepare($conc, "DELETE FROM categories WHERE id=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    header("Location: categories.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories</title>
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
                <h1 class="mb-2">Manage Categories</h1>
                <p class="text-muted">Create and manage job categories</p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card form-card mb-4">
                    <div class="card-body">
                        <h2 class="card-title mb-4">Add New Category</h2>

                        <form method="post">
                            <div class="form-group mb-3">
                                <label class="form-label">Category Name</label>
                                <input type="text" name="cat" class="form-control" placeholder="Enter category name" required>
                            </div>
                            <button type="submit" name="btnadd" class="btn btn-success w-100">Add Category</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">All Categories</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Category Name</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $q = mysqli_query($conc, "SELECT * FROM categories ORDER BY category_name");
                                if (mysqli_num_rows($q) > 0) {
                                    while ($r = mysqli_fetch_assoc($q)) {
                                        echo "<tr>
                                                <td>" . htmlspecialchars($r['category_name']) . "</td>
                                                <td>
                                                    <a href='?del=" . $r['id'] . "' class='btn btn-sm btn-danger' onclick='return confirm(\"Are you sure?\")'>
                                                        <i class='fas fa-trash'></i> Delete
                                                    </a>
                                                </td>
                                            </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='2' class='text-center py-3'><p class='text-muted'>No categories added yet.</p></td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
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
