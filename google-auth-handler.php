<?php
/*
|
| google-auth-handler.php
| 
|
| This file handles Google OAuth token verification and user
| creation/login when Google Sign-In is enabled.
|
| To enable:
| 1. Install: composer require google/apiclient
| 2. Set your CLIENT_ID below
| 3. Uncomment all code in this file
| 4. Uncomment Google auth blocks in login.php and signup.php
|

header('Content-Type: application/json');
session_start();
include('connection.php');

// require_once 'vendor/autoload.php';

define('GOOGLE_CLIENT_ID', 'YOUR_GOOGLE_CLIENT_ID.apps.googleusercontent.com');

// Get JSON body from request
// $input  = json_decode(file_get_contents('php://input'), true);
// $token  = $input['credential'] ?? '';
// $action = $input['action'] ?? 'login'; // 'login' or 'signup'

// if (empty($token)) {
//     echo json_encode(['success' => false, 'message' => 'No token provided']);
//     exit();
// }

// Verify the Google JWT token
// $client = new Google_Client(['client_id' => GOOGLE_CLIENT_ID]);
// $payload = $client->verifyIdToken($token);

// if (!$payload) {
//     echo json_encode(['success' => false, 'message' => 'Invalid Google token']);
//     exit();
// }

// Extract user info from verified token
// $google_email = $payload['email'];
// $google_name  = $payload['name'];
// $google_pic   = $payload['picture'];

// Check if user exists
// $check = $database->query("SELECT * FROM webuser WHERE email='$google_email'");

// if ($check->num_rows > 0) {
//     // User exists — log them in
//     $utype = $check->fetch_assoc()['usertype'];
//     $_SESSION['user']     = $google_email;
//     $_SESSION['usertype'] = $utype;
//
//     $redirect = '';
//     if ($utype == 'p') $redirect = 'patient/index.php';
//     elseif ($utype == 'a') $redirect = 'admin/index.php';
//     elseif ($utype == 'd') $redirect = 'doctor/index.php';
//
//     echo json_encode(['success' => true, 'redirect' => $redirect]);

// } else {
//     // New user — auto-register as patient
//     $database->query("INSERT INTO webuser (email, usertype) VALUES ('$google_email', 'p')");
//     $database->query("INSERT INTO patient (pname, pemail, ppassword)
//                       VALUES ('$google_name', '$google_email', 'google-auth')");
//
//     $_SESSION['user']     = $google_email;
//     $_SESSION['usertype'] = 'p';
//
//     echo json_encode(['success' => true, 'redirect' => 'patient/index.php']);
// }

*/

// Placeholder response while Google Auth is disabled
echo json_encode([
    'success' => false,
    'message' => 'Google Authentication is currently disabled. Please use email and password to login.'
]);
?>
