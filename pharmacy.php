<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
include('db.php');

$success_msg = "";
$error_msg = "";

// Handle Add Medicine
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_medicine'])) {
    $medicine_name = $conn->real_escape_string($_POST['medicine_name']);
    $quantity = intval($_POST['quantity']);
    $price = floatval($_POST['price']);

    $conn->query("INSERT INTO medicines (medicine_name, quantity, price) VALUES ('$medicine_name', $quantity, $price)");
    $success_msg = "Medicine added successfully!";
}

// Fetch medicines from database (using 'quantity' column)
$medicines = $conn->query("SELECT * FROM medicines ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pharmacy & Inventory Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-secondary mb-4 shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold text-white" href="dashboard.php">💊 Pharmacy & Inventory - Hospital System</a>
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

        <div class="row mb-4">
            <div class="col-md-12 text-end">
                <button type="button" class="btn btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#addMedicineModal">
                    + Add New Medicine
                </button>
            </div>
        </div>

        <!-- Pharmacy Inventory Table -->
        <div class="card shadow border-0 p-3">
            <h5 class="text-secondary fw-bold mb-3">📦 Medicine Inventory Stock</h5>
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Medicine Name</th>
                            <th>Available Quantity</th>
                            <th>Price ($)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($medicines && $medicines->num_rows > 0): ?>
                            <?php while($med = $medicines->fetch_assoc()): ?>
                                <tr>
                                    <td>#<?php echo $med['id']; ?></td>
                                    <td><b><?php echo htmlspecialchars($med['medicine_name'] ?? $med['name'] ?? ''); ?></b></td>
                                    <td>
                                        <?php 
                                            // Handle both 'quantity' or 'stock' column names safely
                                            $qty = $med['quantity'] ?? $med['stock'] ?? 0;
                                            if ($qty < 10): 
                                        ?>
                                            <span class="badge bg-danger">Low Stock: <?php echo $qty; ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-success"><?php echo $qty; ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>$<?php echo number_format($med['price'], 2); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="text-center text-muted">No medicines found in inventory.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Medicine Modal -->
    <div class="modal fade" id="addMedicineModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Add Medicine to Inventory</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Medicine Name</label>
                            <input type="text" name="medicine_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Quantity</label>
                            <input type="number" name="quantity" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Price ($)</label>
                            <input type="number" step="0.01" name="price" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="add_medicine" class="btn btn-primary fw-bold">Save Medicine</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>