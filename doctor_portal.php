<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
include('db.php');

// Fetch schedules and appointments for the doctor portal safely using appointment_date
$schedules_result = $conn->query("SELECT * FROM schedules ORDER BY id DESC LIMIT 5");
$appointments_result = $conn->query("SELECT id, appointment_date as date, status FROM appointments ORDER BY id DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Portal - Medi Lanka HMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <style>
        :root {
            --sidebar-width: 280px;
            --sidebar-bg: #0f172a;
            --primary-color: #2563eb;
            --bg-body: #f8fafc;
            --card-border: #e2e8f0;
        }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: var(--bg-body); color: #1e293b; overflow-x: hidden; }
        .sidebar { width: var(--sidebar-width); position: fixed; top: 0; left: 0; height: 100vh; background: var(--sidebar-bg); color: #94a3b8; z-index: 1040; display: flex; flex-direction: column; border-right: 1px solid rgba(255, 255, 255, 0.05); }
        .sidebar-brand { padding: 1.5rem; font-size: 1.35rem; font-weight: 800; color: #ffffff; display: flex; align-items: center; gap: 0.85rem; border-bottom: 1px solid rgba(255, 255, 255, 0.08); flex-shrink: 0; }
        .sidebar-brand span.highlight { background: linear-gradient(135deg, #60a5fa 0%, #2563eb 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .sidebar-menu { padding: 1rem; overflow-y: auto; flex-grow: 1; max-height: calc(100vh - 80px); }
        .sidebar-menu::-webkit-scrollbar { width: 4px; }
        .sidebar-menu::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
        .menu-category { font-size: 0.68rem; text-transform: uppercase; letter-spacing: 0.1em; color: #93c5fd; font-weight: 700; margin: 1.25rem 0 0.5rem 0.75rem; }
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
            <a href="inventory.php" class="sidebar-link"><i class="bi bi-box-seam"></i> Pharmacy Inventory</a>
            <a href="laboratory.php" class="sidebar-link"><i class="bi bi-eyedropper"></i> Laboratory Tests</a>
            <a href="wards.php" class="sidebar-link"><i class="bi bi-door-open"></i> Beds & Wards</a>
            <div class="menu-category">Portals</div>
            <a href="patient_portal.php" class="sidebar-link"><i class="bi bi-person-workspace"></i> Patient Portal</a>
            <a href="doctor_portal.php" class="sidebar-link active"><i class="bi bi-shield-lock-fill"></i> Doctor Portal</a>
        </div>
    </nav>

    <div class="main-wrapper">
        <header class="top-navbar">
            <h5 class="fw-bold text-dark mb-0">Doctor Professional Workspace</h5>
            <div class="d-flex align-items-center gap-4">
                <div class="d-flex align-items-center gap-2 bg-white px-3.5 py-2 rounded-pill border shadow-sm">
                    <span class="bg-primary rounded-circle" style="width: 9px; height: 9px; display:inline-block;"></span>
                    <span class="text-secondary small fw-medium">Doctor: <strong class="text-dark"><?php echo htmlspecialchars($_SESSION['username'] ?? 'Consultant'); ?></strong></span>
                </div>
                <a href="logout.php" class="btn btn-outline-danger btn-sm px-3.5 py-2 rounded-pill fw-semibold d-flex align-items-center gap-1 shadow-sm"><i class="bi bi-box-arrow-right"></i> Logout</a>
            </div>
        </header>

        <div class="p-4 p-md-5">
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="content-card h-100">
                        <h5 class="fw-bold text-dark mb-3"><i class="bi bi-clock text-primary me-2"></i>Consultation Schedules</h5>
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead class="table-light text-secondary uppercase small">
                                    <tr><th>ID</th><th>Doctor / Slot</th></tr>
                                </thead>
                                <tbody>
                                    <?php if ($schedules_result && $schedules_result->num_rows > 0): ?>
                                        <?php while ($sch = $schedules_result->fetch_assoc()): ?>
                                            <tr>
                                                <td class="fw-bold text-primary">#<?php echo $sch['id']; ?></td>
                                                <td><?php echo htmlspecialchars($sch['day'] ?? 'N/A'); ?></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr><td colspan="2" class="text-center text-muted py-3 small">No schedules found.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="content-card h-100">
                        <h5 class="fw-bold text-dark mb-3"><i class="bi bi-calendar-check text-primary me-2"></i>Patient Queue Stream</h5>
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead class="table-light text-secondary uppercase small">
                                    <tr><th>ID</th><th>Date</th><th>Status</th></tr>
                                </thead>
                                <tbody>
                                    <?php if ($appointments_result && $appointments_result->num_rows > 0): ?>
                                        <?php while ($app = $appointments_result->fetch_assoc()): ?>
                                            <tr>
                                                <td class="fw-bold text-primary">#<?php echo $app['id']; ?></td>
                                                <td><?php echo htmlspecialchars($app['date']); ?></td>
                                                <td><span class="badge bg-primary bg-opacity-10 text-primary px-2 py-1"><?php echo htmlspecialchars($app['status'] ?? 'Scheduled'); ?></span></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr><td colspan="3" class="text-center text-muted py-3 small">No queue items found.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>