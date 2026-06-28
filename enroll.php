<?php
require_once 'functions.php';
require_login();

$courseId = filter_input(INPUT_POST, 'course_id', FILTER_VALIDATE_INT);
if (!$courseId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid course ID.']);
    exit;
}

$stmt = $pdo->prepare('SELECT id FROM courses WHERE id = ?');
$stmt->execute([$courseId]);
$course = $stmt->fetch();
if (!$course) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Course not found.']);
    exit;
}

try {
    $stmt = $pdo->prepare('INSERT INTO enrollments (user_id, course_id, enrolled_at) VALUES (?, ?, NOW())');
    $stmt->execute([$_SESSION['user_id'], $courseId]);
    echo json_encode(['success' => true, 'message' => 'Enrolled successfully.']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'You are already enrolled or an error occurred.']);
}
