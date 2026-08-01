<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

include('db.php');
require('fpdf/fpdf.php'); // Include FPDF library

// Check if bill ID is provided
if (isset($_GET['bill_id'])) {
    $bill_id = intval($_GET['bill_id']);

    // Fetch billing and patient details from database
    $query = "
        SELECT b.*, p.full_name, p.phone, p.email 
        FROM billing b 
        JOIN patients p ON b.patient_id = p.id 
        WHERE b.id = $bill_id
    ";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $bill = $result->fetch_assoc();

        // Create a new PDF document class extending FPDF
        class PDF extends FPDF {
            // Page header
            function Header() {
                // Hospital Title
                $this->SetFont('Arial', 'B', 16);
                $this->Cell(0, 10, 'Hospital Management System', 0, 1, 'C');
                $this->SetFont('Arial', '', 12);
                $this->Cell(0, 6, 'Official Medical Invoice / Bill', 0, 1, 'C');
                $this->Ln(10);
            }

            // Page footer
            function Footer() {
                $this->SetY(-15);
                $this->SetFont('Arial', 'I', 8);
                $this->Cell(0, 10, 'Page ' . $this->PageNo() . ' | Thank you for choosing our hospital.', 0, 0, 'C');
            }
        }

        // Initialize PDF
        $pdf = new PDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 12);

        // Bill Information Header
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 8, 'Invoice Details:', 0, 1);
        $pdf->SetFont('Arial', '', 12);

        $pdf->Cell(40, 8, 'Bill ID:', 0, 0);
        $pdf->Cell(0, 8, '#' . $bill['id'], 0, 1);

        $pdf->Cell(40, 8, 'Patient Name:', 0, 0);
        $pdf->Cell(0, 8, $bill['full_name'], 0, 1);

        $pdf->Cell(40, 8, 'Phone:', 0, 0);
        $pdf->Cell(0, 8, $bill['phone'], 0, 1);

        $pdf->Cell(40, 8, 'Date:', 0, 0);
        $pdf->Cell(0, 8, $bill['created_at'] ?? date('Y-m-d'), 0, 1);

        $pdf->Ln(10);

        // Table Column Headers
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetFillColor(230, 230, 230);
        $pdf->Cell(130, 10, 'Description', 1, 0, 'L', true);
        $pdf->Cell(60, 10, 'Amount ($)', 1, 1, 'R', true);

        // Table Row Data
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(130, 10, 'Medical Services & Consultation Fee', 1, 0, 'L');
        $pdf->Cell(60, 10, number_format($bill['total_amount'], 2), 1, 1, 'R');

        // Total Amount Row
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(130, 10, 'Total Payable:', 1, 0, 'R');
        $pdf->Cell(60, 10, '$' . number_format($bill['total_amount'], 2), 1, 1, 'R');

        $pdf->Ln(10);
        
        // Payment Status
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(40, 8, 'Payment Status: ', 0, 0);
        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell(0, 8, $bill['status'], 0, 1);

        // Output the PDF for download ('D' forces download, 'I' displays in browser)
        $pdf->Output('D', 'Invoice_' . $bill['id'] . '.pdf');

    } else {
        echo "Invoice not found!";
    }
} else {
    echo "Invalid Request!";
}
?>