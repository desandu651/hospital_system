<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

include('db.php');

if (!isset($_GET['id'])) {
    die("Invoice ID not provided.");
}

$invoice_id = intval($_GET['id']);

// Fetch Invoice, Patient, and Doctor Details using Prepared Statements
$stmt = $conn->prepare("
    SELECT inv.*, p.full_name AS patient_name, p.phone AS patient_phone, p.nic_passport, d.full_name AS doctor_name, d.specialization 
    FROM invoices inv
    JOIN patients p ON inv.patient_id = p.id
    JOIN doctors d ON inv.doctor_id = d.id
    WHERE inv.id = ?
");
$stmt->bind_param("i", $invoice_id);
$stmt->execute();
$invoice = $stmt->get_result()->fetch_assoc();

if (!$invoice) {
    die("Invoice not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #INV-<?php echo sprintf('%04d', $invoice['id']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .invoice-card { max-width: 800px; margin: 30px auto; background: #fff; border-radius: 10px; }
        @media print {
            .no-print { display: none !important; }
            body { background: #fff; }
            .invoice-card { margin: 0; box-shadow: none !important; }
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="text-end mt-4 no-print">
            <button onclick="window.print()" class="btn btn-primary fw-bold">🖨️ Print / Download PDF</button>
            <a href="billing.php" class="btn btn-secondary">Close</a>
        </div>

        <div class="card shadow-lg border-0 p-5 invoice-card">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
                <div>
                    <h2 class="text-primary fw-bold mb-0">🏥 CityCare Hospital</h2>
                    <small class="text-muted">123 Healthcare Ave, Colombo | Phone: +94 11 234 5678</small>
                </div>
                <div class="text-end">
                    <h3 class="fw-bold text-secondary mb-0">INVOICE</h3>
                    <span class="badge bg-dark">#INV-<?php echo sprintf('%04d', $invoice['id']); ?></span>
                </div>
            </div>

            <!-- Invoice Details -->
            <div class="row mb-4">
                <div class="col-6">
                    <h6 class="text-uppercase text-muted small fw-bold">Billed To:</h6>
                    <h5><b><?php echo htmlspecialchars($invoice['patient_name']); ?></b></h5>
                    <p class="mb-0 text-muted">Phone: <?php echo htmlspecialchars($invoice['patient_phone']); ?></p>
                    <p class="text-muted">NIC/Passport: <?php echo htmlspecialchars($invoice['nic_passport']); ?></p>
                </div>
                <div class="col-6 text-end">
                    <h6 class="text-uppercase text-muted small fw-bold">Doctor Attended:</h6>
                    <h5><b>Dr. <?php echo htmlspecialchars($invoice['doctor_name']); ?></b></h5>
                    <p class="mb-0 text-muted"><?php echo htmlspecialchars($invoice['specialization']); ?></p>
                    <p class="text-muted">Date: <?php echo date('Y-m-d H:i', strtotime($invoice['created_at'])); ?></p>
                </div>
            </div>

            <!-- Fee Breakdown Table -->
            <table class="table table-bordered mb-4">
                <thead class="table-light">
                    <tr>
                        <th>Description</th>
                        <th class="text-end">Amount (LKR)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Doctor Consultation Fee</td>
                        <td class="text-end"><?php echo number_format($invoice['consultation_fee'], 2); ?></td>
                    </tr>
                    <tr>
                        <td>Prescription & Medicines Charge</td>
                        <td class="text-end"><?php echo number_format($invoice['medicine_fee'], 2); ?></td>
                    </tr>
                    <tr>
                        <td>Hospital Service & Lab Charges</td>
                        <td class="text-end"><?php echo number_format($invoice['other_charges'], 2); ?></td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="table-active">
                        <th class="text-uppercase">Total Amount Payable:</th>
                        <th class="text-end text-primary fs-5">LKR <?php echo number_format($invoice['total_amount'], 2); ?></th>
                    </tr>
                </tfoot>
            </table>

            <!-- Payment Status Footer -->
            <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                <div>
                    Status: 
                    <span class="badge <?php echo ($invoice['status'] == 'Paid') ? 'bg-success' : 'bg-danger'; ?> fs-6">
                        <?php echo strtoupper($invoice['status']); ?>
                    </span>
                </div>
                <div class="text-muted small">
                    Thank you for choosing CityCare Hospital!
                </div>
            </div>
        </div>
    </div>

</body>
</html>