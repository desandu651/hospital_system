<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }
include('db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $patient_id = $_POST['patient_id'];
    $amount = $_POST['amount'];
    
    $conn->query("INSERT INTO billing (patient_id, amount) VALUES ('$patient_id', '$amount')");
    header("Location: billing.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Invoice - Medi Lanka HMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-5">
    <div class="container" style="max-width: 600px;">
        <div class="card shadow-sm border-0 p-4 rounded-4">
            <h4 class="fw-bold mb-3">Create New Invoice</h4>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Patient ID</label>
                    <input type="text" name="patient_id" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Amount (Rs.)</label>
                    <input type="number" step="0.01" name="amount" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Save Invoice</button>
                <a href="billing.php" class="btn btn-secondary w-100 mt-2">Back</a>
            </form>
        </div>
    </div>
</body>
</html>