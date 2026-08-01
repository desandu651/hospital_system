<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }
include('db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $doctor_id = $_POST['doctor_id'];
    $day = $_POST['day'];
    $conn->query("INSERT INTO schedules (doctor_id, day) VALUES ('$doctor_id', '$day')");
    header("Location: doctor_schedules.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Schedule - Medi Lanka HMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-5 bg-light">
    <div class="container" style="max-width: 600px;">
        <div class="card shadow-sm border-0 p-4 rounded-4">
            <h4 class="fw-bold mb-3">Add Doctor Schedule</h4>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Doctor ID / Name</label>
                    <input type="text" name="doctor_id" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Day / Time Slot</label>
                    <input type="text" name="day" class="form-control" placeholder="e.g. Monday 9:00 AM - 1:00 PM" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Save Schedule</button>
                <a href="doctor_schedules.php" class="btn btn-secondary w-100 mt-2">Back</a>
            </form>
        </div>
    </div>
</body>
</html>