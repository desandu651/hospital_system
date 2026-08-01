<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }
include('db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $patient_id = $_POST['patient_id'];
    $test_name = $_POST['test_name'];
    $conn->query("INSERT INTO laboratory (patient_id, test_name) VALUES ('$patient_id', '$test_name')");
    header("Location: laboratory.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Lab Test - Medi Lanka HMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-5 bg-light">
    <div class="container" style="max-width: 600px;">
        <div class="card shadow-sm border-0 p-4 rounded-4">
            <h4 class="fw-bold mb-3">Add Laboratory Test</h4>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Patient ID</label>
                    <input type="text" name="patient_id" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Test Name / Details</label>
                    <input type="text" name="test_name" class="form-control" placeholder="e.g. Full Blood Count (FBC)" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Save Test</button>
                <a href="laboratory.php" class="btn btn-secondary w-100 mt-2">Back</a>
            </form>
        </div>
    </div>
</body>
</html>