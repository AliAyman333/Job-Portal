<?php
function save_resume_upload(array $file, string &$error): ?string
{
    if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        $error = 'Please upload a valid resume file.';
        return null;
    }

    if ($file['size'] > 5 * 1024 * 1024) {
        $error = 'Resume file must be 5MB or smaller.';
        return null;
    }

    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowedExtensions = ['pdf', 'doc', 'docx'];
    if (!in_array($extension, $allowedExtensions, true)) {
        $error = 'Only PDF, DOC, and DOCX files are allowed.';
        return null;
    }

    $uploadDir = __DIR__ . '/../uploads/resumes/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $fileName = bin2hex(random_bytes(16)) . '.' . $extension;
    if (!move_uploaded_file($file['tmp_name'], $uploadDir . $fileName)) {
        $error = 'Unable to save uploaded resume.';
        return null;
    }

    return $fileName;
}
?>
