<?php
require("../config/conn.php");
require("../includes/csrf_helper.php");
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != "seeker") {
    header("Location: ../auth/login.php");
    exit();
}
$seeker_id = $_SESSION['user_id'];

// معالجة رد الباحث (تأكيد / رفض) - سطر واحد فقط، محمي بـ CSRF وبفلترة seeker_id
if (isset($_POST['btnrespond'])) {
    csrf_verify();
    $interview_id = intval($_POST['interview_id']);
    $new_status = ($_POST['action'] === 'confirm') ? 'Confirmed' : 'Declined';

    $stmt = mysqli_prepare($conc, "UPDATE interviews SET status=? WHERE id=? AND seeker_id=?");
    mysqli_stmt_bind_param($stmt, "sii", $new_status, $interview_id, $seeker_id);
    mysqli_stmt_execute($stmt);

    header("Location: my-interviews.php");
    exit();
}

$stmt = mysqli_prepare($conc, "
    SELECT i.*, j.title AS job_title, c.company_name
    FROM interviews i
    JOIN applications a ON a.id = i.application_id
    JOIN jobs j ON j.id = a.job_id
    LEFT JOIN companies c ON c.user_id = i.employer_id
    WHERE i.seeker_id = ?
    ORDER BY i.created_at DESC
");
mysqli_stmt_bind_param($stmt, "i", $seeker_id);
mysqli_stmt_execute($stmt);
$interviews = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Interviews</title>
 <link rel="icon" type="image/svg+xml" href="../css/favicon.svg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css"></head>
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
    <h2 class="mb-4">My Interview Invitations</h2>

    <?php while ($iv = mysqli_fetch_assoc($interviews)): ?>
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <h5 class="card-title mb-1">
                        <?= htmlspecialchars($iv['job_title']) ?> —
                        <?= htmlspecialchars($iv['company_name'] ?? 'Company') ?>
                    </h5>
                    <span class="badge bg-secondary"><?= htmlspecialchars($iv['status']) ?></span>
                </div>
                <p class="mb-1"><strong>Date:</strong> <?= htmlspecialchars($iv['proposed_date']) ?> at <?= htmlspecialchars($iv['proposed_time']) ?></p>
                <?php if ($iv['location_or_link']): ?>
                    <p class="mb-1"><strong>Where:</strong> <?= htmlspecialchars($iv['location_or_link']) ?></p>
                <?php endif; ?>
                <?php if ($iv['notes']): ?>
                    <p class="mb-2"><strong>Notes:</strong> <?= htmlspecialchars($iv['notes']) ?></p>
                <?php endif; ?>

                <?php if ($iv['status'] === 'Proposed'): ?>
                    <form method="post" class="d-inline">
                        <?php csrf_field(); ?>
                        <input type="hidden" name="interview_id" value="<?= $iv['id'] ?>">
                        <input type="hidden" name="action" value="confirm">
                        <button type="submit" name="btnrespond" class="btn btn-sm btn-success">Confirm</button>
                    </form>
                    <form method="post" class="d-inline">
                        <?php csrf_field(); ?>
                        <input type="hidden" name="interview_id" value="<?= $iv['id'] ?>">
                        <input type="hidden" name="action" value="decline">
                        <button type="submit" name="btnrespond" class="btn btn-sm btn-outline-danger">Decline</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    <?php endwhile; ?>

    <?php if (mysqli_num_rows($interviews) === 0): ?>
        <p class="text-muted">No interview invitations yet.</p>
    <?php endif; ?>
</div>
</body>
</html>
