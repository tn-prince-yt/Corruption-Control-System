<?php
// Mark Complaint as Completed
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

// Verify complaint is assigned to this officer and is under investigation
$query = "SELECT c.complaint_id FROM complaints c 
          WHERE c.complaint_id = $complaint_id 
          AND c.assigned_officer_id = $officer_id 
          AND c.status = 'under_investigation'";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    // Check if report is submitted
    $report_query = "SELECT report_id FROM reports WHERE complaint_id = $complaint_id AND status = 'submitted'";
    $report_result = $conn->query($report_query);
    
    if ($report_result->num_rows > 0) {
        // Mark as completed
        $update_query = "UPDATE complaints SET status = 'completed' WHERE complaint_id = $complaint_id";
        
        if ($conn->query($update_query) === TRUE) {
            auditLog('MARK_COMPLETED', 'complaints', $complaint_id, 'Investigation case marked as completed');
            $_SESSION['form_success'] = "Case marked as completed.";
        } else {
            $_SESSION['form_error'] = "Failed to mark case as completed.";
        }
    } else {
        $_SESSION['form_error'] = "Cannot mark case completed without a submitted report.";
    }
} else {
    $_SESSION['form_error'] = "Invalid complaint or not assigned to you.";
}

redirect(SITE_URL . 'officer/view_complaint_detail.php?id=' . $complaint_id);
