<?php
$conc = mysqli_connect("localhost", "root", "", "job_portal");

if (!$conc) {
    die("Database Connection Failed");
}

mysqli_set_charset($conc, "utf8mb4");
?>
