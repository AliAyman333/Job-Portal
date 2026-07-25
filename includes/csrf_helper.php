<?php
/**
 * csrf_helper.php
 * حماية بسيطة من هجمات CSRF لكل الفورمات التي تستخدم POST.
 *
 * الاستخدام داخل أي فورم:
 *   <form method="post">
 *       <?php csrf_field(); ?>
 *       ...
 *   </form>
 *
 * وفي أعلى معالج الـ POST:
 *   if (isset($_POST['btnsave'])) {
 *       csrf_verify();
 *       ...
 *   }
 */

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/** يطبع حقل hidden جاهز للحقن داخل أي <form> */
function csrf_field(): void
{
    echo '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token()) . '">';
}

/** يتحقق من صحة التوكن القادم من POST، ويوقف التنفيذ لو غير صالح */
function csrf_verify(): void
{
    $sent = $_POST['csrf_token'] ?? '';
    if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $sent)) {
        http_response_code(403);
        die('Invalid CSRF token. Please refresh the page and try again.');
    }
}
