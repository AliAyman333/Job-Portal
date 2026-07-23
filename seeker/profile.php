<?php
require("../config/conn.php");
require("../includes/upload_helper.php");
session_start();
if ($_SESSION['role'] != "seeker") {
    header("Location: ../auth/login.php");
    exit();
}
$seeker_id = $_SESSION['user_id'];

// جلب بيانات الملف الشخصي الحالية
$stmt = mysqli_prepare($conc, "SELECT * FROM seekers WHERE user_id=?");
mysqli_stmt_bind_param($stmt, "i", $seeker_id);
mysqli_stmt_execute($stmt);
$q = mysqli_stmt_get_result($stmt);
$profile = mysqli_fetch_assoc($q);

// جلب قائمة التصنيفات (Categories) لاستخدامها كـ "Domain"
$cat_query = mysqli_query($conc, "SELECT id, category_name FROM categories ORDER BY category_name");
$categories = [];
while ($c = mysqli_fetch_assoc($cat_query)) {
    $categories[] = $c;
}

// جلب قائمة المهارات من جدول skills
$skills_query = mysqli_query($conc, "SELECT id, skill_name FROM skills ORDER BY skill_name");
$all_skills = [];
while ($s = mysqli_fetch_assoc($skills_query)) {
    $all_skills[] = $s;
}

// المهارات الحالية للمستخدم كنص مفصول بفواصل، نحولها لمصفوفة IDs
$current_skill_ids = [];
if (!empty($profile['skills'])) {
    $current_skill_ids = array_map('intval', explode(',', $profile['skills']));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
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
                        <h2 class="card-title mb-4">My Profile</h2>

                        <form method="post" enctype="multipart/form-data">
                            <!-- اختيار المجال (من جدول categories) -->
                            <div class="form-group mb-3">
                                <label class="form-label">Domain </label>
                                <select name="category_id" class="form-select" required>
                                    <option value="">Select Domain</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= $cat['id'] ?>"
                                            <?= (isset($profile['category_id']) && $profile['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($cat['category_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- اختيار المهارات (متعدد) -->
                            <div class="form-group mb-3">
                                <label class="form-label">Skills</label>
                                <select name="skills[]" class="form-select" multiple size="6" required>
                                    <?php foreach ($all_skills as $skill): ?>
                                        <option value="<?= $skill['id'] ?>"
                                            <?= in_array($skill['id'], $current_skill_ids) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($skill['skill_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="form-text text-muted">Hold Ctrl (Cmd) to select multiple.</small>
                            </div>

                            <!-- الجنس -->
                            <div class="form-group mb-3">
                                <label class="form-label">Gender</label>
                                <select name="gender" class="form-select" required>
                                    <option value="">-- Select --</option>
                                    <option value="Male" <?= (isset($profile['gender']) && $profile['gender'] == 'Male') ? 'selected' : '' ?>>Male</option>
                                    <option value="Female" <?= (isset($profile['gender']) && $profile['gender'] == 'Female') ? 'selected' : '' ?>>Female</option>
                                </select>
                            </div>

                            <!-- سنوات الخبرة (رقم) -->
                            <div class="form-group mb-3">
                                <label class="form-label">Years of Experience</label>
                                <input type="number" name="years_of_experience" class="form-control"
                                       min="0" step="1" placeholder="e.g., 3"
                                       value="<?= $profile['years_of_experience'] ?? '' ?>" required>
                            </div>

                            <!-- التعليم (قائمة منسدلة) -->
                            <div class="form-group mb-3">
                                <label class="form-label">Education</label>
                                <select name="education" class="form-select" required>
                                    <option value="">-- Select Education Level --</option>
                                    <option value="Bachelor's Degree" <?= (isset($profile['education']) && $profile['education'] == "Bachelor's Degree") ? 'selected' : '' ?>>Bachelor's Degree</option>
                                    <option value="Master's Degree" <?= (isset($profile['education']) && $profile['education'] == "Master's Degree") ? 'selected' : '' ?>>Master's Degree</option>
                                    <option value="Doctorate" <?= (isset($profile['education']) && $profile['education'] == "Doctorate") ? 'selected' : '' ?>>Doctorate</option>
                                </select>
                            </div>

                            <!-- رفع السيرة الذاتية -->
                            <div class="form-group mb-4">
                                <label class="form-label">Upload Resume (PDF/DOC/DOCX)</label>
                                <input type="file" name="resume" class="form-control" accept=".pdf,.doc,.docx">
                                <small class="form-text text-muted">Maximum file size: 5MB</small>
                            </div>

                            <button type="submit" name="btnsave" class="btn btn-primary btn-lg w-100">Save Profile</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-white py-4 mt-5">
        <!-- الفوتر كما هو -->
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
if (isset($_POST['btnsave'])) {
    $category_id = intval($_POST['category_id']);
    $gender = $_POST['gender'];
    $years_of_experience = intval($_POST['years_of_experience']);
    $education = $_POST['education']; // الآن قيمة نصية من القائمة

    // معالجة المهارات
    $skills_array = $_POST['skills'] ?? [];
    $skills_ids = array_map('intval', $skills_array);
    $skills_str = implode(',', $skills_ids);

    // معالجة السيرة الذاتية المرفوعة
    if (isset($_FILES['resume']) && $_FILES['resume']['name'] != "") {
        $error = '';
        $resume = save_resume_upload($_FILES['resume'], $error);
        if (!$resume) {
            echo "<script>alert('" . addslashes($error) . "');window.location='profile.php';</script>";
            exit();
        }
    } else {
        $resume = $profile['resume'] ?? '';
    }

    if ($profile) {
        $stmt = mysqli_prepare($conc,
            "UPDATE seekers SET category_id=?, skills=?, gender=?, years_of_experience=?, education=?, resume=? WHERE user_id=?");
        mysqli_stmt_bind_param($stmt, "ississi", $category_id, $skills_str, $gender, $years_of_experience, $education, $resume, $seeker_id);
        mysqli_stmt_execute($stmt);
    } else {
        $stmt = mysqli_prepare($conc,
            "INSERT INTO seekers(user_id, category_id, skills, gender, years_of_experience, education, resume) VALUES(?,?,?,?,?,?,?)");
        mysqli_stmt_bind_param($stmt, "iisssss", $seeker_id, $category_id, $skills_str, $gender, $years_of_experience, $education, $resume);
        mysqli_stmt_execute($stmt);
    }

    echo "<script>alert('Profile Updated');window.location='profile.php';</script>";
}
?>