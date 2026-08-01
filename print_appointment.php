<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

include('db.php');

if (!isset($_GET['id'])) {
    header("Location: appointments.php");
    exit();
}

$id = $_GET['id'];

// Retrieve patient and doctor details related to the appointment
$sql = "
    SELECT a.id, 
           p.full_name AS patient_name, 
           p.age, 
           p.gender, 
           p.phone, 
           d.full_name AS doctor_name, 
           d.specialization, 
           a.appointment_date, 
           a.status 
    FROM appointments a
    JOIN patients p ON a.patient_id = p.id
    JOIN doctors d ON a.doctor_id = d.id
    WHERE a.id = $id
";

$result = $conn->query($sql);

if ($result->num_rows == 0) {
    die("Appointment record not found!");
}

$data = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Slip #<?php echo $data['id']; ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body { 
            background-color: #f8f9fa; 
        }

        .ticket {
            max-width: 500px;
            margin: 40px auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 12px;
            border: 2px dashed #0d6efd;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        /* Hide buttons and background when printing */
        @media print {
            .no-print { 
                display: none; 
            }

            body { 
                background: #fff; 
            }

            .ticket { 
                border: 1px solid #000;
                box-shadow: none;
                margin: 0 auto;
                width: 100%;
            }
        }
    </style>
</head>

<body>

    <div class="ticket">

        <div class="text-center mb-4">

            <h3 class="text-primary fw-bold">
                🏥 City Care Hospital
            </h3>

            <p class="text-muted mb-0">
                No. 123, Hospital Road, Colombo
            </p>

            <p class="text-muted">
                Tel: 011-2345678
            </p>

            <hr>

            <h5 class="fw-bold text-uppercase">
                Appointment Receipt
            </h5>

            <span class="badge bg-primary fs-6">
                Ticket ID: #<?php echo $data['id']; ?>
            </span>

        </div>


        <table class="table table-borderless fs-6">

            <tr>
                <th class="text-secondary">Patient Name:</th>
                <td class="fw-bold">
                    <?php echo $data['patient_name']; ?>
                </td>
            </tr>

            <tr>
                <th class="text-secondary">Age / Gender:</th>
                <td>
                    <?php echo $data['age']; ?> Yrs (<?php echo $data['gender']; ?>)
                </td>
            </tr>

            <tr>
                <th class="text-secondary">Phone Number:</th>
                <td>
                    <?php echo $data['phone']; ?>
                </td>
            </tr>

            <tr>
                <th class="text-secondary">Doctor Name:</th>
                <td class="text-success fw-bold">
                    <?php echo $data['doctor_name']; ?>
                </td>
            </tr>

            <tr>
                <th class="text-secondary">Specialization:</th>
                <td>
                    <?php echo $data['specialization']; ?>
                </td>
            </tr>

            <tr>
                <th class="text-secondary">Date:</th>
                <td>
                    <b class="text-danger">
                        <?php echo $data['appointment_date']; ?>
                    </b>
                </td>
            </tr>

            <tr>
                <th class="text-secondary">Status:</th>
                <td>
                    <span class="badge bg-warning text-dark">
                        <?php echo $data['status']; ?>
                    </span>
                </td>
            </tr>

        </table>


        <hr>

        <p class="text-center text-muted small mb-0">
            Please present this slip at the reception desk.
        </p>


        <!-- Action Buttons -->
        <div class="text-center no-print mt-4">

            <button onclick="window.print()" class="btn btn-primary me-2 fw-bold">
                🖨️ Print / Save PDF
            </button>

            <a href="appointments.php" class="btn btn-secondary">
                ⬅ Back
            </a>

        </div>


    </div>

</body>
</html>