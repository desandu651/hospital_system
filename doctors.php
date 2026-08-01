<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Restrict access to Admin only
if (isset($_SESSION['role']) && $_SESSION['role'] != 'Admin') {
    die("<h3 style='color:red; text-align:center; margin-top:50px;'>Access Denied! Only Administrators can access the Doctors page.<br><br><a href='dashboard.php'>Back to Dashboard</a></h3>");
}

include('db.php');

$message = "";

// Delete doctor record
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    $sql = "DELETE FROM doctors WHERE id = $delete_id";

    if ($conn->query($sql) === TRUE) {
        $message = "<div class='alert alert-warning'>Doctor record deleted successfully!</div>";
    } else {
        $message = "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
    }
}

// Process Add New Doctor form submission
if (isset($_POST['add_doctor'])) {
    $full_name = $conn->real_escape_string($_POST['full_name']);
    $specialization = $conn->real_escape_string($_POST['specialization']);
    $phone = $conn->real_escape_string($_POST['phone']);

    $sql = "INSERT INTO doctors (full_name, specialization, phone) VALUES ('$full_name', '$specialization', '$phone')";

    if ($conn->query($sql) === TRUE) {
        $message = "<div class='alert alert-success'>Doctor added successfully!</div>";
    } else {
        $message = "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
    }
}

// Handle Search Query
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

if ($search != '') {
    $doctors = $conn->query("SELECT * FROM doctors WHERE full_name LIKE '%$search%' OR specialization LIKE '%$search%' ORDER BY id DESC");
} else {
    $doctors = $conn->query("SELECT * FROM doctors ORDER BY id DESC");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Doctors</title>
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

            <!-- Doctor Registration Form (Left Side) -->
            <div class="col-md-4 mb-4">
                <div class="card shadow p-3">
                    <h4 class="text-success mb-3">Add New Doctor</h4>

                    <form action="doctors.php" method="POST">
                        <div class="mb-2">
                            <label class="form-label">Doctor Name</label>
                            <input type="text" name="full_name" class="form-control" placeholder="Dr. John Doe" required>
                        </div>

                        <div class="mb-2">
                            <label class="form-label">Specialization</label>
                            <input type="text" name="specialization" class="form-control" placeholder="e.g. Cardiologist" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="text" name="phone" class="form-control" required>
                        </div>

                        <button type="submit" name="add_doctor" class="btn btn-success w-100 fw-bold">
                            Save Doctor
                        </button>
                    </form>
                </div>
            </div>

            <!-- Doctor List Table & Search Bar (Right Side) -->
            <div class="col-md-8">
                <div class="card shadow p-3">

                    <!-- Search Bar -->
                    <form action="doctors.php" method="GET" class="mb-3">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Search by Doctor Name or Specialization..." value="<?php echo htmlspecialchars($search); ?>">
                            <button type="submit" class="btn btn-success fw-bold">🔍 Search</button>
                            <?php if($search != ''): ?>
                                <a href="doctors.php" class="btn btn-secondary">Clear</a>
                            <?php endif; ?>
                        </div>
                    </form>

                    <h4 class="text-secondary mb-3">Doctor List</h4>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Specialization</th>
                                    <th>Phone</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if ($doctors->num_rows > 0): ?>
                                    <?php while($row = $doctors->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $row['id']; ?></td>
                                            <td><?php echo $row['full_name']; ?></td>
                                            <td><?php echo $row['specialization']; ?></td>
                                            <td><?php echo $row['phone']; ?></td>
                                            <td>
                                                <!-- Edit Button -->
                                                <a href="edit_doctor.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary me-1">
                                                    Edit
                                                </a>

                                                <!-- Delete Button -->
                                                <a href="doctors.php?delete=<?php echo $row['id']; ?>"
                                                   class="btn btn-sm btn-danger"
                                                   onclick="return confirm('Are you sure you want to delete this doctor?');">
                                                    Delete
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center">
                                            No doctors found.
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