<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
include('db.php');

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM inventory WHERE id = $id");
    header("Location: inventory.php");
    exit();
}

$table_check = $conn->query("SHOW TABLES LIKE 'inventory'");
$result = null;
if ($table_check->num_rows > 0) {
    $result = $conn->query("SELECT * FROM inventory ORDER BY id DESC");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pharmacy Inventory - Medi Lanka HMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <style>
        :root {
            --sidebar-width: 280px;
            --sidebar-bg: #0b1329;
            --primary-color: #2563eb;
            --bg-body: #f4f6f9;
            --card-border: #e2e8f0;
        }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: var(--bg-body); color: #1e293b; overflow-x: hidden; }
        .sidebar { width: var(--sidebar-width); position: fixed; top: 0; left: 0; height: 100vh; background: var(--sidebar-bg); color: #94a3b8; z-index: 1040; display: flex; flex-direction: column; border-right: 1px solid rgba(255, 255, 255, 0.05); }
        .sidebar-brand { padding: 1.5rem; font-size: 1.35rem; font-weight: 800; color: #ffffff; display: flex; align-items: center; gap: 0.85rem; border-bottom: 1px solid rgba(255, 255, 255, 0.08); flex-shrink: 0; }
        .sidebar-brand span.highlight { background: linear-gradient(135deg, #60a5fa 0%, #38bdf8 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .sidebar-menu { padding: 1rem; overflow-y: auto; flex-grow: 1; max-height: calc(100vh - 80px); }
        .sidebar-menu::-webkit-scrollbar { width: 4px; }
        .sidebar-menu::-webkit-scrollbar-thumb { background: #1e293b; border-radius: 4px; }
        .menu-category { font-size: 0.68rem; text-transform: uppercase; letter-spacing: 0.1em; color: #64748b; font-weight: 700; margin: 1.25rem 0 0.5rem 0.75rem; }
        .sidebar-link { display: flex; align-items: center; gap: 0.85rem; padding: 0.75rem 1rem; color: #94a3b8; text-decoration: none; border-radius: 0.75rem; font-weight: 500; font-size: 0.9rem; transition: all 0.25s ease; margin-bottom: 0.25rem; }
        .sidebar-link:hover { background: rgba(255, 255, 255, 0.06); color: #ffffff; transform: translateX(4px); }
        .sidebar-link.active { background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); color: #ffffff; font-weight: 600; box-shadow: 0 8px 16px rgba(37, 99, 235, 0.25); }
        .sidebar-link i { font-size: 1.15rem; }
        .main-wrapper { margin-left: var(--sidebar-width); min-height: 100vh; display: flex; flex-direction: column; }
        .top-navbar { height: 80px; background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(16px); border-bottom: 1px solid var(--card-border); display: flex; align-items: center; justify-content: space-between; padding: 0 2.5rem; position: sticky; top: 0; z-index: 1030; }
        .content-card { background: #ffffff; border: 1px solid var(--card-border); border-radius: 1.25rem; padding: 1.75rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.01); }
    </style>
</head>
<body>
    <nav class="sidebar">
        <div class="sidebar-brand">
            <div class="bg-primary text-white p-2 rounded-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px;"><i class="bi bi-hospital-fill fs-5"></i></div>
            <span>Medi <span class="highlight">Lanka</span></span>
        </div>
        <div class="sidebar-menu">
            <div class="menu-category">Overview</div>
            <a href="dashboard.php" class="sidebar-link"><i class="bi bi-grid-1x2-fill"></i> Dashboard</a>
            <div class="menu-category">Clinical Management</div>
            <a href="manage_patients.php" class="sidebar-link"><i class="bi bi-people-fill"></i> Patients Directory</a>
            <a href="manage_doctors.php" class="sidebar-link"><i class="bi bi-heart-pulse-fill"></i> Doctors Staff</a>
            <a href="manage_appointments.php" class="sidebar-link"><i class="bi bi-calendar-check-fill"></i> Appointments</a>
            <a href="doctor_schedules.php" class="sidebar-link"><i class="bi bi-clock-history"></i> Schedules</a>
            <a href="manage_prescriptions.php" class="sidebar-link"><i class="bi bi-capsule"></i> Prescriptions</a>
            <div class="menu-category">Administration & Finance</div>
            <a href="billing.php" class="sidebar-link"><i class="bi bi-receipt"></i> Billing & Invoices</a>
            <a href="staff_payroll.php" class="sidebar-link"><i class="bi bi-cash-stack"></i> Payroll & Staff</a>
            <a href="inventory.php" class="sidebar-link active"><i class="bi bi-box-seam"></i> Pharmacy Inventory</a>
            <a href="laboratory.php" class="sidebar-link"><i class="bi bi-eyedropper"></i> Laboratory Tests</a>
            <a href="wards.php" class="sidebar-link"><i class="bi bi-door-open"></i> Beds & Wards</a>
            <div class="menu-category">Portals</div>
            <a href="patient_portal.php" class="sidebar-link"><i class="bi bi-person-workspace"></i> Patient Portal</a>
            <a href="doctor_portal.php" class="sidebar-link"><i class="bi bi-shield-lock-fill"></i> Doctor Portal</a>
        </div>
    </nav>

    <div class="main-wrapper">
        <header class="top-navbar">
            <h5 class="fw-bold text-dark mb-0">Pharmacy Inventory</h5>
            <div class="d-flex align-items-center gap-4">
                <div class="d-flex align-items-center gap-2 bg-white px-3.5 py-2 rounded-pill border shadow-sm">
                    <span class="bg-success rounded-circle" style="width: 9px; height: 9px; display:inline-block;"></span>
                    <span class="text-secondary small fw-medium">Admin: <strong class="text-dark"><?php echo htmlspecialchars($_SESSION['username'] ?? 'Administrator'); ?></strong></span>
                </div>
                <a href="logout.php" class="btn btn-outline-danger btn-sm px-3.5 py-2 rounded-pill fw-semibold d-flex align-items-center gap-1 shadow-sm"><i class="bi bi-box-arrow-right"></i> Logout</a>
            </div>
        </header>

        <div class="p-4 p-md-5">
            <div class="content-card mb-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="fw-bold text-dark mb-1">Medical Stock & Drugs</h4>
                        <p class="text-secondary small mb-0">Monitor hospital pharmacy items and stock levels.</p>
                    </div>
                    <a href="add_inventory.php" class="btn btn-primary fw-semibold px-4 py-2 rounded-3 shadow-sm d-flex align-items-center gap-2"><i class="bi bi-plus-circle-fill"></i> Add Item</a>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light text-secondary uppercase small">
                            <tr>
                                <th class="py-3 rounded-start">Item ID</th>
                                <th class="py-3">Item Name</th>
                                <th class="py-3">Quantity</th>
                                <th class="py-3">Price</th>
                                <th class="py-3 text-end rounded-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result && $result->num_rows > 0): ?>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td class="fw-bold text-primary">#<?php echo $row['id']; ?></td>
                                        <td><?php echo htmlspecialchars($row['name'] ?? $row['item_name'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($row['quantity'] ?? '0'); ?></td>
                                        <td><?php echo htmlspecialchars($row['price'] ?? '0.00'); ?></td>
                                        <td class="text-end">
                                            <a href="inventory.php?delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-light border text-danger" onclick="return confirm('Delete this item?');"><i class="bi bi-trash"></i></a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted small">
                                        <i class="bi bi-box-seam fs-2 d-block mb-2 text-secondary opacity-50"></i>
                                        No inventory stock items found in database.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>