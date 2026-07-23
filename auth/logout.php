<?php
session_start();
session_destroy();
header("Location: login.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout</title>
    <link rel="icon" type="image/svg+xml" href="../css/favicon.svg">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <h1>You have been logged out</h1>
    <p><a href="login.php">Click here</a> to log in again.</p>
</body>
</html>
