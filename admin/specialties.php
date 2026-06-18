<?php
session_start();
if (!isset($_SESSION['usertype']) || $_SESSION['usertype'] != 'a') {
    header('location: ../login.php');
    exit();
}
include('../connection.php');

$message = '';

// Add specialty
if (isset($_POST['add_specialty'])) {
    $sname = $_POST['sname'];
    $check = $database->query("SELECT * FROM specialties WHERE sname='$sname'");
    if ($check->num_rows > 0) {
        $message = '<div class="alert-box alert-error">This specialty already exists.</div>';
    } else {
        $database->query("INSERT INTO specialties (sname) VALUES ('$sname')");
        $message = '<div class="alert-box alert-success">Specialty added successfully!</div>';
    }
}

// Edit specialty
if (isset($_POST['edit_specialty'])) {
    $id    = $_POST['id'];
    $sname = $_POST['sname_edit'];
    $database->query("UPDATE specialties SET sname='$sname' WHERE id='$id'");
    $message = '<div class="alert-box alert-success">Specialty updated successfully!</div>';
}

// Delete specialty (only if no doctor uses it)
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $inUse = $database->query("SELECT * FROM doctor WHERE specialties='$id'");
    if ($inUse->num_rows > 0) {
        $message = '<div class="alert-box alert-error">Cannot delete — '.$inUse->num_rows.' doctor(s) are assigned to this specialty.</div>';
    } else {
        $database->query("DELETE FROM specialties WHERE id='$id'");
        $message = '<div class="alert-box alert-success">Specialty deleted successfully!</div>';
    }
}

$specialties = $database->query("SELECT * FROM specialties ORDER BY sname ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/animations.css">
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/admin.css">
    <title>Specialties - eDoc</title>
    <style>
        .alert-box { padding:10px 16px;border-radius:8px;margin:16px 20px;font-size:14px; }
        .alert-success { background:#d1e7dd;color:#0f5132; }
        .alert-error { background:#f8d7da;color:#842029; }
        .add-form-row { display:flex;gap:12px;align-items:flex-end; }
        .btn-sm { padding:5px 14px;font-size:12px; }
        .btn-danger-sm { background:#dc3545;color:#fff;border:none;border-radius:4px;padding:5px 14px;font-size:12px;cursor:pointer; }
        .edit-row input { font-size:13px; }
        .badge-count { background:var(--btnice);color:var(--btnnicetext);padding:3px 10px;border-radius:20px;font-size:12px;font-weight:600; }
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
                                <p class="profile-subtitle"><?php echo $_SESSION['user']; ?></p>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2"><a href="../logout.php"><input type="button" value="Log out" class="logout-btn btn-primary-soft btn"></a></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="menu-row"><td class="menu-btn menu-icon-dashbord"><a href="index.php" class="non-style-link-menu"><div><p class="menu-text">Dashboard</p></div></a></td></tr>
            <tr class="menu-row"><td class="menu-btn menu-icon-doctor"><a href="doctors.php" class="non-style-link-menu"><div><p class="menu-text">Doctors</p></div></a></td></tr>
            <tr class="menu-row"><td class="menu-btn menu-icon-schedule"><a href="schedule.php" class="non-style-link-menu"><div><p class="menu-text">Schedule</p></div></a></td></tr>
            <tr class="menu-row"><td class="menu-btn menu-icon-appoinment"><a href="appointment.php" class="non-style-link-menu"><div><p class="menu-text">Appointment</p></div></a></td></tr>
            <tr class="menu-row"><td class="menu-btn menu-icon-patient"><a href="patient.php" class="non-style-link-menu"><div><p class="menu-text">Patients</p></div></a></td></tr>
            <tr class="menu-row">
                <td class="menu-btn menu-icon-patient menu-active menu-icon-patient-active">
                    <a href="specialties.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">Specialties</p></div></a>
                </td>
            </tr>
            <tr class="menu-row"><td class="menu-btn menu-icon-patient"><a href="reports.php" class="non-style-link-menu"><div><p class="menu-text">Reports</p></div></a></td></tr>
            <tr class="menu-row"><td class="menu-btn menu-icon-settings"><a href="settings.php" class="non-style-link-menu"><div><p class="menu-text">Settings</p></div></a></td></tr>
        </table>
    </div>

    <div class="dash-body" style="margin-top:15px;">
        <p style="padding:10px 20px 0;font-size:23px;font-weight:700;color:var(--primarycolor);">Specialty Management</p>
        <p style="padding:0 20px;font-size:14px;color:#8492a6;margin-bottom:0;">Add, edit, or remove medical specialties</p>

        <?php echo $message; ?>

        <!-- Add Specialty -->
        <div class="add-doc-form-container">
            <div class="filter-container" style="padding:16px 20px;">
                <p style="font-size:17px;font-weight:600;margin:0 0 12px;">Add New Specialty</p>
                <form method="POST" class="add-form-row">
                    <input type="text" name="sname" class="input-text" placeholder="e.g. Cardiology" required style="flex:1;">
                    <input type="submit" name="add_specialty" value="+ Add" class="btn btn-primary btn-sm">
                </form>
            </div>
        </div>

        <!-- List -->
        <center>
        <div class="abc" style="height:400px;">
        <table width="92%" class="sub-table" border="0">
            <thead>
                <tr>
                    <th class="table-headin">ID</th>
                    <th class="table-headin">Specialty Name</th>
                    <th class="table-headin">Doctors Assigned</th>
                    <th class="table-headin">Action</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = $specialties->fetch_assoc()):
                $count = $database->query("SELECT COUNT(*) as cnt FROM doctor WHERE specialties='{$row['id']}'")->fetch_assoc()['cnt'];
            ?>
                <tr>
                    <td style="padding:14px 10px;text-align:center;color:#8492a6;"><?php echo $row['id']; ?></td>
                    <td style="padding:10px;font-weight:600;">
                        <form method="POST" style="display:flex;gap:8px;align-items:center;">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <input type="text" name="sname_edit" value="<?php echo $row['sname']; ?>" class="input-text" style="width:200px;padding:4px 8px;">
                            <button type="submit" name="edit_specialty" class="btn btn-primary-gray btn-sm">Save</button>
                        </form>
                    </td>
                    <td style="padding:10px;text-align:center;"><span class="badge-count"><?php echo $count; ?></span></td>
                    <td style="padding:10px;">
                        <a href="specialties.php?delete=<?php echo $row['id']; ?>" class="btn-danger-sm"
                           onclick="return confirm('Delete this specialty?')">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
            <?php if ($specialties->num_rows === 0): ?>
                <tr><td colspan="4" style="text-align:center;padding:40px;color:#8492a6;">No specialties found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
        </div>
        </center>
    </div>
</div>
</body>
</html>
