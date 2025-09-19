<?php
// process_contact.php

// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set response header to JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

// Configuration
$config = [
    'to_email' => 'info@comart.in', // Change to your email
    'from_email' => 'noreply@comart.in', // Change to your domain email
    'company_name' => 'ComXR',
    'subject_prefix' => '[ComXR Contact Form]',
];

// Response function
function sendResponse($success, $message, $data = null) {
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    exit;
}

// Validate and sanitize input
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Check if form was submitted via POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(false, 'Invalid request method. Please use POST.');
}

try {
    // Get and validate form data
    $firstName = sanitizeInput($_POST['first_name'] ?? '');
    $lastName = sanitizeInput($_POST['last_name'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $phone = sanitizeInput($_POST['phone'] ?? '');
    $company = sanitizeInput($_POST['company'] ?? '');
    $service = sanitizeInput($_POST['service'] ?? '');
    $subject = sanitizeInput($_POST['subject'] ?? '');
    $message = sanitizeInput($_POST['message'] ?? '');
    $newsletter = isset($_POST['newsletter']) ? true : false;
    
    // Validation
    $errors = [];
    
    if (empty($firstName)) {
        $errors[] = 'First name is required';
    }
    
    if (empty($lastName)) {
        $errors[] = 'Last name is required';
    }
    
    if (empty($email)) {
        $errors[] = 'Email is required';
    } elseif (!validateEmail($email)) {
        $errors[] = 'Please enter a valid email address';
    }
    
    if (empty($subject)) {
        $errors[] = 'Subject is required';
    }
    
    if (empty($message)) {
        $errors[] = 'Message is required';
    }
    
    if (strlen($message) < 10) {
        $errors[] = 'Message must be at least 10 characters long';
    }
    
    if (!empty($errors)) {
        sendResponse(false, 'Please fix the following errors: ' . implode(', ', $errors));
    }
    
    // Service options mapping
    $serviceOptions = [
        'ar' => 'Augmented Reality (AR)',
        'vr' => 'Virtual Reality (VR)',
        'webxr' => 'WebXR',
        'ai-xr' => 'AI + XR',
        '3d-content' => '3D Content Creation',
        'consultation' => 'General Consultation'
    ];
    
    $serviceName = isset($serviceOptions[$service]) ? $serviceOptions[$service] : 'Not specified';
    
    // Prepare email content
    $fullName = $firstName . ' ' . $lastName;
    $emailSubject = $config['subject_prefix'] . ' ' . $subject;
    
    // HTML email template
    $htmlMessage = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Contact Form Submission</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; }
            .header { background: linear-gradient(135deg, #3b82f6, #10b981); color: white; padding: 20px; text-align: center; }
            .content { padding: 20px; background: #f9f9f9; }
            .field { margin-bottom: 15px; }
            .label { font-weight: bold; color: #555; }
            .value { margin-top: 5px; }
            .message-box { background: white; padding: 15px; border-left: 4px solid #3b82f6; margin: 15px 0; }
            .footer { background: #333; color: white; padding: 15px; text-align: center; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class='header'>
            <h2>New Contact Form Submission</h2>
            <p>From ComXR Website</p>
        </div>
        
        <div class='content'>
            <div class='field'>
                <div class='label'>Name:</div>
                <div class='value'>{$fullName}</div>
            </div>
            
            <div class='field'>
                <div class='label'>Email:</div>
                <div class='value'>{$email}</div>
            </div>
            
            <div class='field'>
                <div class='label'>Phone:</div>
                <div class='value'>" . ($phone ?: 'Not provided') . "</div>
            </div>
            
            <div class='field'>
                <div class='label'>Company:</div>
                <div class='value'>" . ($company ?: 'Not provided') . "</div>
            </div>
            
            <div class='field'>
                <div class='label'>Service Interest:</div>
                <div class='value'>{$serviceName}</div>
            </div>
            
            <div class='field'>
                <div class='label'>Subject:</div>
                <div class='value'>{$subject}</div>
            </div>
            
            <div class='message-box'>
                <div class='label'>Message:</div>
                <div class='value'>" . nl2br($message) . "</div>
            </div>
            
            <div class='field'>
                <div class='label'>Newsletter Subscription:</div>
                <div class='value'>" . ($newsletter ? 'Yes' : 'No') . "</div>
            </div>
            
            <div class='field'>
                <div class='label'>Submitted:</div>
                <div class='value'>" . date('F j, Y \a\t g:i A') . "</div>
            </div>
        </div>
        
        <div class='footer'>
            <p>This email was sent from the ComXR website contact form.</p>
            <p>ComXR - 386, Sane Guruji Building, Vir Savarkar Marg, Prabhadevi, Mumbai - 400025, India</p>
        </div>
    </body>
    </html>
    ";
    
    // Plain text version
    $textMessage = "
    New Contact Form Submission from ComXR Website
    
    Name: {$fullName}
    Email: {$email}
    Phone: " . ($phone ?: 'Not provided') . "
    Company: " . ($company ?: 'Not provided') . "
    Service Interest: {$serviceName}
    Subject: {$subject}
    Newsletter: " . ($newsletter ? 'Yes' : 'No') . "
    
    Message:
    {$message}
    
    Submitted: " . date('F j, Y \a\t g:i A') . "
    ";
    
    // Email headers
    $headers = [
        'MIME-Version: 1.0',
        'Content-Type: text/html; charset=UTF-8',
        'From: ' . $config['from_email'],
        'Reply-To: ' . $email,
        'X-Mailer: PHP/' . phpversion(),
        'X-Priority: 3',
        'Return-Path: ' . $config['from_email']
    ];
    
    // Send email
    $mailSent = mail(
        $config['to_email'],
        $emailSubject,
        $htmlMessage,
        implode("\r\n", $headers)
    );
    
    if ($mailSent) {
        // Log the submission (optional)
        $logEntry = date('Y-m-d H:i:s') . " - Contact form submission from {$fullName} ({$email})\n";
        file_put_contents('contact_log.txt', $logEntry, FILE_APPEND | LOCK_EX);
        
        // Send auto-response to user
        $autoResponseSubject = "Thank you for contacting ComXR";
        $autoResponseMessage = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; }
                .header { background: linear-gradient(135deg, #3b82f6, #10b981); color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; }
                .footer { background: #f8f8f8; padding: 15px; text-align: center; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class='header'>
                <h2>Thank You for Contacting ComXR!</h2>
            </div>
            
            <div class='content'>
                <p>Dear {$firstName},</p>
                
                <p>Thank you for reaching out to us! We have received your message and will get back to you within 24-48 hours.</p>
                
                <p><strong>Your submission details:</strong></p>
                <p>Subject: {$subject}</p>
                <p>Submitted: " . date('F j, Y \a\t g:i A') . "</p>
                
                <p>In the meantime, feel free to explore our services and recent projects on our website.</p>
                
                <p>Best regards,<br>The ComXR Team</p>
            </div>
            
            <div class='footer'>
                <p>ComXR - Obsessed With Innovation</p>
                <p>386, Sane Guruji Building, Vir Savarkar Marg, Prabhadevi, Mumbai - 400025, India</p>
                <p>Phone: +91 9988732889 | Email: info@comart.in</p>
            </div>
        </body>
        </html>
        ";
        
        $autoHeaders = [
            'MIME-Version: 1.0',
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . $config['from_email'],
            'X-Mailer: PHP/' . phpversion()
        ];
        
        mail($email, $autoResponseSubject, $autoResponseMessage, implode("\r\n", $autoHeaders));
        
        sendResponse(true, 'Thank you! Your message has been sent successfully. We will get back to you soon.', [
            'name' => $fullName,
            'email' => $email,
            'subject' => $subject
        ]);
    } else {
        sendResponse(false, 'Sorry, there was an error sending your message. Please try again or contact us directly.');
    }

} catch (Exception $e) {
    // Log error (in production, don't expose detailed error messages)
    error_log('Contact form error: ' . $e->getMessage());
    sendResponse(false, 'An unexpected error occurred. Please try again later.');
}
?>
