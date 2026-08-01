<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

include('db.php');

// Fetch prescriptions with patient and doctor details
$query = "SELECT p.*, 
          COALESCE(pat.name, 'N/A') as patient_name, 
          COALESCE(doc.name, 'N/A') as doctor_name 
          FROM prescriptions p 
          LEFT JOIN patients pat ON p.patient_id = pat.id 
          LEFT JOIN doctors doc ON p.doctor_id = doc.id 
          ORDER BY p.id DESC";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clinical Prescriptions - Medi Lanka HMS</title>
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
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-body);
            color: #1e293b;
            overflow-x: hidden;
        }
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
            transition: all 0.25s ease;
            margin-bottom: 0.25rem;
        }
        .sidebar-link:hover, .sidebar-link.active {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #ffffff;
        }
        .main-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .top-navbar {
            height: 80px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--card-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2.5rem;
            position: sticky;
            top: 0;
            z-index: 1030;
        }
        .content-card {
            background: #ffffff;
            border: 1px solid var(--card-border);
            border-radius: 1.25rem;
            padding: 2rem;
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
            <div class="text-uppercase small text-muted fw-bold mb-2 ps-3">Overview</div>
            <a href="dashboard.php" class="sidebar-link"><i class="bi bi-grid-1x2-fill"></i> Dashboard</a>
            
            <div class="text-uppercase small text-muted fw-bold mt-4 mb-2 ps-3">Clinical Management</div>
            <a href="manage_doctors.php" class="sidebar-link"><i class="bi bi-heart-pulse-fill"></i> Doctors Staff</a>
            <a href="manage_prescriptions.php" class="sidebar-link active"><i class="bi bi-capsule-pill"></i> Prescriptions</a>
        </div>
    </nav>

    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <!-- Top Navbar -->
        <header class="top-navbar">
            <h5 class="fw-bold text-dark mb-0">Clinical Prescriptions</h5>
            <div class="d-flex align-items-center gap-3">
                <span class="text-secondary small fw-semibold"><span class="badge bg-primary">Admin</span>: admin</span>
                <a href="logout.php" class="btn btn-outline-danger btn-sm px-3 rounded-pill fw-semibold">Logout</a>
            </div>
        </header>

        <!-- Content Body -->
        <div class="p-4 p-md-5">
            <div class="content-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="fw-bold text-dark mb-1">Issued Medical Prescriptions</h4>
                        <p class="text-secondary small mb-0">Patient medication history and pharmacy records.</p>
                    </div>
                    <a href="add_prescription.php" class="btn btn-primary rounded-pill px-4 fw-semibold shadow-sm">
                        <i class="bi bi-plus-lg me-1"></i> New Prescription
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light text-secondary small uppercase">
                            <tr>
                                <th class="py-3">ID</th>
                                <th class="py-3">Patient Name</th>
                                <th class="py-3">Consultant Doctor</th>
                                <th class="py-3">Details / Meds</th>
                                <th class="py-3 text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result && $result->num_rows > 0): ?>
                                <?php while($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td class="fw-semibold">#<?php echo $row['id']; ?></td>
                                        <td><?php echo htmlspecialchars($row['patient_name']); ?></td>
                                        <td>Dr. <?php echo htmlspecialchars($row['doctor_name']); ?></td>
                                        <td>
                                            <span class="d-inline-block text-truncate" style="max-width: 250px;">
                                                <?php echo htmlspecialchars($row['medication']); ?>
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <a href="view_prescription.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-light border text-primary" title="View"><i class="bi bi-eye"></i></a>
                                            <a href="delete_prescription.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-light border text-danger" title="Delete" onclick="return confirm('Are you sure?');"><i class="bi bi-trash"></i></a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="text-muted py-4">
                                            <i class="bi bi-capsule fs-1 text-secondary opacity-50 d-block mb-3"></i>
                                            <p class="mb-0">No prescriptions found in the database.</p>
                                        </div>
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