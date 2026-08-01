<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }
include('db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $patient_id = $_POST['patient_id'];
    $doctor_id = $_POST['doctor_id'];
    $date = $_POST['date'];
    
    $conn->query("INSERT INTO appointments (patient_id, doctor_id, date, status) VALUES ('$patient_id', '$doctor_id', '$date', 'Scheduled')");
    header("Location: manage_appointments.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>New Appointment - Medi Lanka HMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f4f6f9; }</style>
</head>
<body class="p-5">
    <div class="container" style="max-width: 600px;">
        <div class="card shadow-sm border-0 p-4 rounded-4">
            <h4 class="fw-bold mb-3 text-dark">Schedule New Appointment</h4>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Patient ID / Name</label>
                    <input type="text" name="patient_id" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Doctor ID / Name</label>
                    <input type="text" name="doctor_id" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Appointment Date & Time</label>
                    <input type="datetime-local" name="date" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100 fw-semibold py-2">Confirm Appointment</button>
                <a href="manage_appointments.php" class="btn btn-outline-secondary w-100 mt-2 py-2">Back</a>
            </form>
        </div>
    </div>
</body>
</html>