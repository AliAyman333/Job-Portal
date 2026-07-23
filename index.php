<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Portal</title>
    <link rel="icon" type="image/svg+xml" href="css/favicon.svg">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom Styles -->
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <img src="css/logo.svg" alt="JobPortal" class="brand-logo">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="about.php">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="auth/login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="auth/register_seeker.php">Job Seeker Register</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="auth/register_employer.php">Employer Register</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero py-5">
        <div class="container text-center">
            <h1 class="display-4">Find Your Dream Job</h1>
            <p class="lead">Connect with top employers and launch your career</p>
            <div class="d-flex gap-2 justify-content-center">
                <a href="auth/register_seeker.php" class="btn btn-light btn-lg">Find Jobs</a>
                <a href="auth/register_employer.php" class="btn btn-outline-light btn-lg">Post Job</a>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <div class="container py-5">
        <h2 class="text-center mb-5">Welcome to Job Portal System</h2>
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">For Job Seekers</h5>
                        <p class="card-text">Browse thousands of job opportunities and apply to your dream position.</p>
                        <a href="auth/register_seeker.php" class="btn btn-primary">Get Started</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">For Employers</h5>
                        <p class="card-text">Post job openings and find qualified candidates for your organization.</p>
                        <a href="auth/register_employer.php" class="btn btn-primary">Post Jobs</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Already a Member?</h5>
                        <p class="card-text">Sign in to your account to access your dashboard and applications.</p>
                        <a href="auth/login.php" class="btn btn-primary">Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
