<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
include('db.php');

$success_msg = "";
$error_msg = "";

// 1. Handle Add Ward
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_ward'])) {
    $ward_name = $conn->real_escape_string($_POST['ward_name']);
    $ward_type = $conn->real_escape_string($_POST['ward_type']);

    $conn->query("INSERT INTO wards (ward_name, ward_type) VALUES ('$ward_name', '$ward_type')");
    $success_msg = "New ward added successfully!";
}

// 2. Handle Add Bed
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_bed'])) {
    $ward_id = intval($_POST['ward_id']);
    $bed_number = $conn->real_escape_string($_POST['bed_number']);

    $conn->query("INSERT INTO beds (ward_id, bed_number, status) VALUES ($ward_id, '$bed_number', 'Available')");
    $success_msg = "New bed added successfully!";
}

// 3. Handle Allocate Bed to Patient
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['allocate_bed'])) {
    $bed_id = intval($_POST['bed_id']);
    $patient_id = intval($_POST['patient_id']);

    $conn->query("UPDATE beds SET status = 'Occupied', patient_id = $patient_id WHERE id = $bed_id");
    $success_msg = "Bed allocated to patient successfully!";
}

// 4. Handle Release Bed
if (isset($_GET['release_id'])) {
    $release_id = intval($_GET['release_id']);
    $conn->query("UPDATE beds SET status = 'Available', patient_id = NULL WHERE id = $release_id");
    header("Location: beds.php");
    exit();
}

// Fetch statistics for dashboard cards
$total_beds = $conn->query("SELECT COUNT(*) as total FROM beds")->fetch_assoc()['total'];
$available_beds = $conn->query("SELECT COUNT(*) as total FROM beds WHERE status = 'Available'")->fetch_assoc()['total'];
$occupied_beds = $conn->query("SELECT COUNT(*) as total FROM beds WHERE status = 'Occupied'")->fetch_assoc()['total'];

// Fetch lists
$wards = $conn->query("SELECT * FROM wards");
$patients = $conn->query("SELECT id, full_name FROM patients");

// Fetch beds with ward and patient details
$beds_list = $conn->query("
    SELECT b.id, b.bed_number, b.status, w.ward_name, w.ward_type, p.full_name AS patient_name 
    FROM beds b
    JOIN wards w ON b.ward_id = w.id
    LEFT JOIN patients p ON b.patient_id = p.id
    ORDER BY b.id DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bed & Ward Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-primary mb-4 shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="dashboard.php">🛏️ Bed & Ward Management - Hospital System</a>
            <a href="dashboard.php" class="btn btn-dark btn-sm fw-bold">⬅ Dashboard</a>
        </div>
    </nav>

    <div class="container mb-5">
        <?php if ($success_msg): ?>
            <div class="alert alert-success"><?php echo $success_msg; ?></div>
        <?php endif; ?>
        <?php if ($error_msg): ?>
            <div class="alert alert-danger"><?php echo $error_msg; ?></div>
        <?php endif; ?>

        <!-- Summary Status Cards -->
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="card bg-info text-white shadow p-3 border-0">
                    <h6 class="text-uppercase small">Total Beds</h6>
                    <h2 class="display-6 fw-bold mb-0"><?php echo $total_beds; ?></h2>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card bg-success text-white shadow p-3 border-0">
                    <h6 class="text-uppercase small">Available Beds (Free)</h6>
                    <h2 class="display-6 fw-bold mb-0"><?php echo $available_beds; ?></h2>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card bg-danger text-white shadow p-3 border-0">
                    <h6 class="text-uppercase small">Occupied Beds</h6>
                    <h2 class="display-6 fw-bold mb-0"><?php echo $occupied_beds; ?></h2>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="row mb-4">
            <div class="col-md-12 text-end">
                <button type="button" class="btn btn-success fw-bold me-2" data-bs-toggle="modal" data-bs-target="#addWardModal">
                    + Add New Ward
                </button>
                <button type="button" class="btn btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#addBedModal">
                    + Add New Bed
                </button>
            </div>
        </div>

        <!-- Beds Status Table -->
        <div class="card shadow border-0 p-3">
            <h5 class="text-secondary fw-bold mb-3">📋 Bed Status & Ward Allocation List</h5>
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Ward Name</th>
                            <th>Ward Type</th>
                            <th>Bed Number</th>
                            <th>Status</th>
                            <th>Allocated Patient</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($beds_list && $beds_list->num_rows > 0): ?>
                            <?php while($bed = $beds_list->fetch_assoc()): ?>
                                <tr>
                                    <td>#<?php echo $bed['id']; ?></td>
                                    <td><b><?php echo htmlspecialchars($bed['ward_name']); ?></b></td>
                                    <td>
                                        <span class="badge bg-secondary"><?php echo htmlspecialchars($bed['ward_type']); ?></span>
                                    </td>
                                    <td><b><?php echo htmlspecialchars($bed['bed_number']); ?></b></td>
                                    <td>
                                        <?php if ($bed['status'] == 'Available'): ?>
                                            <span class="badge bg-success">Available</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Occupied</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php echo $bed['patient_name'] ? htmlspecialchars($bed['patient_name']) : '<span class="text-muted">None</span>'; ?>
                                    </td>
                                    <td>
                                        <?php if ($bed['status'] == 'Available'): ?>
                                            <button type="button" class="btn btn-sm btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#allocateModal<?php echo $bed['id']; ?>">
                                                Allocate Bed
                                            </button>
                                        <?php else: ?>
                                            <a href="beds.php?release_id=<?php echo $bed['id']; ?>" class="btn btn-sm btn-warning fw-bold text-dark" onclick="return confirm('Are you sure you want to release this bed?');">
                                                Release Bed
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>

                                <!-- Modal for Allocate Bed -->
                                <div class="modal fade" id="allocateModal<?php echo $bed['id']; ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form method="POST">
                                                <div class="modal-header">
                                                    <h5 class="modal-title fw-bold">Allocate Bed (<?php echo $bed['bed_number']; ?> - <?php echo $bed['ward_name']; ?>)</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <input type="hidden" name="bed_id" value="<?php echo $bed['id']; ?>">
                                                    <div class="mb-3">
                                                        <label class="form-label">Select Patient</label>
                                                        <select name="patient_id" class="form-select" required>
                                                            <option value="">-- Choose Patient --</option>
                                                            <?php 
                                                            $patients->data_seek(0);
                                                            while($pat = $patients->fetch_assoc()): 
                                                            ?>
                                                                <option value="<?php echo $pat['id']; ?>"><?php echo htmlspecialchars($pat['full_name']); ?></option>
                                                            <?php endwhile; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" name="allocate_bed" class="btn btn-primary fw-bold">Confirm Allocation</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="7" class="text-center text-muted">No beds found in the system.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Ward Modal -->
    <div class="modal fade" id="addWardModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Add New Ward</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Ward Name</label>
                            <input type="text" name="ward_name" class="form-control" placeholder="e.g. Ward A, ICU Unit 1" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ward Type</label>
                            <select name="ward_type" class="form-select" required>
                                <option value="">-- Select Type --</option>
                                <option value="General Ward">General Ward</option>
                                <option value="Private Room">Private Room</option>
                                <option value="ICU">ICU (Intensive Care Unit)</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="add_ward" class="btn btn-success fw-bold">Save Ward</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Bed Modal -->
    <div class="modal fade" id="addBedModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Add New Bed</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Select Ward</label>
                            <select name="ward_id" class="form-select" required>
                                <option value="">-- Choose Ward --</option>
                                <?php 
                                $wards->data_seek(0);
                                while($w = $wards->fetch_assoc()): 
                                ?>
                                    <option value="<?php echo $w['id']; ?>"><?php echo htmlspecialchars($w['ward_name']); ?> (<?php echo $w['ward_type']; ?>)</option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Bed Number / Name</label>
                            <input type="text" name="bed_number" class="form-control" placeholder="e.g. Bed-101, ICU-03" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="add_bed" class="btn btn-primary fw-bold">Save Bed</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>