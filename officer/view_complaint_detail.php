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

// Get complaint details (only if assigned to this officer)
$query = "SELECT c.*, u.first_name, u.last_name, u.email, u.phone FROM complaints c 
          JOIN users u ON c.citizen_id = u.user_id 
          WHERE c.complaint_id = $complaint_id AND c.assigned_officer_id = $officer_id";
$result = $conn->query($query);

if ($result->num_rows === 0) {
    redirect(SITE_URL . 'officer/view_complaints.php');
}

$complaint = $result->fetch_assoc();

// Get evidence files
$evidence_query = "SELECT * FROM evidence WHERE complaint_id = $complaint_id";
$evidence_result = $conn->query($evidence_query);
$evidence_files = array();
while ($row = $evidence_result->fetch_assoc()) {
    $evidence_files[] = $row;
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
    <title>Complaint Details - Investigation Officer</title>
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
                <div class="detail-header">
                    <h2>Complaint #<?php echo $complaint['complaint_id']; ?></h2>
                    <span class="badge badge-<?php echo $complaint['status']; ?>"><?php echo ucfirst(str_replace('_', ' ', $complaint['status'])); ?></span>
                </div>

                <div class="detail-box">
                    <h3>Complaint Information</h3>
                    <div class="info-row">
                        <th>Complainant:</th>
                        <td><?php echo $complaint['first_name'] . ' ' . $complaint['last_name']; ?><br><small><?php echo $complaint['email']; ?> | <?php echo $complaint['phone']; ?></small></td>
                    </div>
                    <div class="info-row">
                        <th>Title:</th>
                        <td><?php echo $complaint['title']; ?></td>
                    </div>
                    <div class="info-row">
                        <th>Category:</th>
                        <td><?php echo $complaint['category']; ?></td>
                    </div>
                    <div class="info-row">
                        <th>Location:</th>
                        <td><?php echo $complaint['location']; ?></td>
                    </div>
                    <div class="info-row">
                        <th>Date of Incident:</th>
                        <td><?php echo formatDate($complaint['complaint_date']); ?></td>
                    </div>
                    <div class="info-row">
                        <th>Description:</th>
                        <td><?php echo nl2br($complaint['description']); ?></td>
                    </div>
                </div>

                <?php if (count($evidence_files) > 0): ?>
                <div class="detail-box">
                    <h3>Evidence Files (<?php echo count($evidence_files); ?>)</h3>
                    <div class="evidence-list">
                        <?php foreach ($evidence_files as $file): ?>
                        <div class="evidence-item">
                            <p><strong><?php echo $file['file_name']; ?></strong> (<?php echo round($file['file_size'] / 1024); ?> KB)</p>
                            <p class="text-muted">Uploaded: <?php echo formatDateTime($file['uploaded_at']); ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($report): ?>
                <div class="detail-box">
                    <h3>Investigation Report</h3>
                    <div class="info-row">
                        <th>Status:</th>
                        <td><?php echo ucfirst($report['status']); ?></td>
                    </div>
                    <div class="info-row">
                        <th>Findings:</th>
                        <td><?php echo nl2br($report['findings']); ?></td>
                    </div>
                    <?php if ($report['recommendations']): ?>
                    <div class="info-row">
                        <th>Recommendations:</th>
                        <td><?php echo nl2br($report['recommendations']); ?></td>
                    </div>
                    <?php endif; ?>
                    <div class="info-row">
                        <th>Created:</th>
                        <td><?php echo formatDateTime($report['created_at']); ?></td>
                    </div>
                    <?php if ($report['submitted_date']): ?>
                    <div class="info-row">
                        <th>Submitted:</th>
                        <td><?php echo formatDateTime($report['submitted_date']); ?></td>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <?php if (!$report || $report['status'] === 'draft'): ?>
                <div class="detail-box">
                    <h3><?php echo $report ? 'Update' : 'Submit'; ?> Investigation Report</h3>
                    <a href="submit_report.php?id=<?php echo $complaint['complaint_id']; ?>" class="btn btn-primary">
                        <?php echo $report ? 'Edit Report' : 'Create Report'; ?>
                    </a>
                </div>
                <?php endif; ?>

                <?php if ($complaint['status'] === 'under_investigation'): ?>
                <div class="detail-box">
                    <h3>Mark Case</h3>
                    <a href="mark_completed.php?id=<?php echo $complaint['complaint_id']; ?>" class="btn btn-success">Mark as Completed</a>
                </div>
                <?php endif; ?>

                <div class="action-buttons">
                    <a href="view_complaints.php" class="btn btn-secondary">Back to Complaints</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
