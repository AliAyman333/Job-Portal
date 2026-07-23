<?php
require("../config/conn.php");
session_start();
if ($_SESSION['role'] != "seeker") {
    header("Location: ../auth/login.php");
    exit();
}

// قراءة قيمة البحث من الرابط
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$searchSql = '';
$searchParams = [];
if ($search !== '') {
    $searchSql = " AND (j.title LIKE ? OR j.location LIKE ?)";
    $searchParams[] = "%$search%";
    $searchParams[] = "%$search%";

    // تخزين عملية البحث في سجل البحث
    $seeker_id = $_SESSION['user_id'];
    $insertSearch = mysqli_prepare($conc, "INSERT INTO search_history (seeker_id, search_term) VALUES (?, ?)");
    mysqli_stmt_bind_param($insertSearch, "is", $seeker_id, $search);
    mysqli_stmt_execute($insertSearch);
    mysqli_stmt_close($insertSearch);

    // الاحتفاظ بآخر 3 عمليات بحث فقط لهذا المستخدم
    $deleteSearch = mysqli_prepare($conc, "DELETE FROM search_history 
        WHERE seeker_id=? 
        AND id NOT IN (
            SELECT id FROM (
                SELECT id FROM search_history 
                WHERE seeker_id=? 
                ORDER BY created_at DESC LIMIT 3
            ) AS keep_ids
        )");
    mysqli_stmt_bind_param($deleteSearch, "ii", $seeker_id, $seeker_id);
    mysqli_stmt_execute($deleteSearch);
    mysqli_stmt_close($deleteSearch);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Jobs</title>
    <link rel="icon" type="image/svg+xml" href="../css/favicon.svg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/styles.css">
</head>

<body>
    <!-- Navigation -->
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

    <!-- Main Content -->
    <div class="container py-5">
        <h2 class="mb-4">Available Job Listings</h2>

        <!-- مربع البحث -->
        <form method="GET" class="mb-4 d-flex">
            <input type="text" name="search" class="form-control me-2"
                   placeholder="ابحث عن وظيفة أو مكان..."
                   value="<?= htmlspecialchars($search) ?>">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i> بحث
            </button>
            <?php if ($search !== ''): ?>
                <a href="jobs.php" class="btn btn-outline-secondary ms-2">
                    <i class="fas fa-times"></i> إلغاء
                </a>
            <?php endif; ?>
        </form>

        <div class="row">
            <?php
            if ($search !== '') {
                $stmt = mysqli_prepare($conc, "SELECT j.*, 
                    COALESCE(c.company_name, u.name) as company_name 
                    FROM jobs j 
                    LEFT JOIN companies c ON j.employer_id=c.user_id
                    LEFT JOIN users u ON j.employer_id=u.id
                    WHERE j.status='Open' AND (j.title LIKE ? OR j.location LIKE ?) ORDER BY j.id DESC");
                $searchParam = "%$search%";
                mysqli_stmt_bind_param($stmt, "ss", $searchParam, $searchParam);
                mysqli_stmt_execute($stmt);
                $q = mysqli_stmt_get_result($stmt);
            } else {
                $q = mysqli_query($conc, "SELECT j.*, 
                    COALESCE(c.company_name, u.name) as company_name 
                    FROM jobs j 
                    LEFT JOIN companies c ON j.employer_id=c.user_id
                    LEFT JOIN users u ON j.employer_id=u.id
                    WHERE j.status='Open' ORDER BY j.id DESC");
            }

            if ($q && mysqli_num_rows($q) > 0) {
                while ($r = mysqli_fetch_assoc($q)) {
                    $company = $r['company_name'] ? htmlspecialchars($r['company_name']) : 'Company';
                    ?>
                    <div class="col-md-6 mb-4">
                        <div class="card job-card">
                            <div class="card-body">
                                <h5 class="job-title"><?= htmlspecialchars($r['title']) ?></h5>
                                <p class="job-company"><?= $company ?></p>
                                
                                <div class="job-meta mb-3">
                                    <span class="job-meta-item">📍 <?= htmlspecialchars($r['location']) ?></span>
                                    <span class="job-salary">💰 <?= htmlspecialchars($r['salary']) ?></span>
                                </div>

                                <p class="text-muted"><?= substr(htmlspecialchars($r['description']), 0, 100) ?>...</p>

                                <a href="job-details.php?job=<?= $r['id'] ?>" class="btn btn-primary btn-sm"><i class="fas fa-eye"></i> View Details</a>
                                <a href="apply-job.php?job=<?= $r['id'] ?>" class="btn btn-success btn-sm"><i class="fas fa-paper-plane"></i> Apply</a>
                            </div>
                        </div>
                    </div>
                <?php
                }
            } else {
                echo '<div class="alert alert-info alert-lg"><i class="fas fa-info-circle"></i> No jobs available at the moment.</div>';
            }
            ?>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>