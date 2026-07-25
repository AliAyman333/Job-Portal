<?php
require("../config/conn.php");
require("../includes/csrf_helper.php");
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != "employer") {
    header("Location: ../auth/login.php");
    exit();
}
$emp_id = $_SESSION['user_id'];

$application_id = intval($_GET['application'] ?? 0);
// جلب بيانات الطلب + تأكد إن الوظيفة تخص هذا الـ employer (منع IDOR)
$stmt = mysqli_prepare($conc, "
    SELECT a.id AS application_id, a.seeker_id, j.id AS job_id, j.title, j.employer_id, u.name AS seeker_name
    FROM applications a
    JOIN jobs j ON j.id = a.job_id
    JOIN users u ON u.id = a.seeker_id
    WHERE a.id = ?
");
mysqli_stmt_bind_param($stmt, "i", $application_id);
mysqli_stmt_execute($stmt);
$application = mysqli_stmt_get_result($stmt)->fetch_assoc();

if (!$application || (int)$application['employer_id'] !== $emp_id) {
    die("Not authorized.");
}

$message = '';

if (isset($_POST['btnschedule'])) {
    csrf_verify();

    $date     = $_POST['proposed_date'];
    $time     = $_POST['proposed_time'];
    $location = trim($_POST['location_or_link']);
    $notes    = trim($_POST['notes']);

    $stmt = mysqli_prepare($conc, "
        INSERT INTO interviews(application_id, employer_id, seeker_id, proposed_date, proposed_time, location_or_link, notes, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, 'Proposed')
    ");
    mysqli_stmt_bind_param($stmt, "iiissss",
        $application_id, $emp_id, $application['seeker_id'],
        $date, $time, $location, $notes
    );

    if (mysqli_stmt_execute($stmt)) {
        $message = "Interview proposal sent to " . htmlspecialchars($application['seeker_name']) . ".";
    } else {
        $message = "Error: " . mysqli_error($conc);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Schedule Interview</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-5">
    <div class="col-md-6 offset-md-3">
        <h3 class="mb-1">Schedule Interview</h3>
        <p class="text-muted mb-4">
            With <strong><?= htmlspecialchars($application['seeker_name']) ?></strong>
            for <strong><?= htmlspecialchars($application['title']) ?></strong>
        </p>

        <?php if ($message): ?>
            <div class="alert alert-info"><?= $message ?></div>
        <?php endif; ?>

        <form method="post">
            <?php csrf_field(); ?>
            <input type="hidden" name="application_id" value="<?= $application_id ?>">

            <div class="mb-3">
                <label class="form-label">Proposed Date</label>
                <input type="date" name="proposed_date" class="form-control" required min="<?= date('Y-m-d') ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Proposed Time</label>
                <input type="time" name="proposed_time" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Location / Meeting Link</label>
                <input type="text" name="location_or_link" class="form-control" placeholder="Office address or Zoom/Meet link">
            </div>
            <div class="mb-3">
                <label class="form-label">Notes (optional)</label>
                <textarea name="notes" class="form-control" rows="3"></textarea>
            </div>
            <button type="submit" name="btnschedule" class="btn btn-primary w-100">Send Proposal</button>
        </form>
    </div>
</div>
</body>
</html>
