<?php
/*
|--------------------------------------------------------------------------
| GOOGLE OAUTH - COMMENTED OUT FOR NOW (PRESENTATION PURPOSES)
|--------------------------------------------------------------------------
| To enable Google Login:
| 1. Go to https://console.cloud.google.com
| 2. Create a project → Enable "Google+ API"
| 3. Create OAuth 2.0 credentials → get Client ID & Secret
| 4. Install library: composer require google/apiclient
| 5. Uncomment the code blocks marked [GOOGLE AUTH] below
|--------------------------------------------------------------------------

// [GOOGLE AUTH] - Load Google API client
// require_once 'vendor/autoload.php';

// $client = new Google_Client();
// $client->setClientId('YOUR_GOOGLE_CLIENT_ID.apps.googleusercontent.com');
// $client->setClientSecret('YOUR_GOOGLE_CLIENT_SECRET');
// $client->setRedirectUri('http://localhost/edoc-doctor-appointment-system/login.php');
// $client->addScope('email');
// $client->addScope('profile');

// [GOOGLE AUTH] - Handle Google callback
// if (isset($_GET['code'])) {
//     $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
//     $client->setAccessToken($token);
//
//     $google_service = new Google_Service_Oauth2($client);
//     $google_user    = $google_service->userinfo->get();
//
//     $google_email = $google_user->email;
//     $google_name  = $google_user->name;
//
//     // Check if user already exists in webuser table
//     $check = $database->query("SELECT * FROM webuser WHERE email='$google_email'");
//     if ($check->num_rows > 0) {
//         // Existing user — log them in
//         $utype = $check->fetch_assoc()['usertype'];
//         $_SESSION['user']     = $google_email;
//         $_SESSION['usertype'] = $utype;
//         if ($utype == 'p') header('location: patient/index.php');
//         elseif ($utype == 'a') header('location: admin/index.php');
//         elseif ($utype == 'd') header('location: doctor/index.php');
//     } else {
//         // New user — auto-register as patient
//         $database->query("INSERT INTO webuser (email, usertype) VALUES ('$google_email', 'p')");
//         $database->query("INSERT INTO patient (pname, pemail, ppassword) VALUES ('$google_name', '$google_email', 'google-auth')");
//         $_SESSION['user']     = $google_email;
//         $_SESSION['usertype'] = 'p';
//         header('location: patient/index.php');
//     }
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
    <link rel="stylesheet" href="css/login.css">

    <!--
    [GOOGLE AUTH] - Load Google Sign-In JavaScript SDK


    <script src="https://accounts.google.com/gsi/client" async defer></script>
    -->

    <title>Login - eDoc</title>
    <style>
        /* Google Sign-In button style - ready for when it's enabled */
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
            margin-top: 10px;
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

    $_SESSION["user"]     = "";
    $_SESSION["usertype"] = "";

    date_default_timezone_set('Asia/Kolkata');
    $date = date('Y-m-d');
    $_SESSION["date"] = $date;

    include("connection.php");

    if ($_POST) {

        $email    = $_POST['useremail'];
        $password = $_POST['userpassword'];

        $error = '<label class="form-label"></label>';

        $result = $database->query("select * from webuser where email='$email'");

        if ($result->num_rows == 1) {
            $utype = $result->fetch_assoc()['usertype'];

            if ($utype == 'p') {
                $checker = $database->query("select * from patient where pemail='$email' and ppassword='$password'");
                if ($checker->num_rows == 1) {
                    $_SESSION['user']     = $email;
                    $_SESSION['usertype'] = 'p';
                    header('location: patient/index.php');
                } else {
                    $error = '<label class="form-label" style="color:rgb(255,62,62);text-align:center;">Wrong credentials: Invalid email or password</label>';
                }

            } elseif ($utype == 'a') {
                $checker = $database->query("select * from admin where aemail='$email' and apassword='$password'");
                if ($checker->num_rows == 1) {
                    $_SESSION['user']     = $email;
                    $_SESSION['usertype'] = 'a';
                    header('location: admin/index.php');
                } else {
                    $error = '<label class="form-label" style="color:rgb(255,62,62);text-align:center;">Wrong credentials: Invalid email or password</label>';
                }

            } elseif ($utype == 'd') {
                $checker = $database->query("select * from doctor where docemail='$email' and docpassword='$password'");
                if ($checker->num_rows == 1) {
                    $_SESSION['user']     = $email;
                    $_SESSION['usertype'] = 'd';
                    header('location: doctor/index.php');
                } else {
                    $error = '<label class="form-label" style="color:rgb(255,62,62);text-align:center;">Wrong credentials: Invalid email or password</label>';
                }
            }

        } else {
            $error = '<label class="form-label" style="color:rgb(255,62,62);text-align:center;">We can\'t find any account for this email.</label>';
        }

    } else {
        $error = '<label class="form-label">&nbsp;</label>';
    }
    ?>

    <center>
    <div class="container">
        <table border="0" style="margin:0;padding:0;width:60%;">
            <tr><td><p class="header-text">Welcome Back!</p></td></tr>
        <div class="form-body">
            <tr><td><p class="sub-text">Login with your details to continue</p></td></tr>
            <tr>
                <form action="" method="POST">
                <td class="label-td">

                    <!--
                    ====================================================
                    [GOOGLE AUTH] - Google Sign-In Button
                    Uncomment below to enable Google Login:
                    ====================================================

                    <a href="<?php // echo $client->createAuthUrl(); ?>" class="google-btn">
                        <img src="https://developers.google.com/identity/images/g-logo.png" alt="Google">
                        Continue with Google
                    </a>

                    <div class="or-divider">or</div>

                    Alternatively using Google Identity Services (newer approach):

                    <div id="g_id_onload"
                        data-client_id="YOUR_GOOGLE_CLIENT_ID.apps.googleusercontent.com"
                        data-callback="handleGoogleLogin"
                        data-auto_prompt="false">
                    </div>
                    <div class="g_id_signin"
                        data-type="standard"
                        data-size="large"
                        data-theme="outline"
                        data-text="sign_in_with"
                        data-shape="rectangular"
                        data-logo_alignment="left">
                    </div>

                    <div class="or-divider">or</div>

                    ====================================================
                    End Google Auth Button
                    ====================================================
                    -->

                    <label for="useremail" class="form-label">Email: </label>
                </td>
            </tr>
            <tr>
                <td class="label-td">
                    <input type="email" name="useremail" class="input-text" placeholder="Email Address" required>
                </td>
            </tr>
            <tr>
                <td class="label-td">
                    <label for="userpassword" class="form-label">Password: </label>
                </td>
            </tr>
            <tr>
                <td class="label-td">
                    <input type="password" name="userpassword" class="input-text" placeholder="Password" required>
                </td>
            </tr>
            <tr>
                <td><br><?php echo $error ?></td>
            </tr>
            <tr>
                <td>
                    <input type="submit" value="Login" class="login-btn btn-primary btn">
                </td>
            </tr>
        </div>
            <tr>
                <td>
                    <br>
                    <label class="sub-text" style="font-weight:280;">Don't have an account&#63; </label>
                    <a href="signup.php" class="hover-link1 non-style-link">Sign Up</a>
                    <br><br><br>
                </td>
            </tr>
                </form>
        </table>
    </div>
    </center>

    <!--
    [GOOGLE AUTH] - Handle Google JWT token callback (newer approach)
    Uncomment below when enabling Google Identity Services:

    <script>
    function handleGoogleLogin(response) {
        // Send the credential token to your PHP backend
        fetch('google-auth-handler.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ credential: response.credential })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.location.href = data.redirect;
            } else {
                alert('Google login failed: ' + data.message);
            }
        });
    }
    </script>
    -->

</body>
</html>
