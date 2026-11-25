<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('log_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/config.php";

require_once __DIR__ . "/config.php";

// Redirect with error message
function boqRedirectError($message)
{
    header("Location: upload-boq.php?error=" . urlencode($message));
    exit;
}

// Only POST requests allowed
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: upload-boq.php");
    exit;
}

// Database connection check
if (!isset($con) || !$con) {
    error_log("BOQ Error: Database connection not found.");
    boqRedirectError("Database connection error. Please try again.");
}

// Customer details
$name = trim($_POST['name'] ?? '');
$company_name = trim($_POST['company_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$mobile = trim($_POST['mobile'] ?? '');
$remarks = trim($_POST['remarks'] ?? '');

// Required field validation
if ($name == '' || $email == '' || $mobile == '') {
    boqRedirectError("Please fill all required fields.");
}

// Email validation
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    boqRedirectError("Please enter a valid email address.");
}

// Mobile validation
if (!preg_match('/^[0-9+\-\s()]{7,20}$/', $mobile)) {
    boqRedirectError("Please enter a valid mobile number.");
}

// Check uploaded files
if (
    !isset($_FILES['boq_files']) ||
    !isset($_FILES['boq_files']['name']) ||
    !is_array($_FILES['boq_files']['name']) ||
    empty($_FILES['boq_files']['name'][0])
) {
    boqRedirectError("Please upload at least one BOQ file.");
}

$fileCount = count($_FILES['boq_files']['name']);

if ($fileCount > 10) {
    boqRedirectError("Maximum 10 files are allowed.");
}

// Upload settings
$uploadDir = __DIR__ . "/uploads/boq/";
$uploadPath = "uploads/boq/";

$maxFileSize = 15 * 1024 * 1024;
$maxTotalSize = 100 * 1024 * 1024;

// Allowed file extensions and MIME types
$allowedFiles = [

    'jpg' => ['image/jpeg'],
    'jpeg' => ['image/jpeg'],
    'png' => ['image/png'],
    'webp' => ['image/webp'],

    'pdf' => ['application/pdf'],

    'xls' => [
        'application/vnd.ms-excel',
        'application/x-ole-storage',
        'application/octet-stream'
    ],

    'xlsx' => [
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/zip',
        'application/x-zip',
        'application/octet-stream'
    ],

    'doc' => [
        'application/msword',
        'application/x-ole-storage',
        'application/octet-stream'
    ],

    'docx' => [
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/zip',
        'application/x-zip',
        'application/octet-stream'
    ]
];

// Create upload directory
if (!is_dir($uploadDir)) {

    if (!mkdir($uploadDir, 0755, true)) {
        boqRedirectError("Unable to create upload folder.");
    }
}

// Check folder permission
if (!is_writable($uploadDir)) {
    boqRedirectError("Upload folder permission denied.");
}

// Prepare uploaded files
$filesToUpload = [];
$totalSize = 0;

if (!class_exists('finfo')) {
    boqRedirectError("File validation is not available on server.");
}

$finfo = new finfo(FILEINFO_MIME_TYPE);

for ($i = 0; $i < $fileCount; $i++) {

    $originalName = basename($_FILES['boq_files']['name'][$i]);

    $tmpName = $_FILES['boq_files']['tmp_name'][$i];

    $fileSize = (int) $_FILES['boq_files']['size'][$i];

    $uploadError = $_FILES['boq_files']['error'][$i];

    // Upload error check
    if ($uploadError !== UPLOAD_ERR_OK) {

        if ($uploadError === UPLOAD_ERR_INI_SIZE || $uploadError === UPLOAD_ERR_FORM_SIZE) {
            boqRedirectError("File size is too large: " . $originalName);
        }

        boqRedirectError("File upload failed: " . $originalName);
    }

    if (!is_uploaded_file($tmpName)) {
        boqRedirectError("Invalid uploaded file: " . $originalName);
    }

    // File size validation
    if ($fileSize <= 0 || $fileSize > $maxFileSize) {
        boqRedirectError("Each file must be below 15 MB: " . $originalName);
    }

    // Extension validation
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

    if (!isset($allowedFiles[$extension])) {
        boqRedirectError("File format not allowed: " . $originalName);
    }

    // MIME validation
    $mimeType = $finfo->file($tmpName);

    if (!in_array($mimeType, $allowedFiles[$extension], true)) {
        boqRedirectError("Invalid file content: " . $originalName);
    }

    $totalSize += $fileSize;

    // Unique file name
    $newFileName = bin2hex(random_bytes(16)) . "." . $extension;

    $filesToUpload[] = [
        'original_name' => $originalName,
        'file_name' => $newFileName,
        'tmp_name' => $tmpName,
        'file_size' => $fileSize,
        'mime_type' => $mimeType
    ];
}

// Total upload size validation
if ($totalSize > $maxTotalSize) {
    boqRedirectError("Total upload size cannot exceed 100 MB.");
}

// Start database transaction
$movedFiles = [];
$transactionStarted = false;

try {

    if (!mysqli_begin_transaction($con)) {
        throw new Exception("Unable to start database transaction.");
    }

    $transactionStarted = true;

    // Insert customer / lead details
    $stmt = mysqli_prepare(
        $con,
        "INSERT INTO elso_boq_leads
        (
            name,
            company_name,
            email,
            mobile,
            remarks
        )
        VALUES (?, ?, ?, ?, ?)"
    );

    if (!$stmt) {
        throw new Exception("Unable to prepare lead query: " . mysqli_error($con));
    }

    mysqli_stmt_bind_param(
        $stmt,
        "sssss",
        $name,
        $company_name,
        $email,
        $mobile,
        $remarks
    );

    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Unable to save lead details: " . mysqli_stmt_error($stmt));
    }

    $leadId = mysqli_insert_id($con);

    mysqli_stmt_close($stmt);

    // Generate lead number
    $leadCode = "ELSO-BOQ-" . str_pad($leadId, 6, "0", STR_PAD_LEFT);

    $updateLead = mysqli_prepare(
        $con,
        "UPDATE elso_boq_leads SET lead_code = ? WHERE id = ?"
    );

    if (!$updateLead) {
        throw new Exception("Unable to prepare lead number query.");
    }

    mysqli_stmt_bind_param(
        $updateLead,
        "si",
        $leadCode,
        $leadId
    );

    if (!mysqli_stmt_execute($updateLead)) {
        throw new Exception("Unable to generate lead number.");
    }

    mysqli_stmt_close($updateLead);

    // Prepare file insert query
    $fileStmt = mysqli_prepare(
        $con,
        "INSERT INTO elso_boq_files
        (
            lead_id,
            original_name,
            file_name,
            file_path,
            file_type,
            file_size
        )
        VALUES (?, ?, ?, ?, ?, ?)"
    );

    if (!$fileStmt) {
        throw new Exception("Unable to prepare file query.");
    }

    // Upload and save file details
    foreach ($filesToUpload as $file) {

        $destination = $uploadDir . $file['file_name'];

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new Exception("Unable to save file: " . $file['original_name']);
        }

        $movedFiles[] = $destination;

        $relativePath = $uploadPath . $file['file_name'];

        mysqli_stmt_bind_param(
            $fileStmt,
            "issssi",
            $leadId,
            $file['original_name'],
            $file['file_name'],
            $relativePath,
            $file['mime_type'],
            $file['file_size']
        );

        if (!mysqli_stmt_execute($fileStmt)) {
            throw new Exception("Unable to save file details: " . mysqli_stmt_error($fileStmt));
        }
    }

    mysqli_stmt_close($fileStmt);

    // Commit database transaction
    if (!mysqli_commit($con)) {
        throw new Exception("Unable to complete database transaction.");
    }

    $transactionStarted = false;

    // Success redirect
    header(
        "Location: upload-boq.php?status=success"
    );
    exit;

} catch (Throwable $e) {

    // Rollback database
    if ($transactionStarted) {
        mysqli_rollback($con);
    }

    // Remove uploaded files if database operation fails
    foreach ($movedFiles as $filePath) {

        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    // Log actual error
    error_log("BOQ Upload Error: " . $e->getMessage());

    boqRedirectError(
        "Something went wrong while submitting your BOQ. Please try again."
    );
}

