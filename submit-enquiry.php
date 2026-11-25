<?php
include "config.php";
header('Content-Type: application/json');


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name  = mysqli_real_escape_string($con, trim($_POST['product_name'] ?? ''));
    $customer_name = mysqli_real_escape_string($con, trim($_POST['customer_name'] ?? ''));
    $company_name  = mysqli_real_escape_string($con, trim($_POST['company_name'] ?? ''));
    $email         = mysqli_real_escape_string($con, trim($_POST['email'] ?? ''));
    $phone         = mysqli_real_escape_string($con, trim($_POST['phone'] ?? ''));
    $quantity      = (int) ($_POST['quantity'] ?? 1);
    $message       = mysqli_real_escape_string($con, trim($_POST['message'] ?? ''));

    if ($customer_name === '' || $email === '' || $phone === '') {
        echo json_encode(['status' => 'error', 'message' => 'Please fill all required fields.']);
        exit;
    }

    $sql = "INSERT INTO elso_enquiries (product_name, customer_name, company_name, email, phone, quantity, message, created_at)
            VALUES ('$product_name', '$customer_name', '$company_name', '$email', '$phone', $quantity, '$message', NOW())";

    if (mysqli_query($con, $sql)) {
        echo json_encode(['status' => 'success', 'message' => 'Thank you! Your enquiry has been received successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . mysqli_error($con)]);
    }
    exit;
}