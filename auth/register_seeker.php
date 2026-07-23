<?php
require("../config/conn.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Seeker Register - Job Portal</title>
    <link rel="icon" type="image/svg+xml" href="../css/favicon.svg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
                    <li class="nav-item"><a class="nav-link" href="../index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="../about.php">About</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Register Section -->
    <div class="container">
        <div class="row justify-content-center my-5">
            <div class="col-md-6">
                <div class="card form-card">
                    <div class="card-body p-5">
                        <h2 class="text-center mb-4">Register as Job Seeker</h2>

                        <form method="post">
                            <div class="form-group mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="name" class="form-control" placeholder="Enter your full name" required>
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label">Email Address</label>
                                <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
                            </div>

                            <div class="form-group mb-4">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" placeholder="Create a password" required>
                            </div>

                            <button type="submit" name="btnreg" class="btn btn-success btn-lg w-100">Register</button>
                        </form>

                        <hr class="my-4">

                        <p class="text-center">Already have an account?</p>
                        <a href="login.php" class="btn btn-outline-primary w-100">Login Here</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <?php
if (isset($_POST['btnreg'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $pass = $_POST['password'];
    $error = '';

    if (empty($name) || empty($email) || empty($pass)) {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($pass) < 8) {
        $error = 'Password must be at least 8 characters long.';
    } else {
        $checkStmt = mysqli_prepare($conc, "SELECT id FROM users WHERE email = ?");
        mysqli_stmt_bind_param($checkStmt, "s", $email);
        mysqli_stmt_execute($checkStmt);
        $checkResult = mysqli_stmt_get_result($checkStmt);

        if (mysqli_num_rows($checkResult) > 0) {
            $error = 'This email is already registered.';
        } else {
            $hashedPass = password_hash($pass, PASSWORD_DEFAULT);
            $stmt = mysqli_prepare($conc, "INSERT INTO users(role,name,email,password) VALUES('seeker',?,?,?)");
            mysqli_stmt_bind_param($stmt, "sss", $name, $email, $hashedPass);
            mysqli_stmt_execute($stmt);

            if (mysqli_stmt_affected_rows($stmt) > 0) {
                echo "<script>alert('Registered successfully.');window.location='login.php';</script>";
                exit();
            }

            $error = 'Registration failed. Please try again.';
        }
    }

    if (!empty($error)) {
        echo "<script>alert(" . json_encode($error) . ");</script>";
    }
}
?>