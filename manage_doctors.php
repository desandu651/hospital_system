<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

include('db.php');

// Handle doctor deletion if requested
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM doctors WHERE id = $id");
    header("Location: manage_doctors.php");
    exit();
}

// Fetch all doctors from database
$doctors_result = $conn->query("SELECT * FROM doctors ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctors Staff - Medi Lanka HMS</title>
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

        /* Sidebar Styling */
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
            max-height: calc(100vh - 80px);
        }
        .sidebar-menu::-webkit-scrollbar {
            width: 4px;
        }
        .sidebar-menu::-webkit-scrollbar-thumb {
            background: #1e293b;
            border-radius: 4px;
        }
        .menu-category {
            font-size: 0.68rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #64748b;
            font-weight: 700;
            margin: 1.25rem 0 0.5rem 0.75rem;
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
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            margin-bottom: 0.25rem;
        }
        .sidebar-link:hover {
            background: rgba(255, 255, 255, 0.06);
            color: #ffffff;
            transform: translateX(4px);
        }
        .sidebar-link.active {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #ffffff;
            font-weight: 600;
            box-shadow: 0 8px 16px rgba(37, 99, 235, 0.25);
        }
        .sidebar-link i {
            font-size: 1.15rem;
        }

        /* Main Wrapper */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Top Navbar */
        .top-navbar {
            height: 80px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--card-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2.5rem;
            position: sticky;
            top: 0;
            z-index: 1030;
        }

        /* Content Cards */
        .content-card {
            background: #ffffff;
            border: 1px solid var(--card-border);
            border-radius: 1.25rem;
            padding: 1.75rem;
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
            <div class="menu-category">Overview</div>
            <a href="dashboard.php" class="sidebar-link"><i class="bi bi-grid-1x2-fill"></i> Dashboard</a>
            
            <div class="menu-category">Clinical Management</div>
            <a href="manage_patients.php" class="sidebar-link"><i class="bi bi-people-fill"></i> Patients Directory</a>
            <a href="manage_doctors.php" class="sidebar-link active"><i class="bi bi-heart-pulse-fill"></i> Doctors Staff</a>
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
            <a href="doctor_portal.php" class="sidebar-link"><i class="bi bi-shield-lock-fill"></i> Doctor Portal</a>
        </div>
    </nav>

    <!-- Main Wrapper -->
    <div class="main-wrapper">
        
        <!-- Top Navbar -->
        <header class="top-navbar">
            <div class="d-flex align-items-center gap-2">
                <h5 class="fw-bold text-dark mb-0">Doctors Staff Directory</h5>
            </div>

            <div class="d-flex align-items-center gap-4">
                <div class="d-flex align-items-center gap-2 bg-white px-3.5 py-2 rounded-pill border shadow-sm">
                    <span class="bg-success rounded-circle" style="width: 9px; height: 9px; display:inline-block;"></span>
                    <span class="text-secondary small fw-medium">Admin: <strong class="text-dark"><?php echo htmlspecialchars($_SESSION['username'] ?? 'Administrator'); ?></strong></span>
                </div>
                <a href="logout.php" class="btn btn-outline-danger btn-sm px-3.5 py-2 rounded-pill fw-semibold d-flex align-items-center gap-1 shadow-sm">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </div>
        </header>

        <!-- Content Body -->
        <div class="p-4 p-md-5">
            <div class="content-card mb-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="fw-bold text-dark mb-1">Clinical Doctors Staff</h4>
                        <p class="text-secondary small mb-0">Manage hospital consultants, specialists, and medical officers.</p>
                    </div>
                    <a href="add_doctor.php" class="btn btn-primary fw-semibold px-4 py-2 rounded-3 shadow-sm d-flex align-items-center gap-2">
                        <i class="bi bi-person-plus-fill"></i> Add New Doctor
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light text-secondary uppercase small">
                            <tr>
                                <th class="py-3 rounded-start">ID</th>
                                <th class="py-3">Doctor Name</th>
                                <th class="py-3">Specialization</th>
                                <th class="py-3">Contact Number</th>
                                <th class="py-3">Email</th>
                                <th class="py-3 text-end rounded-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($doctors_result && $doctors_result->num_rows > 0): ?>
                                <?php while ($row = $doctors_result->fetch_assoc()): ?>
                                    <tr>
                                        <td class="fw-bold text-primary">#<?php echo $row['id']; ?></td>
                                        <td class="fw-semibold text-dark"><?php echo htmlspecialchars($row['name'] ?? 'N/A'); ?></td>
                                        <td><span class="badge bg-info bg-opacity-10 text-info px-2.5 py-1.5 fw-semibold"><?php echo htmlspecialchars($row['specialization'] ?? 'General'); ?></span></td>
                                        <td><?php echo htmlspecialchars($row['phone'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($row['email'] ?? 'N/A'); ?></td>
                                        <td class="text-end">
                                            <a href="edit_doctor.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-light border text-primary me-1"><i class="bi bi-pencil"></i></a>
                                            <a href="manage_doctors.php?delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-light border text-danger" onclick="return confirm('Are you sure you want to delete this doctor record?');"><i class="bi bi-trash"></i></a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted small">
                                        <i class="bi bi-heart-pulse fs-2 d-block mb-2 text-secondary opacity-50"></i>
                                        No doctor records found in the database. Click "Add New Doctor" to register.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>