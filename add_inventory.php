<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }
include('db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];
    
    $conn->query("INSERT INTO inventory (name, quantity, price) VALUES ('$name', '$quantity', '$price')");
    header("Location: inventory.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Inventory - Medi Lanka HMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-5">
    <div class="container" style="max-width: 600px;">
        <div class="card shadow-sm border-0 p-4 rounded-4">
            <h4 class="fw-bold mb-3">Add Pharmacy Item</h4>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Item Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Quantity</label>
                    <input type="number" name="quantity" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Price (Rs.)</label>
                    <input type="number" step="0.01" name="price" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Save Item</button>
                <a href="inventory.php" class="btn btn-secondary w-100 mt-2">Back</a>
            </form>
        </div>
    </div>
</body>
</html>