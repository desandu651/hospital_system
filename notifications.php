<?php
// 1. Email Notification Function (Using PHP mail function or SMTP)
function sendEmailNotification($to_email, $patient_name, $appointment_date, $doctor_name) {
    $subject = "Appointment Confirmation - Hospital Management System";
    
    $message = "
    <html>
    <head>
        <title>Appointment Confirmation</title>
    </head>
    <body>
        <h2>Dear " . htmlspecialchars($patient_name) . ",</h2>
        <p>Your appointment has been successfully booked!</p>
        <p><b>Doctor:</b> " . htmlspecialchars($doctor_name) . "</p>
        <p><b>Date & Time:</b> " . htmlspecialchars($appointment_date) . "</p>
        <p>Please arrive 15 minutes prior to your appointment time.</p>
        <br>
        <p>Thank you,<br><b>Hospital Management System</b></p>
    </body>
    </html>
    ";

    // Headers for HTML email
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: no-reply@hospitalsystem.com" . "\r\n";

    // Send email (Note: Localhost requires an SMTP server like Sendmail/PHPMailer for live emails)
    @mail($to_email, $subject, $message, $headers);
}

// 2. SMS Notification Function (Using Twilio API via cURL)
function sendSMSNotification($to_phone, $patient_name, $appointment_date) {
    $sid   = 'YOUR_TWILIO_ACCOUNT_SID'; // ඔබේ Twilio Account SID එක මෙහි දමන්න
    $token = 'YOUR_TWILIO_AUTH_TOKEN';    // ඔබේ Twilio Auth Token එක මෙහි දමන්න
    $from  = 'YOUR_TWILIO_PHONE_NUMBER';  // ඔබේ Twilio දුරකථන අංකය මෙහි දමන්න

    $message = "Dear $patient_name, your hospital appointment is confirmed for $appointment_date. Thank you!";

    $url = "https://api.twilio.com/2010-04-01/Accounts/$sid/Messages.json";
    
    $data = [
        'To'   => $to_phone,
        'From' => $from,
        'Body' => $message
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERPWD, "$sid:$token");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return $response;
}
?>