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

// Fetch appointments for dropdown with patient and doctor names
$appointments_result = $conn->query("SELECT a.id, p.name as patient_name, d.name as doctor_name, a.appointment_date 
                                    FROM appointments a 
                                    JOIN patients p ON a.patient_id = p.id 
                                    JOIN doctors d ON a.doctor_id = d.id 
                                    ORDER BY a.id DESC");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $appointment_id = intval($_POST['appointment_id']);
    $medication = trim($_POST['medication']);
    $instructions = trim($_POST['instructions']);

    if (empty($appointment_id) || empty($medication)) {
        $error = "Appointment and Medication are required fields.";
    } else {
        $stmt = $conn->prepare("INSERT INTO prescriptions (appointment_id, medication, instructions) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $appointment_id, $medication, $instructions);
        
        if ($stmt->execute()) {
            $success = "Prescription added successfully!";
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
    <title>Add Prescription - Medi Lanka HMS</title>
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
            <a href="manage_prescriptions.php" class="sidebar-link active"><i class="bi bi-prescription2"></i> Prescriptions</a>
        </div>
    </nav>

    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <!-- Top Navbar -->
        <header class="top-navbar">
            <h5 class="fw-bold text-dark mb-0">Add New Prescription</h5>
            <a href="manage_prescriptions.php" class="btn btn-outline-secondary btn-sm px-3 rounded-pill fw-semibold">
                <i class="bi bi-arrow-left"></i> Back to Prescriptions
            </a>
        </header>

        <!-- Content Body -->
        <div class="p-4 p-md-5">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="content-card">
                        <h4 class="fw-bold text-dark mb-3">Create Prescription Record</h4>
                        <p class="text-secondary small mb-4">Select the corresponding appointment and add medication details below.</p>

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
                                <label for="appointment_id" class="form-label fw-semibold">Select Appointment</label>
                                <select class="form-control" id="appointment_id" name="appointment_id" required>
                                    <option value="">-- Choose Appointment (Patient & Doctor) --</option>
                                    <?php while ($row = $appointments_result->fetch_assoc()): ?>
                                        <option value="<?php echo $row['id']; ?>">
                                            Appointment #<?php echo $row['id']; ?> - Patient: <?php echo htmlspecialchars($row['patient_name']); ?> | Doctor: <?php echo htmlspecialchars($row['doctor_name']); ?> (<?php echo $row['appointment_date']; ?>)
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="medication" class="form-label fw-semibold">Medication Details</label>
                                <textarea class="form-control" id="medication" name="medication" rows="3" placeholder="Enter medicine names and dosage..." required></textarea>
                            </div>

                            <div class="mb-4">
                                <label for="instructions" class="form-label fw-semibold">Instructions / Notes</label>
                                <textarea class="form-control" id="instructions" name="instructions" rows="2" placeholder="Special instructions for the patient..."></textarea>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="manage_prescriptions.php" class="btn btn-light border px-4 py-2 rounded-3">Cancel</a>
                                <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 fw-semibold">Save Prescription</button>
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