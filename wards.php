<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
include('db.php');

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM wards WHERE id = $id");
    header("Location: wards.php");
    exit();
}

$table_check = $conn->query("SHOW TABLES LIKE 'wards'");
$result = null;
if ($table_check->num_rows > 0) {
    $result = $conn->query("SELECT * FROM wards ORDER BY id DESC");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Beds & Wards - Medi Lanka HMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        :root { --sidebar-width: 280px; --sidebar-bg: #0b1329; --bg-body: #f4f6f9; --card-border: #e2e8f0; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: var(--bg-body); color: #1e293b; }
        .sidebar { width: var(--sidebar-width); position: fixed; top: 0; left: 0; height: 100vh; background: var(--sidebar-bg); color: #94a3b8; z-index: 1040; display: flex; flex-direction: column; }
        .sidebar-brand { padding: 1.5rem; font-size: 1.35rem; font-weight: 800; color: #ffffff; display: flex; align-items: center; gap: 0.85rem; border-bottom: 1px solid rgba(255, 255, 255, 0.08); }
        .sidebar-menu { padding: 1rem; overflow-y: auto; flex-grow: 1; }
        .sidebar-link { display: flex; align-items: center; gap: 0.85rem; padding: 0.75rem 1rem; color: #94a3b8; text-decoration: none; border-radius: 0.75rem; font-weight: 500; margin-bottom: 0.25rem; }
        .sidebar-link:hover, .sidebar-link.active { background: #2563eb; color: #ffffff; }
        .main-wrapper { margin-left: var(--sidebar-width); min-height: 100vh; display: flex; flex-direction: column; }
        .top-navbar { height: 80px; background: rgba(255, 255, 255, 0.9); border-bottom: 1px solid var(--card-border); display: flex; align-items: center; justify-content: space-between; padding: 0 2.5rem; position: sticky; top: 0; z-index: 1030; }
        .content-card { background: #ffffff; border: 1px solid var(--card-border); border-radius: 1.25rem; padding: 1.75rem; }
    </style>
</head>
<body>
    <nav class="sidebar">
        <div class="sidebar-brand">
            <i class="bi bi-hospital-fill"></i><span>Medi Lanka</span>
        </div>
        <div class="sidebar-menu">
            <a href="dashboard.php" class="sidebar-link"><i class="bi bi-grid-1x2-fill"></i> Dashboard</a>
            <a href="wards.php" class="sidebar-link active"><i class="bi bi-door-open"></i> Beds & Wards</a>
        </div>
    </nav>
    <div class="main-wrapper">
        <header class="top-navbar">
            <h5 class="fw-bold text-dark mb-0">Beds & Wards Management</h5>
            <a href="logout.php" class="btn btn-outline-danger btn-sm rounded-pill">Logout</a>
        </header>
        <div class="p-4 p-md-5">
            <div class="content-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="fw-bold text-dark mb-0">Hospital Wards & Beds</h4>
                    <a href="add_ward.php" class="btn btn-primary btn-sm"><i class="bi bi-plus-circle"></i> Add Ward</a>
                </div>
                <table class="table align-middle">
                    <thead>
                        <tr><th>ID</th><th>Ward Name</th><th>Bed Count</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td>#<?php echo $row['id']; ?></td>
                                    <td><?php echo htmlspecialchars($row['ward_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['bed_count']); ?></td>
                                    <td><a href="wards.php?delete=<?php echo $row['id']; ?>" class="text-danger" onclick="return confirm('Delete?');"><i class="bi bi-trash"></i></a></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="text-center text-muted">No wards found in database.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>