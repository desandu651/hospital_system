<?php
session_start();
if (!isset($_SESSION['user_id'])) { 
    header("Location: index.php"); 
    exit(); 
}
include('db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ward_name = $_POST['ward_name'];
    $bed_count = $_POST['bed_count'];
    
    $conn->query("INSERT INTO wards (ward_name, bed_count) VALUES ('$ward_name', '$bed_count')");
    header("Location: wards.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Ward - Medi Lanka HMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f4f6f9; }
    </style>
</head>
<body class="p-5">
    <div class="container" style="max-width: 600px;">
        <div class="card shadow-sm border-0 p-4 rounded-4">
            <h4 class="fw-bold mb-3 text-dark">Add New Hospital Ward</h4>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Ward Name / Number</label>
                    <input type="text" name="ward_name" class="form-control" placeholder="e.g. Surgical Ward - 01" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Bed Count</label>
                    <input type="number" name="bed_count" class="form-control" placeholder="e.g. 20" required>
                </div>
                <button type="submit" class="btn btn-primary w-100 fw-semibold py-2">Save Ward</button>
                <a href="wards.php" class="btn btn-outline-secondary w-100 mt-2 py-2">Back to Wards</a>
            </form>
        </div>
    </div>
</body>
</html>