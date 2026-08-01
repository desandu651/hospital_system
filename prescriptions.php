<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

include('db.php');

$message = "";

// Auto-create prescriptions table if it does not exist
$create_table = "
    CREATE TABLE IF NOT EXISTS prescriptions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        appointment_id INT NOT NULL,
        diagnosis TEXT NOT NULL,
        medicines TEXT NOT NULL,
        notes TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE
    );
";
$conn->query($create_table);

// Delete prescription record
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $sql = "DELETE FROM prescriptions WHERE id = $delete_id";

    if ($conn->query($sql) === TRUE) {
        $message = "<div class='alert alert-warning'>Prescription deleted successfully!</div>";
    } else {
        $message = "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
    }
}

// Add new prescription
if (isset($_POST['add_prescription'])) {
    $appointment_id = $_POST['appointment_id'];
    $diagnosis = $conn->real_escape_string($_POST['diagnosis']);
    $medicines = $conn->real_escape_string($_POST['medicines']);
    $notes = $conn->real_escape_string($_POST['notes']);

    $sql = "INSERT INTO prescriptions (appointment_id, diagnosis, medicines, notes) 
            VALUES ('$appointment_id', '$diagnosis', '$medicines', '$notes')";

    if ($conn->query($sql) === TRUE) {
        // Automatically mark the appointment status as Completed
        $conn->query("UPDATE appointments SET status = 'Completed' WHERE id = '$appointment_id'");
        $message = "<div class='alert alert-success'>Prescription added successfully and Appointment marked as Completed!</div>";
    } else {
        $message = "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
    }
}

// Fetch appointments for dropdown menu
$appointments_list = $conn->query("
    SELECT a.id, p.full_name AS patient_name, d.full_name AS doctor_name, a.appointment_date 
    FROM appointments a
    JOIN patients p ON a.patient_id = p.id
    JOIN doctors d ON a.doctor_id = d.id
    ORDER BY a.id DESC
");

// Fetch existing prescriptions list
$prescriptions_list = $conn->query("
    SELECT pr.id, pr.diagnosis, pr.medicines, pr.notes, pr.created_at,
           p.full_name AS patient_name, d.full_name AS doctor_name
    FROM prescriptions pr
    JOIN appointments a ON pr.appointment_id = a.id
    JOIN patients p ON a.patient_id = p.id
    JOIN doctors d ON a.doctor_id = d.id
    ORDER BY pr.id DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Prescriptions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <!-- Navigation Bar -->
    <nav class="navbar navbar-dark bg-success mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="dashboard.php">🏥 Hospital Management System</a>
            <a href="dashboard.php" class="btn btn-light btn-sm fw-bold">⬅ Back to Dashboard</a>
        </div>
    </nav>

    <div class="container">
        <?php echo $message; ?>

        <div class="row">
            <!-- Form Section -->
            <div class="col-md-4 mb-4">
                <div class="card shadow p-3">
                    <h4 class="text-success mb-3">Add Prescription</h4>
                    <form action="prescriptions.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Select Appointment</label>
                            <select name="appointment_id" class="form-select" required>
                                <option value="">-- Select Appointment --</option>
                                <?php while($app = $appointments_list->fetch_assoc()): ?>
                                    <option value="<?php echo $app['id']; ?>">
                                        #<?php echo $app['id']; ?> - <?php echo $app['patient_name']; ?> (Dr. <?php echo $app['doctor_name']; ?>)
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Diagnosis / Illness</label>
                            <input type="text" name="diagnosis" class="form-control" placeholder="e.g. Fever & Cough" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Prescribed Medicines</label>
                            <textarea name="medicines" class="form-control" rows="3" placeholder="e.g. Paracetamol 500mg - 1 tab 3x daily" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Additional Advice / Notes</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="e.g. Rest for 3 days, drink warm water"></textarea>
                        </div>

                        <button type="submit" name="add_prescription" class="btn btn-success w-100 fw-bold">Save Prescription</button>
                    </form>
                </div>
            </div>

            <!-- Table Section -->
            <div class="col-md-8">
                <div class="card shadow p-3">
                    <h4 class="text-secondary mb-3">Prescription History</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Patient</th>
                                    <th>Doctor</th>
                                    <th>Diagnosis</th>
                                    <th>Medicines</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($prescriptions_list && $prescriptions_list->num_rows > 0): ?>
                                    <?php while($row = $prescriptions_list->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $row['id']; ?></td>
                                            <td><?php echo $row['patient_name']; ?></td>
                                            <td><?php echo $row['doctor_name']; ?></td>
                                            <td><span class="badge bg-info text-dark"><?php echo $row['diagnosis']; ?></span></td>
                                            <td><small><?php echo nl2br($row['medicines']); ?></small></td>
                                            <td>
                                                <a href="prescriptions.php?delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this prescription?');">Delete</a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center">No prescriptions recorded yet.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>