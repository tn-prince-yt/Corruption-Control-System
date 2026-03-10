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

// Get assigned complaints
$complaints_query = "SELECT c.*, u.first_name, u.last_name, u.email 
                     FROM complaints c 
                     JOIN users u ON c.citizen_id = u.user_id 
                     WHERE c.assigned_officer_id = $officer_id 
                     ORDER BY c.created_at DESC";
$result = $conn->query($complaints_query);
$complaints = array();
while ($row = $result->fetch_assoc()) {
    $complaints[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assigned Complaints - Investigation Officer</title>
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
                <h2>Assigned Complaints</h2>
                
                <?php if (count($complaints) > 0): ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Complainant</th>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Evidence</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($complaints as $complaint): ?>
                            <tr>
                                <td>#<?php echo $complaint['complaint_id']; ?></td>
                                <td><?php echo $complaint['first_name'] . ' ' . $complaint['last_name']; ?></td>
                                <td><?php echo $complaint['title']; ?></td>
                                <td><?php echo $complaint['category']; ?></td>
                                <td><span class="badge badge-<?php echo $complaint['status']; ?>"><?php echo ucfirst(str_replace('_', ' ', $complaint['status'])); ?></span></td>
                                <td><?php echo $complaint['evidence_count']; ?></td>
                                <td><?php echo formatDate($complaint['complaint_date']); ?></td>
                                <td><a href="view_complaint_detail.php?id=<?php echo $complaint['complaint_id']; ?>" class="btn btn-sm btn-primary">View</a></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="empty-state">
                    <p>No complaints assigned to you yet.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
