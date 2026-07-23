<?php
/**
 * سكريبت تحويل يُشغَّل مرة واحدة فقط.
 * يشفّر أي باسورد لسه مخزّن كنص عادي باستخدام password_hash().
 * آمن تشغّله أكتر من مرة: أي باسورد متشفر بالفعل (bcrypt، يبدأ بـ $2y$) بيتجاهله.
 *
 * شغّله مرة من المتصفح أو من الـ CLI، وبعدين امسحه.
 */
require("conn.php");

$result = mysqli_query($conc, "SELECT id, password FROM users");
$updated = 0;
$skipped = 0;

while ($row = mysqli_fetch_assoc($result)) {
    $id = $row['id'];
    $storedPassword = $row['password'];

    if (password_get_info($storedPassword)['algo'] !== null) {
        $skipped++;
        continue;
    }

    $hashed = password_hash($storedPassword, PASSWORD_DEFAULT);
    $stmt = mysqli_prepare($conc, "UPDATE users SET password = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "si", $hashed, $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    $updated++;
}

echo "Done. Hashed: $updated user(s). Already hashed (skipped): $skipped user(s).";