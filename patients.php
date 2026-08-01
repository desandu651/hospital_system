<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

include('db.php');

$message = "";

// Delete patient record
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $sql = "DELETE FROM patients WHERE id = $delete_id";

    if ($conn->query($sql) === TRUE) {
        $message = "<div class='alert alert-warning'>Patient record deleted successfully!</div>";
    } else {
        $message = "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
    }
}

// Add new patient
if (isset($_POST['add_patient'])) {
    $full_name = $_POST['full_name'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    $sql = "INSERT INTO patients (full_name, age, gender, phone, address) 
            VALUES ('$full_name', '$age', '$gender', '$phone', '$address')";

    if ($conn->query($sql) === TRUE) {
        $message = "<div class='alert alert-success'>Patient added successfully!</div>";
    } else {
        $message = "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
    }
}

// Search Logic
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

if ($search != '') {
    $patients = $conn->query("SELECT * FROM patients 
                              WHERE full_name LIKE '%$search%' 
                              OR phone LIKE '%$search%' 
                              ORDER BY id DESC");
} else {
    $patients = $conn->query("SELECT * FROM patients ORDER BY id DESC");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Patients</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <!-- Navigation Bar -->
    <nav class="navbar navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="dashboard.php">🏥 Hospital Management System</a>
            <a href="dashboard.php" class="btn btn-light btn-sm">⬅ Back to Dashboard</a>
        </div>
    </nav>

    <div class="container">
        <?php echo $message; ?>

        <div class="row">

            <!-- Patient Registration Form (Left Side) -->
            <div class="col-md-4 mb-4">
                <div class="card shadow p-3">
                    <h4 class="text-primary mb-3">Add New Patient</h4>

                    <form action="patients.php" method="POST">

                        <div class="mb-2">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="full_name" class="form-control" required>
                        </div>

                        <div class="mb-2">
                            <label class="form-label">Age</label>
                            <input type="number" name="age" class="form-control" required>
                        </div>

                        <div class="mb-2">
                            <label class="form-label">Gender</label>
                            <select name="gender" class="form-select" required>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <div class="mb-2">
                            <label class="form-label">Phone Number</label>
                            <input type="text" name="phone" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <textarea name="address" class="form-control" rows="2" required></textarea>
                        </div>

                        <button type="submit" name="add_patient" class="btn btn-success w-100">
                            Save Patient
                        </button>

                    </form>
                </div>
            </div>


            <!-- Patient List Table + Search Bar (Right Side) -->
            <div class="col-md-8">
                <div class="card shadow p-3">

                    <!-- Search Bar -->
                    <form action="patients.php" method="GET" class="mb-3">
                        <div class="input-group">

                            <input type="text" 
                                   name="search" 
                                   class="form-control" 
                                   placeholder="Search by Patient Name or Phone Number..."
                                   value="<?php echo htmlspecialchars($search); ?>">

                            <button type="submit" class="btn btn-primary">
                                🔍 Search
                            </button>

                            <?php if($search != ''): ?>
                                <a href="patients.php" class="btn btn-secondary">
                                    Clear
                                </a>
                            <?php endif; ?>

                        </div>
                    </form>


                    <h4 class="text-secondary mb-3">Patient List</h4>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle">

                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Age</th>
                                    <th>Gender</th>
                                    <th>Phone</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php if ($patients->num_rows > 0): ?>

                                    <?php while($row = $patients->fetch_assoc()): ?>

                                        <tr>
                                            <td><?php echo $row['id']; ?></td>
                                            <td><?php echo $row['full_name']; ?></td>
                                            <td><?php echo $row['age']; ?></td>
                                            <td><?php echo $row['gender']; ?></td>
                                            <td><?php echo $row['phone']; ?></td>

                                            <td>
                                                <!-- Edit Button -->
                                                <a href="edit_patient.php?id=<?php echo $row['id']; ?>" 
                                                   class="btn btn-sm btn-primary me-1">
                                                    Edit
                                                </a>

                                                <!-- Delete Button -->
                                                <a href="patients.php?delete=<?php echo $row['id']; ?>" 
                                                   class="btn btn-sm btn-danger"
                                                   onclick="return confirm('Are you sure you want to delete this patient?');">
                                                    Delete
                                                </a>
                                            </td>

                                        </tr>

                                    <?php endwhile; ?>

                                <?php else: ?>

                                    <tr>
                                        <td colspan="6" class="text-center">
                                            No patients found.
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