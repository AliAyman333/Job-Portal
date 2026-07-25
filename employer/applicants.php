<?php
require("../config/conn.php");
require("../includes/matching_helper.php");
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != "employer") {
    header("Location: ../auth/login.php");
    exit();
}
$emp_id = $_SESSION['user_id'];

// المشروع يستخدم اسم البارامتر "job" (نفس my-jobs.php و job-details.php)
$job_id = isset($_GET['job']) ? intval($_GET['job']) : 0;

// لو دخل من رابط Applicants بالـ navbar بدون تحديد وظيفة، نعرضله قائمة وظائفه ليختار منها
if ($job_id <= 0) {
    $stmt = mysqli_prepare($conc, "SELECT id, title, status FROM jobs WHERE employer_id=? ORDER BY id DESC");
    mysqli_stmt_bind_param($stmt, "i", $emp_id);
    mysqli_stmt_execute($stmt);
    $myJobs = mysqli_stmt_get_result($stmt);
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Select a Job</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="../css/styles.css">
    </head>
    <body>
    <div class="container py-5">
        <h2 class="mb-4">Select a Job to View Applicants</h2>
        <div class="list-group">
            <?php while ($j = mysqli_fetch_assoc($myJobs)): ?>
                <a href="applicants.php?job=<?= $j['id'] ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                    <?= htmlspecialchars($j['title']) ?>
                    <span class="badge bg-secondary"><?= htmlspecialchars($j['status']) ?></span>
                </a>
            <?php endwhile; ?>
            <?php if (mysqli_num_rows($myJobs) === 0): ?>
                <p class="text-muted">You haven't posted any jobs yet.</p>
            <?php endif; ?>
        </div>
    </div>
    </body>
    </html>
    <?php
    exit();
}

// تأكد إن الوظيفة تخص هذا الـ employer (منع IDOR)
$stmt = mysqli_prepare($conc, "SELECT * FROM jobs WHERE id=? AND employer_id=?");
mysqli_stmt_bind_param($stmt, "ii", $job_id, $emp_id);
mysqli_stmt_execute($stmt);
$job = mysqli_stmt_get_result($stmt)->fetch_assoc();
if (!$job) {
    die("Job not found or not authorized.");
}

// جلب المتقدمين مع بيانات seekers + users
$stmt = mysqli_prepare($conc, "
    SELECT a.id AS application_id, a.status AS app_status, a.applied_at,
           u.id AS seeker_user_id, u.name, u.email,
           s.category_id, s.skills, s.education, s.years_of_experience, s.resume
    FROM applications a
    JOIN users u ON u.id = a.seeker_id
    LEFT JOIN seekers s ON s.user_id = a.seeker_id
    WHERE a.job_id = ?
    ORDER BY a.applied_at DESC
");
mysqli_stmt_bind_param($stmt, "i", $job_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$applicants = [];
while ($row = mysqli_fetch_assoc($result)) {
    $row['match'] = calculateMatchScore($row, $job);
    $applicants[] = $row;
}

// ترتيب المتقدمين حسب نسبة التطابق (الأعلى أولاً)
usort($applicants, fn($a, $b) => $b['match']['total'] <=> $a['match']['total']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Applicants - <?= htmlspecialchars($job['title']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
<div class="container py-5">
    <h2 class="mb-1">Applicants</h2>
    <p class="text-muted mb-4">For job: <strong><?= htmlspecialchars($job['title']) ?></strong> · sorted by best match</p>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Match</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Experience</th>
                    <th>Education</th>
                    <th>Skills Matched</th>
                    <th>Applied At</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($applicants as $a): ?>
                <tr>
                    <td>
                        <span class="badge <?= matchScoreBadgeClass($a['match']['total']) ?>">
                            <?= $a['match']['total'] ?>%
                        </span>
                    </td>
                    <td><?= htmlspecialchars($a['name']) ?></td>
                    <td><?= htmlspecialchars($a['email']) ?></td>
                    <td><?= (int)($a['years_of_experience'] ?? 0) ?> yrs</td>
                    <td><?= htmlspecialchars($a['education'] ?? '-') ?></td>
                    <td><?= $a['match']['matched_skills_count'] ?>/<?= $a['match']['required_skills_count'] ?></td>
                    <td><?= htmlspecialchars($a['applied_at']) ?></td>
                    <td><?= htmlspecialchars($a['app_status']) ?></td>
                    <td>
                        <?php if (!empty($a['resume'])): ?>
                            <a href="../uploads/resumes/<?= urlencode($a['resume']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary">Resume</a>
                        <?php endif; ?>
                        <a href="schedule-interview.php?application=<?= $a['application_id'] ?>" class="btn btn-sm btn-primary">
                            Schedule Interview
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($applicants)): ?>
                <tr><td colspan="9" class="text-center text-muted">No applicants yet.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>