<?php
// Process Complaint Approval/Rejection
include '../includes/config.php';
include '../includes/functions.php';

requireLogin();
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $complaint_id = intval($_POST['complaint_id']);
    $action = sanitizeInput($_POST['action']);
    $officer_id = isset($_POST['officer_id']) && !empty($_POST['officer_id']) ? intval($_POST['officer_id']) : null;
    $rejection_reason = isset($_POST['rejection_reason']) ? sanitizeInput($_POST['rejection_reason']) : '';
    
    // Validate
    if (empty($action) || !in_array($action, array('approve', 'reject'))) {
        $_SESSION['form_error'] = "Invalid action.";
        redirect(SITE_URL . 'admin/approve_complaint.php?id=' . $complaint_id);
    }
    
    // Get complaint
    $query = "SELECT * FROM complaints WHERE complaint_id = $complaint_id";
    $result = $conn->query($query);
    
    if ($result->num_rows === 0 || $result->fetch_assoc()['status'] !== 'pending') {
        $_SESSION['form_error'] = "Invalid complaint or already processed.";
        redirect(SITE_URL . 'admin/view_complaints.php');
    }
    
    if ($action === 'approve') {
        // Approve complaint
        if ($officer_id === null) {
            $_SESSION['form_error'] = "Please select an officer.";
            redirect(SITE_URL . 'admin/approve_complaint.php?id=' . $complaint_id);
        }
        
        $update_query = "UPDATE complaints SET status = 'approved', assigned_officer_id = $officer_id, approval_date = NOW() WHERE complaint_id = $complaint_id";
        
        // Also update officer status
        if ($conn->query($update_query) === TRUE) {
            auditLog('APPROVE_COMPLAINT', 'complaints', $complaint_id, 'Complaint approved and assigned to officer');
            $_SESSION['form_success'] = "Complaint approved and assigned to officer.";
        }
    } else if ($action === 'reject') {
        // Reject complaint
        $rejection_reason = $conn->real_escape_string($rejection_reason);
        
        if (empty($rejection_reason)) {
            $_SESSION['form_error'] = "Please provide rejection reason.";
            redirect(SITE_URL . 'admin/approve_complaint.php?id=' . $complaint_id);
        }
        
        $update_query = "UPDATE complaints SET status = 'rejected', rejection_reason = '$rejection_reason', approval_date = NOW() WHERE complaint_id = $complaint_id";
        
        if ($conn->query($update_query) === TRUE) {
            auditLog('REJECT_COMPLAINT', 'complaints', $complaint_id, 'Complaint rejected');
            $_SESSION['form_success'] = "Complaint rejected.";
        }
    }
    
    redirect(SITE_URL . 'admin/view_complaints.php?filter=pending');
} else {
    redirect(SITE_URL . 'admin/view_complaints.php');
}
