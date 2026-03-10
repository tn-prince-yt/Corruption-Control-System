<?php
// Process Report Submission
include '../includes/config.php';
include '../includes/functions.php';

requireLogin();
requireOfficer();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $complaint_id = intval($_POST['complaint_id']);
    $officer_id = intval($_POST['officer_id']);
    $findings = sanitizeInput($_POST['findings']);
    $recommendations = isset($_POST['recommendations']) ? sanitizeInput($_POST['recommendations']) : '';
    $status = sanitizeInput($_POST['status']);
    
    // Validate
    if (empty($findings)) {
        $_SESSION['form_error'] = "Findings are required.";
        redirect(SITE_URL . 'officer/submit_report.php?id=' . $complaint_id);
    }
    
    if (!in_array($status, array('draft', 'submitted'))) {
        $_SESSION['form_error'] = "Invalid status.";
        redirect(SITE_URL . 'officer/submit_report.php?id=' . $complaint_id);
    }
    
    // Escape for database
    $findings = $conn->real_escape_string($findings);
    $recommendations = $conn->real_escape_string($recommendations);
    
    // Check if report exists
    $report_query = "SELECT report_id FROM reports WHERE complaint_id = $complaint_id";
    $report_result = $conn->query($report_query);
    
    if ($report_result->num_rows > 0) {
        // Update existing report
        $submitted_date = ($status === 'submitted') ? "NOW()" : "NULL";
        $update_query = "UPDATE reports SET findings = '$findings', recommendations = '$recommendations', status = '$status', submitted_date = IF('$status' = 'submitted', NOW(), submitted_date) WHERE complaint_id = $complaint_id";
        
        if ($conn->query($update_query) === TRUE) {
            auditLog('UPDATE_REPORT', 'reports', $complaint_id, 'Investigation report updated');
            $_SESSION['form_success'] = "Report updated successfully.";
        } else {
            $_SESSION['form_error'] = "Failed to update report.";
        }
    } else {
        // Create new report
        $submitted_date = ($status === 'submitted') ? "NOW()" : "NULL";
        $insert_query = "INSERT INTO reports (complaint_id, officer_id, findings, recommendations, status, submitted_date) 
                        VALUES ($complaint_id, $officer_id, '$findings', '$recommendations', '$status', IF('$status' = 'submitted', NOW(), NULL))";
        
        if ($conn->query($insert_query) === TRUE) {
            auditLog('CREATE_REPORT', 'reports', $complaint_id, 'Investigation report created');
            $_SESSION['form_success'] = "Report submitted successfully.";
        } else {
            $_SESSION['form_error'] = "Failed to create report.";
        }
    }
    
    redirect(SITE_URL . 'officer/view_complaint_detail.php?id=' . $complaint_id);
} else {
    redirect(SITE_URL . 'officer/view_complaints.php');
}
