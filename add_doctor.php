<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }
include('db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $specialization = $_POST['specialization'];
    $phone = $_POST['phone'];
    
    $conn->query("INSERT INTO doctors (name, specialization, phone) VALUES ('$name', '$specialization', '$phone')");
    header("Location: manage_doctors.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Doctor - Medi Lanka HMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f4f6f9; }</style>
</head>
<body class="p-5">
    <div class="container" style="max-width: 600px;">
        <div class="card shadow-sm border-0 p-4 rounded-4">
            <h4 class="fw-bold mb-3 text-dark">Add New Doctor Staff</h4>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Doctor Name</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Dr. Perera" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Specialization</label>
                    <input type="text" name="specialization" class="form-control" placeholder="e.g. Cardiologist" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Phone Number</label>
                    <input type="text" name="phone" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100 fw-semibold py-2">Save Doctor</button>
                <a href="manage_doctors.php" class="btn btn-outline-secondary w-100 mt-2 py-2">Back</a>
            </form>
        </div>
    </div>
</body>
</html>