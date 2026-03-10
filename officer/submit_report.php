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

$complaint_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Verify complaint is assigned to this officer
$query = "SELECT c.complaint_id FROM complaints c WHERE c.complaint_id = $complaint_id AND c.assigned_officer_id = $officer_id";
$result = $conn->query($query);

if ($result->num_rows === 0) {
    redirect(SITE_URL . 'officer/view_complaints.php');
}

// Get existing report if any
$report_query = "SELECT * FROM reports WHERE complaint_id = $complaint_id";
$report_result = $conn->query($report_query);
$report = $report_result->num_rows > 0 ? $report_result->fetch_assoc() : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $report ? 'Update' : 'Submit'; ?> Report - Investigation Officer</title>
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
                </div>
                <nav class="sidebar-menu">
                    <a href="dashboard.php">Dashboard</a>
                    <a href="view_complaints.php" class="active">Assigned Complaints</a>
                    <a href="../auth/logout.php">Logout</a>
                </nav>
            </div>

            <div class="main-content">
                <h2><?php echo $report ? 'Update' : 'Submit'; ?> Investigation Report</h2>
                <p>Complaint #<?php echo $complaint_id; ?></p>

                <?php
                if (isset($_SESSION['form_error'])) {
                    echo '<div class="alert alert-danger">' . $_SESSION['form_error'] . '</div>';
                    unset($_SESSION['form_error']);
                }
                if (isset($_SESSION['form_success'])) {
                    echo '<div class="alert alert-success">' . $_SESSION['form_success'] . '</div>';
                    unset($_SESSION['form_success']);
                }
                ?>

                <form method="POST" action="process_report.php" class="form">
                    <input type="hidden" name="complaint_id" value="<?php echo $complaint_id; ?>">
                    <input type="hidden" name="officer_id" value="<?php echo $officer_id; ?>">

                    <div class="form-group">
                        <label for="findings">Findings</label>
                        <textarea id="findings" name="findings" rows="6" required><?php echo $report ? $report['findings'] : ''; ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="recommendations">Recommendations</label>
                        <textarea id="recommendations" name="recommendations" rows="4"><?php echo $report ? ($report['recommendations'] ? $report['recommendations'] : '') : ''; ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="status">Report Status</label>
                        <select id="status" name="status" required>
                            <option value="draft" <?php echo (!$report || $report['status'] === 'draft') ? 'selected' : ''; ?>>Save as Draft</option>
                            <option value="submitted" <?php echo ($report && $report['status'] === 'submitted') ? 'selected' : ''; ?>>Submit for Approval</option>
                        </select>
                    </div>

                    <div class="action-buttons">
                        <button type="submit" class="btn btn-primary">Save Report</button>
                        <a href="view_complaint_detail.php?id=<?php echo $complaint_id; ?>" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
