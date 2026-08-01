<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

include('db.php');

$error = '';
$success = '';

// Check if ID is provided in URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: manage_appointments.php");
    exit();
}

$id = intval($_GET['id']);

// Fetch appointment details
$stmt = $conn->prepare("SELECT * FROM appointments WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: manage_appointments.php");
    exit();
}

$appointment = $result->fetch_assoc();

// Fetch patients and doctors using 'name' column instead of 'full_name'
$patients_result = $conn->query("SELECT * FROM patients ORDER BY name ASC");
$doctors_result = $conn->query("SELECT * FROM doctors ORDER BY name ASC");

// Handle form submission for update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $patient_id = intval($_POST['patient_id']);
    $doctor_id = intval($_POST['doctor_id']);
    $appointment_date = trim($_POST['appointment_date']);
    $status = trim($_POST['status']);

    if (empty($patient_id) || empty($doctor_id) || empty($appointment_date)) {
        $error = "All required fields must be filled.";
    } else {
        $update_stmt = $conn->prepare("UPDATE appointments SET patient_id = ?, doctor_id = ?, appointment_date = ?, status = ? WHERE id = ?");
        $update_stmt->bind_param("iissi", $patient_id, $doctor_id, $appointment_date, $status, $id);
        
        if ($update_stmt->execute()) {
            $success = "Appointment updated successfully!";
            // Refresh appointment data
            $stmt->execute();
            $result = $stmt->get_result();
            $appointment = $result->fetch_assoc();
        } else {
            $error = "Something went wrong. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Appointment - Medi Lanka HMS</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <style>
        :root {
            --sidebar-width: 280px;
            --sidebar-bg: #0b1329;
            --primary-color: #2563eb;
            --bg-body: #f4f6f9;
            --card-border: #e2e8f0;
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-body);
            color: #1e293b;
            overflow-x: hidden;
        }
        .sidebar {
            width: var(--sidebar-width);
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            background: var(--sidebar-bg);
            color: #94a3b8;
            z-index: 1040;
            display: flex;
            flex-direction: column;
            border-right: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 4px 0 24px rgba(0, 0, 0, 0.04);
        }
        .sidebar-brand {
            padding: 1.5rem;
            font-size: 1.35rem;
            font-weight: 800;
            color: #ffffff;
            display: flex;
            align-items: center;
            gap: 0.85rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            flex-shrink: 0;
        }
        .sidebar-brand span.highlight {
            background: linear-gradient(135deg, #60a5fa 0%, #38bdf8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .sidebar-menu {
            padding: 1rem;
            overflow-y: auto;
            flex-grow: 1;
        }
        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            padding: 0.75rem 1rem;
            color: #94a3b8;
            text-decoration: none;
            border-radius: 0.75rem;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.25s ease;
            margin-bottom: 0.25rem;
        }
        .sidebar-link:hover, .sidebar-link.active {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #ffffff;
        }
        .main-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .top-navbar {
            height: 80px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--card-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2.5rem;
            position: sticky;
            top: 0;
            z-index: 1030;
        }
        .content-card {
            background: #ffffff;
            border: 1px solid var(--card-border);
            border-radius: 1.25rem;
            padding: 2rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.01);
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="sidebar-brand">
            <div class="bg-primary text-white p-2 rounded-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px;">
                <i class="bi bi-hospital-fill fs-5"></i>
            </div>
            <span>Medi <span class="highlight">Lanka</span></span>
        </div>

        <div class="sidebar-menu">
            <div class="text-uppercase small text-muted fw-bold mb-2 ps-3">Clinical Management</div>
            <a href="manage_appointments.php" class="sidebar-link active"><i class="bi bi-calendar-check-fill"></i> Appointments</a>
        </div>
    </nav>

    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <!-- Top Navbar -->
        <header class="top-navbar">
            <h5 class="fw-bold text-dark mb-0">Edit Appointment Record</h5>
            <a href="manage_appointments.php" class="btn btn-outline-secondary btn-sm px-3 rounded-pill fw-semibold">
                <i class="bi bi-arrow-left"></i> Back to Appointments
            </a>
        </header>

        <!-- Content Body -->
        <div class="p-4 p-md-5">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="content-card">
                        <h4 class="fw-bold text-dark mb-3">Update Appointment Information</h4>
                        <p class="text-secondary small mb-4">Modify the appointment details below and save changes.</p>

                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?php echo $error; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($success)): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?php echo $success; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="patient_id" class="form-label fw-semibold">Select Patient</label>
                                <select class="form-control" id="patient_id" name="patient_id" required>
                                    <option value="">-- Choose Patient --</option>
                                    <?php while ($p = $patients_result->fetch_assoc()): ?>
                                        <option value="<?php echo $p['id']; ?>" <?php if ($appointment['patient_id'] == $p['id']) echo 'selected'; ?>>
                                            <?php echo htmlspecialchars($p['name']); ?> (ID: #<?php echo $p['id']; ?>)
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="doctor_id" class="form-label fw-semibold">Select Doctor</label>
                                <select class="form-control" id="doctor_id" name="doctor_id" required>
                                    <option value="">-- Choose Doctor --</option>
                                    <?php while ($d = $doctors_result->fetch_assoc()): ?>
                                        <option value="<?php echo $d['id']; ?>" <?php if ($appointment['doctor_id'] == $d['id']) echo 'selected'; ?>>
                                            <?php echo htmlspecialchars($d['name']); ?> (<?php echo htmlspecialchars($d['specialization']); ?>)
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="appointment_date" class="form-label fw-semibold">Appointment Date & Time</label>
                                <input type="datetime-local" class="form-control" id="appointment_date" name="appointment_date" value="<?php echo date('Y-m-d\TH:i', strtotime($appointment['appointment_date'])); ?>" required>
                            </div>

                            <div class="mb-4">
                                <label for="status" class="form-label fw-semibold">Status</label>
                                <select class="form-control" id="status" name="status">
                                    <option value="Pending" <?php if ($appointment['status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                                    <option value="Completed" <?php if ($appointment['status'] == 'Completed') echo 'selected'; ?>>Completed</option>
                                    <option value="Cancelled" <?php if ($appointment['status'] == 'Cancelled') echo 'selected'; ?>>Cancelled</option>
                                </select>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="manage_appointments.php" class="btn btn-light border px-4 py-2 rounded-3">Cancel</a>
                                <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 fw-semibold">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>