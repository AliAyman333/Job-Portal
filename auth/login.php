<?php
require("../config/conn.php");
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Job Portal</title>
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

    <!-- Login Section -->
    <div class="container">
        <div class="row justify-content-center my-5">
            <div class="col-md-6">
                <div class="card form-card">
                    <div class="card-body p-5">
                        <h2 class="text-center mb-4">Login to Your Account</h2>

                        <form method="post">
                            <div class="form-group mb-3">
                                <label class="form-label">Email Address</label>
                                <input type="email" name="email" class="form-control" placeholder="Enter your email"
                                    required>
                            </div>

                            <div class="form-group mb-4">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control"
                                    placeholder="Enter your password" required>
                            </div>

                            <button type="submit" name="btnlogin" class="btn btn-primary btn-lg w-100">Login</button>
                        </form>

                        <hr class="my-4">

                        <p class="text-center">Don't have an account?</p>
                        <a href="register_seeker.php" class="btn btn-outline-primary w-100 mb-2">Register as Job
                            Seeker</a>
                        <a href="register_employer.php" class="btn btn-outline-secondary w-100">Register as Employer</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <?php
    // Check if login form was submitted
    if (isset($_POST["btnlogin"])) {
        // Get email and password from form
        $email = trim($_POST["email"]);
        $password = $_POST["password"];

        // Validate input
        if (empty($email) || empty($password)) {
            $error = "Email and Password are required!";
            echo "<script>alert(" . json_encode($error) . ");</script>";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Please enter a valid email address.";
            echo "<script>alert(" . json_encode($error) . ");</script>";
        } else {
            // Check user credentials - try all roles
            $roles = array('admin', 'employer', 'seeker');
            $userFound = false;
            $userData = null;
            $userRole = null;

            foreach ($roles as $role) {
                // Use prepared statement to prevent SQL injection
                $credentialQuery = "SELECT id, name, email, role, password FROM users WHERE email=? AND role=? AND status=1";
                $stmt = mysqli_prepare($conc, $credentialQuery);

                if (!$stmt) {
                    echo "Prepare failed: " . mysqli_error($conc);
                    exit;
                }

                // Bind parameters correctly - pass by reference
                $email_ref = $email;
                $role_ref = $role;
                mysqli_stmt_bind_param($stmt, "ss", $email_ref, $role_ref);
                mysqli_stmt_execute($stmt);
                $queryResult = mysqli_stmt_get_result($stmt);

                // If user exists, check their password
                if ($queryResult->num_rows > 0) {
                    $userData = $queryResult->fetch_assoc();
                    if (password_verify($password, $userData["password"])) {
                        $_SESSION["user_id"] = $userData["id"];
                        $_SESSION["username"] = $userData["name"];
                        $_SESSION["role"] = $userData["role"];

                        $userFound = true;
                        $userRole = $userData["role"];

                        // Log activity
                        $user_id = $userData["id"];
                        $action = "Logged in as " . ucfirst($userRole);
                        $logStmt = mysqli_prepare($conc, "INSERT INTO activity_log (user_id, action) VALUES (?, ?)");
                        mysqli_stmt_bind_param($logStmt, "is", $user_id, $action);
                        mysqli_stmt_execute($logStmt);
                        mysqli_stmt_close($logStmt);

                        mysqli_stmt_close($stmt);
                        break;
                    }
                }
                mysqli_stmt_close($stmt);
            }

            // If user found and password verified, redirect based on role
            if ($userFound) {
                // Redirect to Admin Dashboard
                if ($userRole === "admin") {
                    echo "<script>window.location.href='../admin/dashboard.php';</script>";
                }
                // Redirect to Employer Dashboard
                elseif ($userRole === "employer") {
                    echo "<script>window.location.href='../employer/dashboard.php';</script>";
                }
                // Redirect to Job Seeker Dashboard
                elseif ($userRole === "seeker") {
                    echo "<script>window.location.href='../seeker/dashboard.php';</script>";
                }
                exit();
            }
            // If user not found or password incorrect, show error
            else {
                $error = "Invalid Email or Password!";
                echo "<script>alert('$error');</script>";
            }
        }
    } else {
        $error = null;
    }
    ?>