<?php
// api/submit-consult.php
header('Content-Type: application/json');

// Prevent direct access to this file from URL (only POST allowed)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Method not allowed"]);
    exit;
}

require_once 'db_config.php';

// Sanitize and validate inputs
$full_name = trim(filter_input(INPUT_POST, 'full_name', FILTER_SANITIZE_STRING));
$phone_number = trim(filter_input(INPUT_POST, 'phone_number', FILTER_SANITIZE_STRING));
$email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
$highest_education = trim(filter_input(INPUT_POST, 'highest_education', FILTER_SANITIZE_STRING));
$result_score = trim(filter_input(INPUT_POST, 'result_score', FILTER_SANITIZE_STRING));
$passing_year = trim(filter_input(INPUT_POST, 'passing_year', FILTER_SANITIZE_STRING));
$interested_to_study = trim(filter_input(INPUT_POST, 'interested_to_study', FILTER_SANITIZE_STRING));
$has_passport = trim(filter_input(INPUT_POST, 'has_passport', FILTER_SANITIZE_STRING));
$budget = trim(filter_input(INPUT_POST, 'budget', FILTER_SANITIZE_STRING));

// Basic validation to ensure required fields are not empty
if (empty($full_name) || empty($phone_number) || empty($email) || empty($highest_education) || empty($result_score) || empty($passing_year) || empty($interested_to_study) || empty($has_passport) || empty($budget)) {
    echo json_encode(["status" => "error", "message" => "All fields are required."]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["status" => "error", "message" => "Invalid email address."]);
    exit;
}

try {
    // Insert into database using prepared statements to prevent SQL injection
    $sql = "INSERT INTO consultations (full_name, phone_number, email, highest_education, result_score, passing_year, interested_to_study, has_passport, budget) 
            VALUES (:full_name, :phone_number, :email, :highest_education, :result_score, :passing_year, :interested_to_study, :has_passport, :budget)";
    
    $stmt = $pdo->prepare($sql);
    
    $stmt->bindParam(':full_name', $full_name);
    $stmt->bindParam(':phone_number', $phone_number);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':highest_education', $highest_education);
    $stmt->bindParam(':result_score', $result_score);
    $stmt->bindParam(':passing_year', $passing_year);
    $stmt->bindParam(':interested_to_study', $interested_to_study);
    $stmt->bindParam(':has_passport', $has_passport);
    $stmt->bindParam(':budget', $budget);
    
    if ($stmt->execute()) {
        
        // Send email notification
        $to = "officeysstudyfocus@gmail.com, support@ysstudyfocus.com";
        $subject = "New Consultation Booking: " . $full_name;
        $message = "
        <html>
        <head>
        <title>New Consultation Booking</title>
        </head>
        <body>
        <h2>New Student Consultation</h2>
        <p><strong>Name:</strong> {$full_name}</p>
        <p><strong>Phone Number:</strong> {$phone_number}</p>
        <p><strong>Email:</strong> {$email}</p>
        <p><strong>Highest Education:</strong> {$highest_education}</p>
        <p><strong>Result/Score:</strong> {$result_score}</p>
        <p><strong>Passing Year:</strong> {$passing_year}</p>
        <p><strong>Interested to Study:</strong> {$interested_to_study}</p>
        <p><strong>Has Passport:</strong> {$has_passport}</p>
        <p><strong>Budget:</strong> {$budget}</p>
        </body>
        </html>
        ";
        
        // Always set content-type when sending HTML email
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: no-reply@ysstudyfocus.com" . "\r\n";
        
        // Attempt to send email, but don't fail the whole request if it fails
        @mail($to, $subject, $message, $headers);
        
        echo json_encode(["status" => "success", "message" => "Your consultation request has been submitted successfully."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to save data."]);
    }

} catch (\PDOException $e) {
    // Log error in production, but show generic error to user
    error_log("Database error: " . $e->getMessage());
    echo json_encode(["status" => "error", "message" => "A system error occurred. Please try again later."]);
}
?>
