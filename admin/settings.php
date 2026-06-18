<?php
session_start();
if (!isset($_SESSION['usertype']) || $_SESSION['usertype'] != 'a') {
    header('location: ../login.php');
    exit();
}
include('../connection.php');

$aemail  = $_SESSION['user'];
$message = '';

if (isset($_POST['update_password'])) {
    $newpass = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    if ($newpass !== $confirm) {
        $message = '<div class="alert-box alert-error">Passwords do not match.</div>';
    } elseif (strlen($newpass) < 4) {
        $message = '<div class="alert-box alert-error">Password must be at least 4 characters.</div>';
    } else {
        $database->query("UPDATE admin SET apassword='$newpass' WHERE aemail='$aemail'");
        $message = '<div class="alert-box alert-success">Password updated successfully!</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/animations.css">
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/admin.css">
    <title>Settings - eDoc</title>
    <style>
        .alert-box { padding:10px 16px;border-radius:8px;margin:16px 20px;font-size:14px; }
        .alert-success { background:#d1e7dd;color:#0f5132; }
        .alert-error { background:#f8d7da;color:#842029; }
        .settings-card { max-width:480px;padding:30px;margin:20px; }
        .form-group { margin-bottom:16px; }
        .form-group label { display:block;font-size:13px;font-weight:600;margin-bottom:6px;color:#3b3b3b; }
    </style>
</head>
<body>
<div class="container">
    <div class="menu">
        <table class="menu-container" border="0">
            <tr>
                <td style="padding:10px" colspan="2">
                    <table border="0" class="profile-container">
                        <tr>
                            <td width="30%" style="padding-left:20px">
                                <img src="../img/user.png" alt="" width="100%" style="border-radius:50%">
                            </td>
                            <td style="padding:0;margin:0;">
                                <p class="profile-title">Super Admin</p>
                                <p class="profile-subtitle"><?php echo $aemail; ?></p>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <a href="../logout.php"><input type="button" value="Log out" class="logout-btn btn-primary-soft btn"></a>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="menu-row">
                <td class="menu-btn menu-icon-dashbord"><a href="index.php" class="non-style-link-menu"><div><p class="menu-text">Dashboard</p></div></a></td>
            </tr>
            <tr class="menu-row">
                <td class="menu-btn menu-icon-doctor"><a href="doctors.php" class="non-style-link-menu"><div><p class="menu-text">Doctors</p></div></a></td>
            </tr>
            <tr class="menu-row">
                <td class="menu-btn menu-icon-schedule"><a href="schedule.php" class="non-style-link-menu"><div><p class="menu-text">Schedule</p></div></a></td>
            </tr>
            <tr class="menu-row">
                <td class="menu-btn menu-icon-appoinment"><a href="appointment.php" class="non-style-link-menu"><div><p class="menu-text">Appointment</p></div></a></td>
            </tr>
            <tr class="menu-row">
                <td class="menu-btn menu-icon-patient"><a href="patient.php" class="non-style-link-menu"><div><p class="menu-text">Patients</p></div></a></td>
            </tr>
            <tr class="menu-row">
                <td class="menu-btn menu-icon-patient"><a href="specialties.php" class="non-style-link-menu"><div><p class="menu-text">Specialties</p></div></a></td>
            </tr>
            <tr class="menu-row">
                <td class="menu-btn menu-icon-patient"><a href="reports.php" class="non-style-link-menu"><div><p class="menu-text">Reports</p></div></a></td>
            </tr>
            <tr class="menu-row">
                <td class="menu-btn menu-icon-settings menu-active menu-icon-settings-active">
                    <a href="settings.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">Settings</p></div></a>
                </td>
            </tr>
        </table>
    </div>

    <div class="dash-body" style="margin-top:15px;">
        <p style="padding:10px 20px 0;font-size:23px;font-weight:700;color:var(--primarycolor);">Account Settings</p>
        <p style="padding:0 20px;font-size:14px;color:#8492a6;margin-bottom:0;">Manage your Super Admin credentials</p>

        <?php echo $message; ?>

        <div class="filter-container settings-card">
            <p style="font-size:17px;font-weight:600;margin-bottom:16px;">Change Password</p>
            <form method="POST">
                <div class="form-group">
                    <label>Admin Email (read only)</label>
                    <input type="email" class="input-text" value="<?php echo $aemail; ?>" readonly style="background:#f8f9fa;">
                </div>
                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" name="new_password" class="input-text" placeholder="Enter new password" required>
                </div>
                <div class="form-group">
                    <label>Confirm New Password</label>
                    <input type="password" name="confirm_password" class="input-text" placeholder="Confirm new password" required>
                </div>
                <input type="submit" name="update_password" value="Update Password" class="btn btn-primary" style="width:100%;">
            </form>
        </div>
    </div>
</div>
</body>
</html>
