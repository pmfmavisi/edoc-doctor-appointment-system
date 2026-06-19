<?php
/*

// [GOOGLE AUTH] - Load Google API client
// require_once 'vendor/autoload.php';

// $client = new Google_Client();
// $client->setClientId('YOUR_GOOGLE_CLIENT_ID.apps.googleusercontent.com');
// $client->setClientSecret('YOUR_GOOGLE_CLIENT_SECRET');
// $client->setRedirectUri('http://localhost/edoc-doctor-appointment-system/signup.php');
// $client->addScope('email');
// $client->addScope('profile');

// [GOOGLE AUTH] - Handle Google callback on signup page
// if (isset($_GET['code'])) {
//     include("connection.php");
//
//     $token       = $client->fetchAccessTokenWithAuthCode($_GET['code']);
//     $client->setAccessToken($token);
//
//     $google_service = new Google_Service_Oauth2($client);
//     $google_user    = $google_service->userinfo->get();
//
//     $google_email = $google_user->email;
//     $google_name  = $google_user->name;
//     $google_pic   = $google_user->picture;
//
//     // Check if already registered
//     $check = $database->query("SELECT * FROM webuser WHERE email='$google_email'");
//     if ($check->num_rows > 0) {
//         // Already exists — redirect to login
//         header('location: login.php?msg=already_exists');
//         exit();
//     }
//
//     // Auto-register as patient
//     session_start();
//     $database->query("INSERT INTO webuser (email, usertype) VALUES ('$google_email', 'p')");
//     $database->query("INSERT INTO patient (pname, pemail, ppassword) VALUES ('$google_name', '$google_email', 'google-auth')");
//
//     $_SESSION['user']     = $google_email;
//     $_SESSION['usertype'] = 'p';
//     header('location: patient/index.php');
//     exit();
// }

*/
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/animations.css">
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/signup.css">

    <!--
    [GOOGLE AUTH] - Load Google Sign-In JavaScript SDK
    Uncomment below when enabling Google Sign-Up:

    <script src="https://accounts.google.com/gsi/client" async defer></script>
    -->

    <title>Sign Up - eDoc</title>
    <style>
        .google-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            padding: 10px;
            border: 1.5px solid #e9ecef;
            border-radius: 5px;
            background: #fff;
            font-family: 'Inter', sans-serif;
            font-size: 15px;
            font-weight: 500;
            color: #3b3b3b;
            cursor: pointer;
            transition: 0.2s;
            text-decoration: none;
            margin-bottom: 10px;
        }
        .google-btn:hover {
            background: #f8f9fa;
            border-color: #0A76D8;
        }
        .google-btn img {
            width: 20px;
            height: 20px;
        }
        .or-divider {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 14px 0;
            color: #8492a6;
            font-size: 13px;
        }
        .or-divider::before,
        .or-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e9ecef;
        }
    </style>
</head>
<body>
<?php

session_start();
include("connection.php");

$error = '';

if ($_POST) {
    $name     = $_POST['name'];
    $email    = $_POST['email'];
    $password = $_POST['password'];
    $address  = $_POST['address'];
    $nic      = $_POST['nic'];
    $dob      = $_POST['dob'];
    $tel      = $_POST['tel'];

    // Check if email already exists
    $check = $database->query("SELECT * FROM webuser WHERE email='$email'");
    if ($check->num_rows > 0) {
        $error = '<label class="form-label" style="color:rgb(255,62,62);">This email is already registered. <a href="login.php">Login instead</a></label>';
    } else {
        $database->query("INSERT INTO webuser (email, usertype) VALUES ('$email', 'p')");
        $database->query("INSERT INTO patient (pname, pemail, ppassword, paddress, pnic, pdob, ptel)
                          VALUES ('$name','$email','$password','$address','$nic','$dob','$tel')");
        header('location: login.php');
        exit();
    }
} else {
    $error = '<label class="form-label">&nbsp;</label>';
}
?>

<center>
<div class="container">
    <table border="0" style="margin:0;padding:0;width:60%;">
        <tr><td><p class="header-text">Create Account</p></td></tr>
    <div class="form-body">
        <tr><td><p class="sub-text">Register as a patient to book appointments</p></td></tr>
        <tr>
            <form action="" method="POST">
            <td class="label-td">

                <!--
                ====================================================
                [GOOGLE AUTH] - Google Sign-Up Button
                Uncomment below to enable Sign Up with Google:
                ====================================================

                <a href="<?php // echo $client->createAuthUrl(); ?>" class="google-btn">
                    <img src="https://developers.google.com/identity/images/g-logo.png" alt="Google">
                    Sign up with Google
                </a>

                <div class="or-divider">or sign up with email</div>

                Alternatively using Google Identity Services (newer approach):

                <div id="g_id_onload"
                    data-client_id="YOUR_GOOGLE_CLIENT_ID.apps.googleusercontent.com"
                    data-callback="handleGoogleSignup"
                    data-auto_prompt="false">
                </div>
                <div class="g_id_signin"
                    data-type="standard"
                    data-size="large"
                    data-theme="outline"
                    data-text="signup_with"
                    data-shape="rectangular"
                    data-logo_alignment="left">
                </div>

                <div class="or-divider">or sign up with email</div>

                ====================================================
                End Google Auth Button
                ====================================================
                -->

                <label for="name" class="form-label">Full Name: </label>
            </td>
        </tr>
        <tr>
            <td class="label-td">
                <input type="text" name="name" class="input-text" placeholder="Full Name" required>
            </td>
        </tr>
        <tr>
            <td class="label-td">
                <label for="email" class="form-label">Email: </label>
            </td>
        </tr>
        <tr>
            <td class="label-td">
                <input type="email" name="email" class="input-text" placeholder="Email Address" required>
            </td>
        </tr>
        <tr>
            <td class="label-td">
                <label for="password" class="form-label">Password: </label>
            </td>
        </tr>
        <tr>
            <td class="label-td">
                <input type="password" name="password" class="input-text" placeholder="Password" required>
            </td>
        </tr>
        <tr>
            <td class="label-td">
                <label for="tel" class="form-label">Phone Number: </label>
            </td>
        </tr>
        <tr>
            <td class="label-td">
                <input type="tel" name="tel" class="input-text" placeholder="Phone Number" required>
            </td>
        </tr>
        <tr>
            <td class="label-td">
                <label for="address" class="form-label">Address: </label>
            </td>
        </tr>
        <tr>
            <td class="label-td">
                <input type="text" name="address" class="input-text" placeholder="Address" required>
            </td>
        </tr>
        <tr>
            <td class="label-td">
                <label for="nic" class="form-label">NIC / ID Number: </label>
            </td>
        </tr>
        <tr>
            <td class="label-td">
                <input type="text" name="nic" class="input-text" placeholder="NIC Number" required>
            </td>
        </tr>
        <tr>
            <td class="label-td">
                <label for="dob" class="form-label">Date of Birth: </label>
            </td>
        </tr>
        <tr>
            <td class="label-td">
                <input type="date" name="dob" class="input-text" required>
            </td>
        </tr>
        <tr>
            <td><br><?php echo $error ?></td>
        </tr>
        <tr>
            <td>
                <input type="submit" value="Create Account" class="login-btn btn-primary btn">
            </td>
        </tr>
    </div>
        <tr>
            <td>
                <br>
                <label class="sub-text" style="font-weight:280;">Already have an account&#63; </label>
                <a href="login.php" class="hover-link1 non-style-link">Login</a>
                <br><br><br>
            </td>
        </tr>
            </form>
    </table>
</div>
</center>

<!--
[GOOGLE AUTH] - Handle Google JWT token callback (newer approach)
Uncomment when enabling Google Identity Services:

<script>
function handleGoogleSignup(response) {
    fetch('google-auth-handler.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ credential: response.credential, action: 'signup' })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            window.location.href = data.redirect;
        } else {
            alert('Google signup failed: ' + data.message);
        }
    });
}
</script>
-->

</body>
</html>
