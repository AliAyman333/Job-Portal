<?php
require("../config/conn.php");
session_start();
if ($_SESSION['role'] != "employer") {
    header("Location: ../auth/login.php");
    exit();
}
$emp_id = $_SESSION['user_id'];

// جلب التصنيفات (Categories) لاستخدامها كـ Domain
$cat_query = mysqli_query($conc, "SELECT id, category_name FROM categories ORDER BY category_name");
$categories = [];
while ($c = mysqli_fetch_assoc($cat_query)) {
    $categories[] = $c;
}

// جلب المهارات من جدول skills
$skills_query = mysqli_query($conc, "SELECT id, skill_name FROM skills ORDER BY skill_name");
$all_skills = [];
while ($s = mysqli_fetch_assoc($skills_query)) {
    $all_skills[] = $s;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post New Job</title>
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
            <div class="col-md-8">
                <div class="card form-card">
                    <div class="card-body">
                        <h2 class="card-title mb-4">Post New Job Opening</h2>

                        <form method="post">
                            <!-- عنوان الوظيفة -->
                            <div class="form-group mb-3">
                                <label class="form-label">Job Title</label>
                                <input type="text" name="title" class="form-control"
                                       placeholder="e.g., Senior Software Engineer" required>
                            </div>

                            <!-- المجال (Category) من جدول categories -->
                            <div class="form-group mb-3">
                                <label class="form-label">Domain / Category</label>
                                <select name="category_id" class="form-select" required>
                                    <option value="">Select Domain</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= $cat['id'] ?>">
                                            <?= htmlspecialchars($cat['category_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- المهارات المطلوبة -->
                            <div class="form-group mb-3">
                                <label class="form-label">Required Skills</label>
                                <select name="skills[]" class="form-select" multiple size="6" required>
                                    <?php foreach ($all_skills as $skill): ?>
                                        <option value="<?= $skill['id'] ?>">
                                            <?= htmlspecialchars($skill['skill_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="form-text text-muted">Hold Ctrl (Cmd) to select multiple.</small>
                            </div>

                            <!-- الجنس المطلوب -->
                            <div class="form-group mb-3">
                                <label class="form-label">Required Gender</label>
                                <select name="gender" class="form-select" required>
                                    <option value="">-- Select --</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Not important">Not important</option>
                                </select>
                            </div>

                            <!-- التعليم المطلوب -->
                         <div class="form-group mb-3">
                                <label class="form-label">Education</label>
                                <select name="education" class="form-select" required>
                                    <option value="">-- Select Education Level --</option>
                                    <option value="Bachelor's Degree" <?= (isset($profile['education']) && $profile['education'] == "Bachelor's Degree") ? 'selected' : '' ?>>Bachelor's Degree</option>
                                    <option value="Master's Degree" <?= (isset($profile['education']) && $profile['education'] == "Master's Degree") ? 'selected' : '' ?>>Master's Degree</option>
                                    <option value="Doctorate" <?= (isset($profile['education']) && $profile['education'] == "Doctorate") ? 'selected' : '' ?>>Doctorate</option>
                                </select>
                            </div>

                            <!-- الموقع والراتب -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Location</label>
                                    <input type="text" name="location" class="form-control"
                                           placeholder="e.g., New York, NY" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Salary (Annual)</label>
                                    <input type="text" name="salary" class="form-control"
                                           placeholder="e.g., 80000" required>
                                </div>
                            </div>

                            <!-- نوع الوظيفة وتاريخ الانتهاء -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Job Type</label>
                                    <select name="job_type" class="form-select" required>
                                        <option value="">Select Job Type</option>
                                        <option>Full-Time</option>
                                        <option>Part-Time</option>
                                        <option>Internship</option>
                                        <option>Contract</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Expiration Date</label>
                                    <input type="date" name="expiry" class="form-control" required>
                                </div>
                            </div>

                            <!-- وصف الوظيفة -->
                            <div class="form-group mb-4">
                                <label class="form-label">Job Description</label>
                                <textarea name="description" class="form-control" rows="6"
                                          placeholder="Describe the job responsibilities, requirements, and benefits..."
                                          required></textarea>
                            </div>

                            <button type="submit" name="btnpost" class="btn btn-success btn-lg w-100">
                                Post Job
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
if (isset($_POST['btnpost'])) {
    $title = trim($_POST['title']);
    $category_id = intval($_POST['category_id']);
    $location = trim($_POST['location']);
    $salary = trim($_POST['salary']);
    $job_type = $_POST['job_type'];
    $expiry = $_POST['expiry'];
    $description = trim($_POST['description']);
    $gender = $_POST['gender'];
    $education = trim($_POST['education']);

    // معالجة المهارات
    $skills_array = $_POST['skills'] ?? [];
    $skills_ids = array_map('intval', $skills_array);
    $skills_str = implode(',', $skills_ids);

    $allowed_types = ['Full-Time', 'Part-Time', 'Internship', 'Contract'];
    if (!in_array($job_type, $allowed_types, true)) {
        echo "<script>alert('Invalid job type');window.location='post-job.php';</script>";
        exit();
    }

    // إدراج الوظيفة مع category_id بدلاً من domain
    $stmt = mysqli_prepare($conc,
        "INSERT INTO jobs(employer_id, title, category_id, location, salary, job_type, expiry_date, description, skills, gender, education, status)
         VALUES(?,?,?,?,?,?,?,?,?,?,?,'Open')");
    mysqli_stmt_bind_param($stmt, "issssssssss",
        $emp_id, $title, $category_id, $location, $salary,
        $job_type, $expiry, $description, $skills_str,
        $gender, $education
    );
    $insert_result = mysqli_stmt_execute($stmt);

    if ($insert_result) {
        echo "<script>alert('Job Posted Successfully');window.location='my-jobs.php';</script>";
    } else {
        echo "<script>alert('Error posting job: " . mysqli_error($conc) . "');window.location='post-job.php';</script>";
    }
}
?>