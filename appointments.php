<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

include('db.php');

$message = "";

// Delete appointment record
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $sql = "DELETE FROM appointments WHERE id = $delete_id";

    if ($conn->query($sql) === TRUE) {
        $message = "<div class='alert alert-warning'>Appointment deleted successfully!</div>";
    } else {
        $message = "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
    }
}

// Add new appointment
if (isset($_POST['add_appointment'])) {
    $patient_id = $_POST['patient_id'];
    $doctor_id = $_POST['doctor_id'];
    $appointment_date = $_POST['appointment_date'];

    $sql = "INSERT INTO appointments (patient_id, doctor_id, appointment_date, status) 
            VALUES ('$patient_id', '$doctor_id', '$appointment_date', 'Pending')";

    if ($conn->query($sql) === TRUE) {
        $message = "<div class='alert alert-success'>Appointment booked successfully!</div>";

        // Include notification functions file
        include('notifications.php');

        // Fetch patient and doctor details for notifications
        $patient_info_query = $conn->query("SELECT full_name, email, phone FROM patients WHERE id = $patient_id");
        $patient_data = $patient_info_query->fetch_assoc();

        $doctor_info_query = $conn->query("SELECT full_name FROM doctors WHERE id = $doctor_id");
        $doctor_data = $doctor_info_query->fetch_assoc();

        if ($patient_data) {
            $patient_name = $patient_data['full_name'];
            $patient_email = $patient_data['email'];
            $patient_phone = $patient_data['phone'];
            $doctor_name = $doctor_data['full_name'] ?? 'Doctor';

            // 1. Send Email Notification
            if (!empty($patient_email)) {
                sendEmailNotification($patient_email, $patient_name, $appointment_date, $doctor_name);
            }

            // 2. Send SMS Notification via Twilio
            if (!empty($patient_phone)) {
                sendSMSNotification($patient_phone, $patient_name, $appointment_date);
            }
        }

    } else {
        $message = "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
    }
}

$patients = $conn->query("SELECT * FROM patients ORDER BY full_name ASC");
$doctors = $conn->query("SELECT * FROM doctors ORDER BY full_name ASC");

// Filter appointments by date
$filter_date = isset($_GET['filter_date']) ? $_GET['filter_date'] : '';

$query = "
    SELECT a.id, 
           p.full_name AS patient_name, 
           d.full_name AS doctor_name, 
           d.specialization, 
           a.appointment_date, 
           a.status 
    FROM appointments a
    JOIN patients p ON a.patient_id = p.id
    JOIN doctors d ON a.doctor_id = d.id
";

if ($filter_date != '') {
    $query .= " WHERE a.appointment_date = '$filter_date'";
}

$query .= " ORDER BY a.id DESC";

$appointments = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Appointments</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-warning mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold text-dark" href="dashboard.php">
            🏥 Hospital Management System
        </a>
        <a href="dashboard.php" class="btn btn-dark btn-sm">
            ⬅ Back to Dashboard
        </a>
    </div>
</nav>

<div class="container">

    <?php echo $message; ?>

    <div class="row">

        <!-- Appointment Booking Form -->
        <div class="col-md-4 mb-4">
            <div class="card shadow p-3">
                <h4 class="text-dark mb-3">
                    Book Appointment
                </h4>

                <form action="appointments.php" method="POST">
                    <div class="mb-2">
                        <label class="form-label">Select Patient</label>
                        <select name="patient_id" class="form-select" required>
                            <option value="">-- Select Patient --</option>
                            <?php while($p = $patients->fetch_assoc()): ?>
                                <option value="<?php echo $p['id']; ?>">
                                    <?php echo $p['full_name']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Select Doctor</label>
                        <select name="doctor_id" class="form-select" required>
                            <option value="">-- Select Doctor --</option>
                            <?php while($d = $doctors->fetch_assoc()): ?>
                                <option value="<?php echo $d['id']; ?>">
                                    <?php echo $d['full_name']; ?> (<?php echo $d['specialization']; ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Appointment Date</label>
                        <input type="date" name="appointment_date" class="form-control" required>
                    </div>

                    <button type="submit" name="add_appointment" class="btn btn-warning w-100 fw-bold">
                        Book Appointment
                    </button>
                </form>
            </div>
        </div>

        <!-- Appointment List -->
        <div class="col-md-8">
            <div class="card shadow p-3">

                <!-- Date Filter Bar -->
                <form action="appointments.php" method="GET" class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text bg-light fw-bold">
                            Filter by Date:
                        </span>
                        <input type="date" name="filter_date" class="form-control" value="<?php echo htmlspecialchars($filter_date); ?>">
                        <button type="submit" class="btn btn-warning fw-bold">
                            Filter
                        </button>
                        <?php if($filter_date != ''): ?>
                            <a href="appointments.php" class="btn btn-secondary">
                                Clear
                            </a>
                        <?php endif; ?>
                    </div>
                </form>

                <h4 class="text-secondary mb-3">
                    Appointment List
                </h4>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>App. ID</th>
                                <th>Patient</th>
                                <th>Doctor</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if ($appointments->num_rows > 0): ?>
                            <?php while($row = $appointments->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo $row['patient_name']; ?></td>
                                <td><?php echo $row['doctor_name']; ?></td>
                                <td><?php echo $row['appointment_date']; ?></td>
                                <td>
                                    <span class="badge <?php echo ($row['status'] == 'Completed') ? 'bg-success' : (($row['status'] == 'Cancelled') ? 'bg-danger' : 'bg-warning text-dark'); ?>">
                                        <?php echo $row['status']; ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="print_appointment.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-info text-white me-1">
                                        Print
                                    </a>
                                    <a href="edit_appointment.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary me-1">
                                        Edit
                                    </a>
                                    <a href="appointments.php?delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this appointment?');">
                                        Delete
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">
                                    No appointments found for the selected date.
                                </td>
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