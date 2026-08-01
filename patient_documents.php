<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

include('db.php');

$message = "";

// Auto-create patient_documents table if it does not exist
$create_table = "
    CREATE TABLE IF NOT EXISTS patient_documents (
        id INT AUTO_INCREMENT PRIMARY KEY,
        patient_id INT NOT NULL,
        document_title VARCHAR(255) NOT NULL,
        file_name VARCHAR(255) NOT NULL,
        uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE
    );
";
$conn->query($create_table);

// Create uploads folder if not exists
$upload_dir = "uploads/";
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Handle Document Upload
if (isset($_POST['upload_doc'])) {
    $patient_id = intval($_POST['patient_id']);
    $document_title = trim($_POST['document_title']);

    if (isset($_FILES['document_file']) && $_FILES['document_file']['error'] == 0) {
        $file_name = time() . '_' . basename($_FILES['document_file']['name']);
        $target_file = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['document_file']['tmp_name'], $target_file)) {
            $stmt = $conn->prepare("INSERT INTO patient_documents (patient_id, document_title, file_name) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $patient_id, $document_title, $file_name);
            
            if ($stmt->execute()) {
                $message = "<div class='alert alert-success'>Medical report uploaded successfully!</div>";
            } else {
                $message = "<div class='alert alert-danger'>Database Error: " . $conn->error . "</div>";
            }
        } else {
            $message = "<div class='alert alert-danger'>Failed to upload file to server.</div>";
        }
    } else {
        $message = "<div class='alert alert-danger'>Please select a valid file to upload.</div>";
    }
}

// Handle Delete Document
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $res = $conn->query("SELECT file_name FROM patient_documents WHERE id = $id");
    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $file_path = $upload_dir . $row['file_name'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
        $conn->query("DELETE FROM patient_documents WHERE id = $id");
        $message = "<div class='alert alert-warning'>Document deleted successfully!</div>";
    }
}

// Fetch patients list for dropdown
$patients_list = $conn->query("SELECT id, full_name FROM patients ORDER BY full_name ASC");

// Fetch documents list
$documents_list = $conn->query("
    SELECT pd.*, p.full_name AS patient_name 
    FROM patient_documents pd
    JOIN patients p ON pd.patient_id = p.id
    ORDER BY pd.id DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Medical Reports</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <!-- Navigation Bar -->
    <nav class="navbar navbar-dark bg-primary mb-4 shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="dashboard.php">🏥 Hospital Management System</a>
            <a href="dashboard.php" class="btn btn-light btn-sm fw-bold">⬅ Back to Dashboard</a>
        </div>
    </nav>

    <div class="container mb-5">
        <?php echo $message; ?>

        <div class="row">
            <!-- Upload Form (Left Side) -->
            <div class="col-md-4 mb-4">
                <div class="card shadow border-0 p-3">
                    <h4 class="text-primary mb-3">Upload Medical Report</h4>
                    <form action="patient_documents.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Select Patient</label>
                            <select name="patient_id" class="form-select" required>
                                <option value="">-- Choose Patient --</option>
                                <?php while($p = $patients_list->fetch_assoc()): ?>
                                    <option value="<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['full_name']); ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Report Title / Description</label>
                            <input type="text" name="document_title" class="form-control" placeholder="e.g. Blood Test Report, X-Ray" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Choose File (PDF, Images)</label>
                            <input type="file" name="document_file" class="form-control" required>
                        </div>

                        <button type="submit" name="upload_doc" class="btn btn-primary w-100 fw-bold">Upload Report</button>
                    </form>
                </div>
            </div>

            <!-- Documents Table (Right Side) -->
            <div class="col-md-8">
                <div class="card shadow border-0 p-3">
                    <h4 class="text-secondary mb-3">Patient Medical Reports List</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Patient Name</th>
                                    <th>Report Title</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($documents_list && $documents_list->num_rows > 0): ?>
                                    <?php while($doc = $documents_list->fetch_assoc()): ?>
                                        <tr>
                                            <td>#<?php echo $doc['id']; ?></td>
                                            <td><b><?php echo htmlspecialchars($doc['patient_name']); ?></b></td>
                                            <td><?php echo htmlspecialchars($doc['document_title']); ?></td>
                                            <td><?php echo $doc['uploaded_at']; ?></td>
                                            <td>
                                                <a href="uploads/<?php echo $doc['file_name']; ?>" target="_blank" class="btn btn-sm btn-info fw-bold text-white">View</a>
                                                <a href="patient_documents.php?delete=<?php echo $doc['id']; ?>" class="btn btn-sm btn-danger fw-bold" onclick="return confirm('Are you sure you want to delete this report?');">Delete</a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">No medical reports found.</td>
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