<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
include('db.php');

// Fetch counts for dashboard metrics
$patients_count = 0;
$doctors_count = 0;
$appointments_count = 0;

$p_res = $conn->query("SELECT COUNT(*) as total FROM patients");
if ($p_res) { $patients_count = $p_res->fetch_assoc()['total']; }

$d_res = $conn->query("SELECT COUNT(*) as total FROM doctors");
if ($d_res) { $doctors_count = $d_res->fetch_assoc()['total']; }

$a_res = $conn->query("SELECT COUNT(*) as total FROM appointments");
if ($a_res) { $appointments_count = $a_res->fetch_assoc()['total']; }

// Global Smart Search Query Handler
$global_search_results = null;
if (isset($_GET['global_search']) && !empty(trim($_GET['global_search']))) {
    $search_term = $conn->real_escape_string(trim($_GET['global_search']));
    $global_search_results = $conn->query("
        SELECT id, name, phone, diagnosis, 'Patient' as type 
        FROM patients 
        WHERE name LIKE '%$search_term%' OR phone LIKE '%$search_term%' OR diagnosis LIKE '%$search_term%'
        LIMIT 5
    ");
}

// Fetch recent appointments using correct table joins
$recent_appointments = $conn->query("
    SELECT a.id, 
           COALESCE(p.name, 'N/A') AS patient_name, 
           COALESCE(d.name, 'N/A') AS doctor_name, 
           a.appointment_date 
    FROM appointments a
    LEFT JOIN patients p ON a.patient_id = p.id
    LEFT JOIN doctors d ON a.doctor_id = d.id
    ORDER BY a.id DESC 
    LIMIT 5
");

// Safe Low Stock Alert items fallback
$low_stock_items = [
    ['item_name' => 'Paracetamol 500mg', 'stock_quantity' => 4],
    ['item_name' => 'Amoxicillin Capsules', 'stock_quantity' => 2]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Executive Dashboard - Medi Lanka HMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        :root {
            --sidebar-width: 280px;
            --sidebar-bg: #0f172a;
            --primary-blue: #0284c7;
            --primary-gradient: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            --bg-body: #f8fafc;
            --card-border: #e2e8f0;
            --card-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.05);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-body);
            color: #0f172a;
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
        }

        .sidebar-brand {
            padding: 1.75rem 1.5rem;
            font-size: 1.4rem;
            font-weight: 800;
            color: #ffffff;
            display: flex;
            align-items: center;
            gap: 0.85rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .sidebar-brand span.highlight {
            background: linear-gradient(135deg, #38bdf8 0%, #0284c7 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .sidebar-menu {
            padding: 1.25rem 1rem;
            overflow-y: auto;
            flex-grow: 1;
        }

        .sidebar-menu::-webkit-scrollbar { width: 4px; }
        .sidebar-menu::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.2); border-radius: 4px; }

        .menu-category {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: #38bdf8;
            font-weight: 700;
            margin: 1.5rem 0 0.75rem 0.75rem;
            opacity: 0.8;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            padding: 0.8rem 1rem;
            color: #cbd5e1;
            text-decoration: none;
            border-radius: 0.85rem;
            font-weight: 500;
            font-size: 0.92rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            margin-bottom: 0.35rem;
        }

        .sidebar-link:hover {
            background: rgba(255, 255, 255, 0.08);
            color: #ffffff;
            transform: translateX(6px);
        }

        .sidebar-link.active {
            background: var(--primary-gradient);
            color: #ffffff;
            font-weight: 600;
            box-shadow: 0 8px 20px rgba(2, 132, 199, 0.3);
        }

        .sidebar-link i { font-size: 1.2rem; }

        /* Main Wrapper */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Top Navbar */
        .top-navbar {
            height: 85px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--card-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2.5rem;
            position: sticky;
            top: 0;
            z-index: 1030;
        }

        /* Hero Promo Banner */
        .hero-banner {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 50%, #0f172a 100%);
            border-radius: 1.5rem;
            color: #ffffff;
            padding: 2.5rem 3rem;
            position: relative;
            overflow: hidden;
            box-shadow: 0 20px 40px -15px rgba(2, 132, 199, 0.3);
        }

        .hero-banner::after {
            content: '';
            position: absolute;
            right: -50px;
            bottom: -50px;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            pointer-events: none;
        }

        /* Global Smart Search Bar */
        .global-search-card {
            background: #ffffff;
            border: 1px solid var(--card-border);
            border-radius: 1.25rem;
            padding: 1.5rem 2rem;
            box-shadow: var(--card-shadow);
        }

        /* Quick Access Service Cards */
        .service-card {
            background: #ffffff;
            border: 1px solid var(--card-border);
            border-radius: 1.15rem;
            padding: 1.25rem;
            text-decoration: none;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 1rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.01);
            transition: all 0.3s ease;
        }

        .service-card:hover {
            border-color: #0284c7;
            transform: translateY(-4px);
            box-shadow: 0 12px 25px -5px rgba(2, 132, 199, 0.15);
            background: #f0f9ff;
        }

        .service-icon {
            width: 48px;
            height: 48px;
            border-radius: 0.85rem;
            background: #e0f2fe;
            color: #0284c7;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            flex-shrink: 0;
        }

        /* Metric Cards */
        .metric-card {
            background: #ffffff;
            border: 1px solid var(--card-border);
            border-radius: 1.25rem;
            padding: 1.75rem;
            box-shadow: var(--card-shadow);
            transition: transform 0.3s ease;
        }
        .metric-card:hover { transform: translateY(-3px); }

        .content-card {
            background: #ffffff;
            border: 1px solid var(--card-border);
            border-radius: 1.25rem;
            padding: 2rem;
            box-shadow: var(--card-shadow);
        }

        /* Mini Calendar Widget Styling */
        .calendar-widget {
            background: #ffffff;
            border: 1px solid var(--card-border);
            border-radius: 1.25rem;
            padding: 1.5rem;
            box-shadow: var(--card-shadow);
        }
        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 4px;
            text-align: center;
        }
        .calendar-day-name {
            font-size: 0.75rem;
            font-weight: 700;
            color: #94a3b8;
            padding-bottom: 0.5rem;
        }
        .calendar-date {
            font-size: 0.85rem;
            padding: 0.5rem 0;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.2s;
            font-weight: 600;
            color: #334155;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 32px;
            width: 32px;
            margin: auto;
        }
        .calendar-date:hover {
            background: #f1f5f9;
            color: #0284c7;
        }
        .calendar-date.active {
            background: var(--primary-gradient);
            color: #ffffff;
            box-shadow: 0 4px 10px rgba(2, 132, 199, 0.3);
        }

        /* Activity Log Timeline Item */
        .activity-item {
            display: flex;
            gap: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px dashed #f1f5f9;
            margin-bottom: 1rem;
        }
        .activity-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
            margin-bottom: 0;
        }
        .activity-badge {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

    <!-- Sidebar Navigation -->
    <nav class="sidebar">
        <div class="sidebar-brand">
            <div class="text-white p-2 rounded-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 42px; height: 42px; background: #0284c7;"><i class="bi bi-hospital-fill fs-5"></i></div>
            <span>Medi <span class="highlight">Lanka</span></span>
        </div>
        <div class="sidebar-menu">
            <div class="menu-category">Overview</div>
            <a href="dashboard.php" class="sidebar-link active"><i class="bi bi-grid-1x2-fill"></i> Dashboard</a>
            
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
            <a href="doctor_portal.php" class="sidebar-link"><i class="bi bi-shield-lock-fill"></i> Doctor Portal</a>
        </div>
    </nav>

    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <!-- Top Navbar -->
        <header class="top-navbar">
            <div>
                <h4 class="fw-bold text-dark mb-0 tracking-tight">Executive Health Portal</h4>
                <p class="text-muted small mb-0">Welcome back to Medi Lanka advanced command center.</p>
            </div>
            <div class="d-flex align-items-center gap-3">
                <!-- Notification Bell Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-light border position-relative rounded-circle p-2 shadow-sm d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-bell-fill text-secondary fs-5"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem;">
                            <?php echo count($low_stock_items) + 1; ?>
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-3" style="width: 320px; border-radius: 1rem;">
                        <li class="dropdown-header text-uppercase fw-bold text-muted small px-0 pb-2 border-bottom">System Live Alerts</li>
                        
                        <!-- Emergency Alert Item -->
                        <li>
                            <a class="dropdown-item px-0 py-2 border-bottom text-wrap d-flex gap-2 align-items-start" href="wards.php">
                                <div class="bg-danger bg-opacity-10 text-danger p-2 rounded-2 mt-1"><i class="bi bi-exclamation-triangle-fill"></i></div>
                                <div>
                                    <span class="fw-bold text-dark d-block small">ICU Emergency Admission</span>
                                    <span class="text-muted" style="font-size: 0.75rem;">Patient #102 requires immediate critical observation.</span>
                                </div>
                            </a>
                        </li>

                        <!-- Low Stock Pharmacy Items -->
                        <?php foreach($low_stock_items as $item): ?>
                        <li>
                            <a class="dropdown-item px-0 py-2 border-bottom text-wrap d-flex gap-2 align-items-start" href="inventory.php">
                                <div class="bg-warning bg-opacity-10 text-warning p-2 rounded-2 mt-1"><i class="bi bi-capsule"></i></div>
                                <div>
                                    <span class="fw-bold text-dark d-block small">Low Stock: <?php echo htmlspecialchars($item['item_name']); ?></span>
                                    <span class="text-muted" style="font-size: 0.75rem;">Only <?php echo $item['stock_quantity']; ?> units remaining in inventory.</span>
                                </div>
                            </a>
                        </li>
                        <?php endforeach; ?>

                        <li class="pt-2 text-center">
                            <a href="inventory.php" class="text-primary fw-bold text-decoration-none small">View All Alerts</a>
                        </li>
                    </ul>
                </div>

                <div class="d-flex align-items-center gap-2 bg-white px-3 py-2 rounded-pill border shadow-sm">
                    <span class="bg-success rounded-circle" style="width: 8px; height: 8px; display:inline-block;"></span>
                    <span class="text-secondary small fw-medium">Admin: <strong class="text-dark"><?php echo htmlspecialchars($_SESSION['username'] ?? 'Administrator'); ?></strong></span>
                </div>
                <a href="logout.php" class="btn btn-outline-danger btn-sm px-3.5 py-2 rounded-pill fw-semibold d-flex align-items-center gap-1 shadow-sm"><i class="bi bi-box-arrow-right"></i> Logout</a>
            </div>
        </header>

        <!-- Main Body Content -->
        <div class="p-4 p-md-5">
            
            <!-- Hero Promo Banner -->
            <div class="hero-banner mb-4">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <span class="badge bg-white bg-opacity-25 text-white px-3 py-1 rounded-pill fw-semibold mb-3 small"><i class="bi bi-stars me-1"></i> 2026 Smart Healthcare System</span>
                        <h1 class="fw-extrabold display-6 mb-2">Consult Top Specialists & Manage Hospital Care Instantly</h1>
                        <p class="text-light opacity-85 mb-4">Seamless video consultations, fast appointment tracking, and complete digital records with zero wait time.</p>
                        <a href="manage_appointments.php" class="btn btn-light text-primary fw-bold px-4 py-2.5 rounded-pill shadow-sm">Book Appointment Now <i class="bi bi-arrow-right ms-1"></i></a>
                    </div>
                </div>
            </div>

            <!-- ================= 5. GLOBAL SMART SEARCH BAR ================= -->
            <div class="global-search-card mb-5">
                <h5 class="fw-bold text-dark mb-3"><i class="bi bi-search text-primary me-2"></i> Global Smart Patient Search Bar</h5>
                <form action="dashboard.php" method="GET" class="row g-3">
                    <div class="col-md-9">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0 text-muted ps-3"><i class="bi bi-person-lines-fill"></i></span>
                            <input type="text" name="global_search" class="form-control bg-light border-0 py-2.5" placeholder="Search by Patient Name, Phone Number or Medical Diagnosis..." value="<?php echo htmlspecialchars($_GET['global_search'] ?? ''); ?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100 fw-bold py-2.5 shadow-sm"><i class="bi bi-search me-1"></i> Search Records</button>
                    </div>
                </form>

                <!-- Search Results Box if searched -->
                <?php if (isset($_GET['global_search'])): ?>
                    <div class="mt-4 pt-3 border-top">
                        <h6 class="fw-bold text-secondary small text-uppercase mb-3">Search Results for: "<?php echo htmlspecialchars($_GET['global_search']); ?>"</h6>
                        <?php if ($global_search_results && $global_search_results->num_rows > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-sm align-middle mb-0">
                                    <thead class="table-light text-muted" style="font-size: 0.75rem;">
                                        <tr>
                                            <th>ID</th>
                                            <th>Patient Name</th>
                                            <th>Phone</th>
                                            <th>Diagnosis</th>
                                            <th class="text-end">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($s_row = $global_search_results->fetch_assoc()): ?>
                                            <tr>
                                                <td class="fw-bold text-primary">#<?php echo $s_row['id']; ?></td>
                                                <td class="fw-semibold text-dark"><?php echo htmlspecialchars($s_row['name']); ?></td>
                                                <td><?php echo htmlspecialchars($s_row['phone']); ?></td>
                                                <td><span class="badge bg-info bg-opacity-10 text-info px-2 py-1"><?php echo htmlspecialchars($s_row['diagnosis'] ?? 'General Checkup'); ?></span></td>
                                                <td class="text-end">
                                                    <a href="manage_patients.php?search=<?php echo urlencode($s_row['name']); ?>" class="btn btn-sm btn-outline-primary py-0 px-2">View File</a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted small mb-0">No matching patients or diagnoses found in database.</p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Metric Cards Row -->
            <div class="row g-4 mb-5">
                <div class="col-md-4">
                    <div class="metric-card d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-uppercase text-muted fw-bold" style="font-size: 0.72rem; letter-spacing: 0.05em;">Total Patients</span>
                            <h2 class="fw-extrabold text-dark mt-1 mb-2 display-6"><?php echo $patients_count; ?></h2>
                            <span class="badge bg-success bg-opacity-10 text-success fw-semibold px-2.5 py-1 rounded-pill small"><i class="bi bi-arrow-up-right me-1"></i> Active registry</span>
                        </div>
                        <div class="service-icon" style="background: #dcfce7; color: #16a34a;"><i class="bi bi-people-fill"></i></div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="metric-card d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-uppercase text-muted fw-bold" style="font-size: 0.72rem; letter-spacing: 0.05em;">Clinical Doctors</span>
                            <h2 class="fw-extrabold text-dark mt-1 mb-2 display-6"><?php echo $doctors_count; ?></h2>
                            <span class="badge bg-primary bg-opacity-10 text-primary fw-semibold px-2.5 py-1 rounded-pill small"><i class="bi bi-shield-check me-1"></i> Verified staff</span>
                        </div>
                        <div class="service-icon"><i class="bi bi-heart-pulse-fill"></i></div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="metric-card d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-uppercase text-muted fw-bold" style="font-size: 0.72rem; letter-spacing: 0.05em;">Appointments</span>
                            <h2 class="fw-extrabold text-dark mt-1 mb-2 display-6"><?php echo $appointments_count; ?></h2>
                            <span class="badge bg-warning bg-opacity-10 text-dark fw-semibold px-2.5 py-1 rounded-pill small"><i class="bi bi-clock-history me-1"></i> Scheduled queue</span>
                        </div>
                        <div class="service-icon" style="background: #fef3c7; color: #d97706;"><i class="bi bi-calendar-check-fill"></i></div>
                    </div>
                </div>
            </div>

            <!-- ADVANCED CHARTS & CALENDAR SECTION -->
            <div class="row g-4 mb-5">
                <!-- Monthly Revenue & Expense Bar Chart -->
                <div class="col-lg-8">
                    <div class="content-card h-100">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h5 class="fw-bold text-dark mb-1">Monthly Financial Analytics</h5>
                                <p class="text-muted small mb-0">Overview of hospital monthly revenue vs expenses.</p>
                            </div>
                            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-1.5 rounded-pill fw-semibold small">2026 Live Data</span>
                        </div>
                        <div style="position: relative; height: 310px; width: 100%;">
                            <canvas id="revenueExpenseChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Interactive Mini Calendar Widget (Feature 3) -->
                <div class="col-lg-4">
                    <div class="calendar-widget h-100">
                        <div class="calendar-header">
                            <h6 class="fw-bold text-dark mb-0" id="calendarMonthYear">August 2026</h6>
                            <div class="d-flex gap-1">
                                <button class="btn btn-light btn-sm border p-1 rounded-2" onclick="changeMonth(-1)"><i class="bi bi-chevron-left"></i></button>
                                <button class="btn btn-light btn-sm border p-1 rounded-2" onclick="changeMonth(1)"><i class="bi bi-chevron-right"></i></button>
                            </div>
                        </div>
                        <div class="calendar-grid mb-2">
                            <div class="calendar-day-name">Su</div>
                            <div class="calendar-day-name">Mo</div>
                            <div class="calendar-day-name">Tu</div>
                            <div class="calendar-day-name">We</div>
                            <div class="calendar-day-name">Th</div>
                            <div class="calendar-day-name">Fr</div>
                            <div class="calendar-day-name">Sa</div>
                        </div>
                        <div class="calendar-grid" id="calendarDays">
                            <!-- Populated dynamically via JS -->
                        </div>
                        <div class="mt-3 pt-3 border-top">
                            <span class="d-block fw-bold text-dark small mb-1"><i class="bi bi-clock-history text-primary me-1"></i> Duty Roster Note</span>
                            <span class="text-muted" style="font-size: 0.75rem;">Click any date to check active doctor duty shifts & booked visits.</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Appointments Status Breakdown & Quick Access Grid Row -->
            <div class="row g-4 mb-5">
                <div class="col-lg-4">
                    <div class="content-card h-100">
                        <div class="mb-4">
                            <h5 class="fw-bold text-dark mb-1">Appointment Status</h5>
                            <p class="text-muted small mb-0">Breakdown of queues & visits.</p>
                        </div>
                        <div style="position: relative; height: 220px; width: 100%;" class="d-flex justify-content-center align-items-center">
                            <canvas id="appointmentStatusChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Quick Access Services Grid Container -->
                <div class="col-lg-8">
                    <div class="content-card h-100">
                        <h5 class="fw-bold text-dark mb-1">Quick Access Services</h5>
                        <p class="text-muted small mb-4">Direct shortcut grid to administrative hospital services.</p>
                        
                        <div class="row g-3">
                            <div class="col-md-4">
                                <a href="manage_patients.php" class="service-card">
                                    <div class="service-icon"><i class="bi bi-people"></i></div>
                                    <div>
                                        <span class="d-block fw-bold small text-dark">Patients</span>
                                        <span class="text-muted" style="font-size: 0.75rem;">Registry</span>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="manage_doctors.php" class="service-card">
                                    <div class="service-icon" style="background: #e0f2fe; color: #0284c7;"><i class="bi bi-heart-pulse"></i></div>
                                    <div>
                                        <span class="d-block fw-bold small text-dark">Doctors</span>
                                        <span class="text-muted" style="font-size: 0.75rem;">Staff Profiles</span>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="manage_appointments.php" class="service-card">
                                    <div class="service-icon" style="background: #fef3c7; color: #d97706;"><i class="bi bi-calendar-event"></i></div>
                                    <div>
                                        <span class="d-block fw-bold small text-dark">Appointments</span>
                                        <span class="text-muted" style="font-size: 0.75rem;">Queues</span>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="billing.php" class="service-card">
                                    <div class="service-icon" style="background: #f3e8ff; color: #9333ea;"><i class="bi bi-receipt"></i></div>
                                    <div>
                                        <span class="d-block fw-bold small text-dark">Billing</span>
                                        <span class="text-muted" style="font-size: 0.75rem;">Invoices</span>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="inventory.php" class="service-card">
                                    <div class="service-icon" style="background: #ccfbf1; color: #0d9488;"><i class="bi bi-box-seam"></i></div>
                                    <div>
                                        <span class="d-block fw-bold small text-dark">Pharmacy</span>
                                        <span class="text-muted" style="font-size: 0.75rem;">Inventory</span>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="wards.php" class="service-card">
                                    <div class="service-icon" style="background: #fee2e2; color: #dc2626;"><i class="bi bi-door-open"></i></div>
                                    <div>
                                        <span class="d-block fw-bold small text-dark">Beds & Wards</span>
                                        <span class="text-muted" style="font-size: 0.75rem;">Rooms</span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ================= 4. QUICK EXPORT & PRINT OPTIONS & 6. LIVE ACTIVITY LOG ================= -->
            <div class="row g-4 mb-5">
                <!-- Recent Appointments Table Card with Export/Print Buttons -->
                <div class="col-lg-8">
                    <div class="content-card h-100" id="printableAppointmentsArea">
                        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
                            <div>
                                <h5 class="fw-bold text-dark mb-1">Recent Appointments</h5>
                                <p class="text-muted small mb-0">Latest bookings logged into Medi Lanka database.</p>
                            </div>
                            <!-- Export and Print Action Buttons -->
                            <div class="d-flex gap-2">
                                <button onclick="exportTableToCSV('appointments.csv')" class="btn btn-outline-success btn-sm fw-semibold px-2.5 py-1.5 rounded-3 d-flex align-items-center gap-1"><i class="bi bi-file-earmark-excel"></i> Excel</button>
                                <button onclick="window.print()" class="btn btn-outline-primary btn-sm fw-semibold px-2.5 py-1.5 rounded-3 d-flex align-items-center gap-1"><i class="bi bi-printer"></i> Print</button>
                                <a href="manage_appointments.php" class="btn btn-light border btn-sm fw-semibold px-3 py-1.5 rounded-3 text-secondary">View All</a>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table align-middle mb-0" id="appointmentsTable">
                                <thead class="table-light text-uppercase text-muted" style="font-size: 0.75rem; letter-spacing: 0.05em;">
                                    <tr>
                                        <th class="py-3 rounded-start">ID</th>
                                        <th class="py-3">Patient Name</th>
                                        <th class="py-3">Consultant Doctor</th>
                                        <th class="py-3">Date</th>
                                        <th class="py-3 text-end rounded-end">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($recent_appointments && $recent_appointments->num_rows > 0): ?>
                                        <?php while ($row = $recent_appointments->fetch_assoc()): ?>
                                            <tr>
                                                <td class="fw-bold text-primary">#<?php echo $row['id']; ?></td>
                                                <td class="fw-semibold text-dark"><?php echo htmlspecialchars($row['patient_name']); ?></td>
                                                <td class="text-secondary"><?php echo htmlspecialchars($row['doctor_name']); ?></td>
                                                <td class="text-muted small"><?php echo htmlspecialchars($row['appointment_date'] ?? 'N/A'); ?></td>
                                                <td class="text-end">
                                                    <span class="badge bg-success bg-opacity-10 text-success px-2.5 py-1.5 fw-semibold">Confirmed</span>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted small">No recent appointments found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- 6. Live Activity Log / Audit Trail -->
                <div class="col-lg-4">
                    <div class="content-card h-100">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="fw-bold text-dark mb-0">System Activity Log</h5>
                            <span class="badge bg-secondary bg-opacity-10 text-secondary px-2.5 py-1 rounded-pill small">Live Audit</span>
                        </div>
                        
                        <div class="activity-timeline">
                            <div class="activity-item">
                                <div class="activity-badge bg-primary bg-opacity-10 text-primary"><i class="bi bi-person-plus-fill"></i></div>
                                <div>
                                    <span class="d-block fw-bold text-dark small">New Patient Registered</span>
                                    <span class="text-muted" style="font-size: 0.75rem;">Admin added patient record #142 (Kamal Perera).</span>
                                    <span class="d-block text-secondary mt-1" style="font-size: 0.68rem;">10 minutes ago</span>
                                </div>
                            </div>

                            <div class="activity-item">
                                <div class="activity-badge bg-success bg-opacity-10 text-success"><i class="bi bi-receipt"></i></div>
                                <div>
                                    <span class="d-block fw-bold text-dark small">Bill Payment Verified</span>
                                    <span class="text-muted" style="font-size: 0.75rem;">Cashier settled invoice INV-2026-089.</span>
                                    <span class="d-block text-secondary mt-1" style="font-size: 0.68rem;">42 minutes ago</span>
                                </div>
                            </div>

                            <div class="activity-item">
                                <div class="activity-badge bg-warning bg-opacity-10 text-warning"><i class="bi bi-calendar-check"></i></div>
                                <div>
                                    <span class="d-block fw-bold text-dark small">Appointment Scheduled</span>
                                    <span class="text-muted" style="font-size: 0.75rem;">Dr. Silva channel appointment booked.</span>
                                    <span class="d-block text-secondary mt-1" style="font-size: 0.68rem;">2 hours ago</span>
                                </div>
                            </div>

                            <div class="activity-item">
                                <div class="activity-badge bg-danger bg-opacity-10 text-danger"><i class="bi bi-capsule"></i></div>
                                <div>
                                    <span class="d-block fw-bold text-dark small">Pharmacy Stock Updated</span>
                                    <span class="text-muted" style="font-size: 0.75rem;">Amoxicillin inventory levels adjusted.</span>
                                    <span class="d-block text-secondary mt-1" style="font-size: 0.68rem;">5 hours ago</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Chart.js & Interactive Calendar & Export Script -->
    <script>
        // 1. Monthly Revenue & Expense Bar Chart
        const ctxBar = document.getElementById('revenueExpenseChart').getContext('2d');
        const revenueExpenseChart = new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [
                    {
                        label: 'Revenue ($)',
                        data: [12000, 19000, 15000, 22000, 28000, 31000, 25000, 34000, 30000, 38000, 42000, 45000],
                        backgroundColor: '#0284c7',
                        borderRadius: 6
                    },
                    {
                        label: 'Expenses ($)',
                        data: [8000, 11000, 9500, 14000, 16000, 18000, 15000, 20000, 17000, 22000, 24000, 26000],
                        backgroundColor: '#cbd5e1',
                        borderRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 12, font: { family: 'Plus Jakarta Sans', weight: '600' } }
                    }
                },
                scales: {
                    x: { grid: { display: false } },
                    y: { grid: { color: '#f1f5f9' }, ticks: { beginAtZero: true } }
                }
            }
        });

        // 2. Appointments Status Breakdown Donut Chart
        const ctxDonut = document.getElementById('appointmentStatusChart').getContext('2d');
        const appointmentStatusChart = new Chart(ctxDonut, {
            type: 'doughnut',
            data: {
                labels: ['Confirmed', 'Completed', 'Cancelled'],
                datasets: [{
                    data: [<?php echo max(1, $appointments_count); ?>, 18, 4],
                    backgroundColor: ['#0284c7', '#10b981', '#ef4444'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 12, font: { family: 'Plus Jakarta Sans', weight: '600' } }
                    }
                },
                cutout: '70%'
            }
        });

        // 3. Interactive Mini Calendar Script
        let today = new Date();
        let currentMonth = today.getMonth();
        let currentYear = today.getFullYear();

        const months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

        function renderCalendar(month, year) {
            let firstDay = new Date(year, month).getDay();
            let daysInMonth = 32 - new Date(year, month, 32).getDate();

            let calendarDays = document.getElementById("calendarDays");
            calendarDays.innerHTML = "";
            document.getElementById("calendarMonthYear").innerText = months[month] + " " + year;

            let cellCount = 0;
            for (let i = 0; i < firstDay; i++) {
                let cell = document.createElement("div");
                calendarDays.appendChild(cell);
                cellCount++;
            }

            for (let i = 1; i <= daysInMonth; i++) {
                let cell = document.createElement("div");
                cell.classList.add("calendar-date");
                cell.innerText = i;

                if (i === today.getDate() && year === today.getFullYear() && month === today.getMonth()) {
                    cell.classList.add("active");
                }

                cell.onclick = function() {
                    document.querySelectorAll('.calendar-date').forEach(d => d.classList.remove('active'));
                    cell.classList.add('active');
                };

                calendarDays.appendChild(cell);
                cellCount++;
            }
        }

        function changeMonth(direction) {
            currentMonth += direction;
            if (currentMonth > 11) {
                currentMonth = 0;
                currentYear++;
            } else if (currentMonth < 0) {
                currentMonth = 11;
                currentYear--;
            }
            renderCalendar(currentMonth, currentYear);
        }

        renderCalendar(currentMonth, currentYear);

        // 4. Quick Export Table to CSV Function
        function exportTableToCSV(filename) {
            let csv = [];
            let rows = document.querySelectorAll("#appointmentsTable tr");
            
            for (let i = 0; i < rows.length; i++) {
                let row = [], cols = rows[i].querySelectorAll("td, th");
                for (let j = 0; j < cols.length; j++) 
                    row.push('"' + cols[j].innerText.replace(/"/g, '""') + '"');
                csv.push(row.join(","));
            }

            // Download CSV file
            let csvFile = new Blob([csv.join("\n")], { type: "text/csv" });
            let downloadLink = document.createElement("a");
            downloadLink.download = filename;
            downloadLink.href = window.URL.createObjectURL(csvFile);
            downloadLink.style.display = "none";
            document.body.appendChild(downloadLink);
            downloadLink.click();
            document.body.removeChild(downloadLink);
        }
    </script>
</body>
</html>