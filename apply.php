<?php
require_once 'functions.php';
require_login();

$jobId = filter_input(INPUT_POST, 'job_id', FILTER_VALIDATE_INT);
if (!$jobId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid job ID.']);
    exit;
}

$stmt = $pdo->prepare('SELECT id FROM jobs WHERE id = ?');
$stmt->execute([$jobId]);
$job = $stmt->fetch();
if (!$job) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Job not found.']);
    exit;
}

try {
    $stmt = $pdo->prepare('INSERT INTO applications (user_id, job_id, applied_at) VALUES (?, ?, NOW())');
    $stmt->execute([$_SESSION['user_id'], $jobId]);
    echo json_encode(['success' => true, 'message' => 'Job application submitted successfully.']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'You have already applied for this position or an error occurred.']);
}
