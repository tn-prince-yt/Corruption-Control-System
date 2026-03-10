<?php
include '../includes/config.php';
include '../includes/functions.php';

requireLogin();
requireOfficer();

// Get officer info
$user_id = $_SESSION['user_id'];
$query = "SELECT o.officer_id FROM officers WHERE user_id = $user_id";
$result = $conn->query($query);
$officer = $result->fetch_assoc();
$officer_id = $officer['officer_id'];

// Get statistics
$active_cases = $conn->query("SELECT COUNT(*) as count FROM complaints WHERE assigned_officer_id = $officer_id AND status = 'under_investigation'")->fetch_assoc()['count'];
$approved_cases = $conn->query("SELECT COUNT(*) as count FROM complaints WHERE assigned_officer_id = $officer_id AND status = 'approved'")->fetch_assoc()['count'];
$completed_cases = $conn->query("SELECT COUNT(*) as count FROM complaints WHERE assigned_officer_id = $officer_id AND status = 'completed'")->fetch_assoc()['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Investigation Officer</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <?php include '../includes/navbar.php'; ?>

        <div class="dashboard">
            <div class="sidebar">
                <div class="user-profile">
                    <h3><?php echo $_SESSION['first_name'] . ' ' . $_SESSION['last_name']; ?></h3>
                    <p><?php echo $_SESSION['email']; ?></p>
                    <p class="user-type">Investigation Officer</p>
                </div>
                <nav class="sidebar-menu">
                    <a href="dashboard.php" class="active">Dashboard</a>
                    <a href="view_complaints.php">Assigned Complaints</a>
                    <a href="../auth/logout.php">Logout</a>
                </nav>
            </div>

            <div class="main-content">
                <h2>Welcome, <?php echo $_SESSION['first_name']; ?>!</h2>
                
                <div class="stats-grid">
                    <div class="stat-card">
                        <h3><?php echo $approved_cases; ?></h3>
                        <p>Approved Cases</p>
                    </div>
                    <div class="stat-card">
                        <h3><?php echo $active_cases; ?></h3>
                        <p>Active Investigation</p>
                    </div>
                    <div class="stat-card">
                        <h3><?php echo $completed_cases; ?></h3>
                        <p>Completed Cases</p>
                    </div>
                </div>

                <div class="actions">
                    <a href="view_complaints.php" class="btn btn-primary">View Assigned Complaints</a>
                </div>

                <div class="info-box">
                    <h3>Investigation Process</h3>
                    <ol>
                        <li>View assigned complaints in the complaints list</li>
                        <li>Click on a complaint to see details and evidence</li>
                        <li>Conduct your investigation</li>
                        <li>Submit investigation report with findings</li>
                        <li>Mark case as completed</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
