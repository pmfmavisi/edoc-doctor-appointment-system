<?php
session_start();
if (!isset($_SESSION['usertype']) || $_SESSION['usertype'] != 'a') {
    header('location: ../login.php');
    exit();
}
include('../connection.php');

date_default_timezone_set('Asia/Kolkata');
$today = date('Y-m-d');

// ── STATS ──────────────────────────────────────────────────────
$total_doctors      = $database->query("SELECT COUNT(*) as cnt FROM doctor")->fetch_assoc()['cnt'];
$total_patients     = $database->query("SELECT COUNT(*) as cnt FROM patient")->fetch_assoc()['cnt'];
$total_appointments = $database->query("SELECT COUNT(*) as cnt FROM appointment")->fetch_assoc()['cnt'];
$total_schedules    = $database->query("SELECT COUNT(*) as cnt FROM schedule")->fetch_assoc()['cnt'];

// ── CHART 1: Appointments per Doctor ──────────────────────────
$docChart = $database->query("
    SELECT doctor.docname, COUNT(appointment.appoid) as total
    FROM doctor
    LEFT JOIN schedule ON schedule.docid = doctor.docid
    LEFT JOIN appointment ON appointment.scheduleid = schedule.scheduleid
    GROUP BY doctor.docid, doctor.docname
    ORDER BY total DESC
    LIMIT 8
");
$docLabels = []; $docData = [];
while ($row = $docChart->fetch_assoc()) {
    $docLabels[] = "Dr. " . $row['docname'];
    $docData[]   = (int)$row['total'];
}

// ── CHART 2: Status Breakdown ──────────────────────────────────
$statusChart = $database->query("
    SELECT COALESCE(status,'pending') as status, COUNT(*) as total
    FROM appointment GROUP BY status
");
$statusLabels = []; $statusData = [];
while ($row = $statusChart->fetch_assoc()) {
    $statusLabels[] = ucfirst($row['status']);
    $statusData[]   = (int)$row['total'];
}
if (count($statusLabels) === 0) { $statusLabels = ['No Data']; $statusData = [1]; }

// ── CHART 3: Specialties Distribution ──────────────────────────
$specChart = $database->query("
    SELECT specialties.sname, COUNT(doctor.docid) as total
    FROM specialties
    LEFT JOIN doctor ON doctor.specialties = specialties.id
    GROUP BY specialties.id, specialties.sname
    HAVING total > 0
    ORDER BY total DESC
    LIMIT 8
");
$specLabels = []; $specData = [];
while ($row = $specChart->fetch_assoc()) {
    $specLabels[] = $row['sname'];
    $specData[]   = (int)$row['total'];
}
if (count($specLabels) === 0) { $specLabels = ['No Data']; $specData = [1]; }

// ── TABLE: Recent Appointments ─────────────────────────────────
$recent = $database->query("
    SELECT appointment.apponum, appointment.appodate, appointment.status,
           patient.pname, doctor.docname, schedule.title
    FROM appointment
    JOIN patient ON patient.pid = appointment.pid
    JOIN schedule ON schedule.scheduleid = appointment.scheduleid
    JOIN doctor ON doctor.docid = schedule.docid
    ORDER BY appointment.appodate DESC
    LIMIT 15
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/animations.css">
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/admin.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <title>Reports - eDoc</title>
    <style>
        .report-grid { display:grid;grid-template-columns:1fr 1fr;gap:20px;padding:0 20px;margin-top:10px; }
        .chart-card { background:#fff;border:1px solid #ebebeb;border-radius:8px;padding:20px;height:320px; }
        .chart-card h6 { font-size:15px;font-weight:600;margin:0 0 12px;color:#333; }
        .full-width { grid-column:1 / -1; }
        @media print {
            .menu, .no-print { display:none !important; }
            .dash-body { width:100% !important; margin-left:0 !important; }
        }
        .print-btn { background:var(--primarycolor);color:#fff;border:none;border-radius:5px;padding:8px 18px;font-size:13px;cursor:pointer;font-family:'Inter',sans-serif; }
    </style>
</head>
<body>
<div class="container">
    <div class="menu no-print">
        <table class="menu-container" border="0">
            <tr>
                <td style="padding:10px" colspan="2">
                    <table border="0" class="profile-container">
                        <tr>
                            <td width="30%" style="padding-left:20px"><img src="../img/user.png" alt="" width="100%" style="border-radius:50%"></td>
                            <td style="padding:0;margin:0;">
                                <p class="profile-title">Super Admin</p>
                                <p class="profile-subtitle"><?php echo $_SESSION['user']; ?></p>
                            </td>
                        </tr>
                        <tr><td colspan="2"><a href="../logout.php"><input type="button" value="Log out" class="logout-btn btn-primary-soft btn"></a></td></tr>
                    </table>
                </td>
            </tr>
            <tr class="menu-row"><td class="menu-btn menu-icon-dashbord"><a href="index.php" class="non-style-link-menu"><div><p class="menu-text">Dashboard</p></div></a></td></tr>
            <tr class="menu-row"><td class="menu-btn menu-icon-doctor"><a href="doctors.php" class="non-style-link-menu"><div><p class="menu-text">Doctors</p></div></a></td></tr>
            <tr class="menu-row"><td class="menu-btn menu-icon-schedule"><a href="schedule.php" class="non-style-link-menu"><div><p class="menu-text">Schedule</p></div></a></td></tr>
            <tr class="menu-row"><td class="menu-btn menu-icon-appoinment"><a href="appointment.php" class="non-style-link-menu"><div><p class="menu-text">Appointment</p></div></a></td></tr>
            <tr class="menu-row"><td class="menu-btn menu-icon-patient"><a href="patient.php" class="non-style-link-menu"><div><p class="menu-text">Patients</p></div></a></td></tr>
            <tr class="menu-row"><td class="menu-btn menu-icon-patient"><a href="specialties.php" class="non-style-link-menu"><div><p class="menu-text">Specialties</p></div></a></td></tr>
            <tr class="menu-row">
                <td class="menu-btn menu-icon-patient menu-active menu-icon-patient-active">
                    <a href="reports.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">Reports</p></div></a>
                </td>
            </tr>
            <tr class="menu-row"><td class="menu-btn menu-icon-settings"><a href="settings.php" class="non-style-link-menu"><div><p class="menu-text">Settings</p></div></a></td></tr>
        </table>
    </div>

    <div class="dash-body" style="margin-top:15px;">
        <table width="100%" border="0">
            <tr>
                <td style="padding:10px 20px 0;">
                    <p style="font-size:23px;font-weight:700;color:var(--primarycolor);margin:0;">Reports &amp; Analytics</p>
                    <p style="font-size:14px;color:#8492a6;margin:4px 0 0;">System overview as of <?php echo $today; ?></p>
                </td>
                <td style="text-align:right;padding-right:20px;" class="no-print">
                    <button onclick="window.print()" class="print-btn">🖨 Print Report</button>
                </td>
            </tr>
        </table>

        <!-- Stats Cards -->
        <center>
        <table class="filter-container" style="border:none;margin-top:10px;" border="0">
            <tr><td colspan="4"><p style="font-size:18px;font-weight:600;padding-left:12px;">Overview</p></td></tr>
            <tr>
                <td style="width:25%;">
                    <div class="dashboard-items" style="padding:20px;margin:auto;width:95%;display:flex;">
                        <div><div class="h1-dashboard"><?php echo $total_doctors; ?></div><br><div class="h3-dashboard">Doctors</div></div>
                        <div class="btn-icon-back dashboard-icons" style="background-image:url('../img/icons/doctors-hover.svg');"></div>
                    </div>
                </td>
                <td style="width:25%;">
                    <div class="dashboard-items" style="padding:20px;margin:auto;width:95%;display:flex;">
                        <div><div class="h1-dashboard"><?php echo $total_patients; ?></div><br><div class="h3-dashboard">Patients</div></div>
                        <div class="btn-icon-back dashboard-icons" style="background-image:url('../img/icons/patients-hover.svg');"></div>
                    </div>
                </td>
                <td style="width:25%;">
                    <div class="dashboard-items" style="padding:20px;margin:auto;width:95%;display:flex;">
                        <div><div class="h1-dashboard"><?php echo $total_appointments; ?></div><br><div class="h3-dashboard">Appointments</div></div>
                        <div class="btn-icon-back dashboard-icons" style="background-image:url('../img/icons/book-hover.svg');"></div>
                    </div>
                </td>
                <td style="width:25%;">
                    <div class="dashboard-items" style="padding:20px;margin:auto;width:95%;display:flex;">
                        <div><div class="h1-dashboard"><?php echo $total_schedules; ?></div><br><div class="h3-dashboard">Schedules</div></div>
                        <div class="btn-icon-back dashboard-icons" style="background-image:url('../img/icons/session-iceblue.svg');"></div>
                    </div>
                </td>
            </tr>
        </table>
        </center>

        <!-- Charts -->
        <div class="report-grid">
            <div class="chart-card">
                <h6>Appointments per Doctor</h6>
                <canvas id="docChart"></canvas>
            </div>
            <div class="chart-card">
                <h6>Appointment Status Breakdown</h6>
                <canvas id="statusChart"></canvas>
            </div>
            <div class="chart-card full-width" style="height:300px;">
                <h6>Doctors per Specialty</h6>
                <canvas id="specChart"></canvas>
            </div>
        </div>

        <!-- Recent Appointments Table -->
        <p style="padding:20px 20px 0;font-size:18px;font-weight:600;">Recent Appointments</p>
        <center>
        <div class="abc" style="height:300px;">
        <table width="92%" class="sub-table" border="0">
            <thead>
                <tr>
                    <th class="table-headin">#</th>
                    <th class="table-headin">Patient</th>
                    <th class="table-headin">Doctor</th>
                    <th class="table-headin">Session</th>
                    <th class="table-headin">Date</th>
                    <th class="table-headin">Status</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = $recent->fetch_assoc()): ?>
                <tr>
                    <td style="padding:12px;text-align:center;"><?php echo $row['apponum']; ?></td>
                    <td style="padding:10px;font-weight:600;"><?php echo $row['pname']; ?></td>
                    <td style="padding:10px;">Dr. <?php echo $row['docname']; ?></td>
                    <td style="padding:10px;color:#8492a6;"><?php echo $row['title']; ?></td>
                    <td style="padding:10px;"><?php echo $row['appodate']; ?></td>
                    <td style="padding:10px;"><?php echo ucfirst($row['status'] ?? 'pending'); ?></td>
                </tr>
            <?php endwhile; ?>
            <?php if ($recent->num_rows === 0): ?>
                <tr><td colspan="6" style="text-align:center;padding:30px;color:#8492a6;">No appointments found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
        </div>
        </center>
        <br>
    </div>
</div>

<script>
const docCtx = document.getElementById('docChart');
new Chart(docCtx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($docLabels); ?>,
        datasets: [{
            label: 'Appointments',
            data: <?php echo json_encode($docData); ?>,
            backgroundColor: '#0A76D8',
            borderRadius: 6
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
    }
});

const statusCtx = document.getElementById('statusChart');
new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: <?php echo json_encode($statusLabels); ?>,
        datasets: [{
            data: <?php echo json_encode($statusData); ?>,
            backgroundColor: ['#ffc107', '#198754', '#dc3545', '#6c757d']
        }]
    },
    options: { responsive: true, maintainAspectRatio: false }
});

const specCtx = document.getElementById('specChart');
new Chart(specCtx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($specLabels); ?>,
        datasets: [{
            label: 'Doctors',
            data: <?php echo json_encode($specData); ?>,
            backgroundColor: '#1b62b3',
            borderRadius: 6
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { x: { beginAtZero: true, ticks: { stepSize: 1 } } }
    }
});
</script>
</body>
</html>
