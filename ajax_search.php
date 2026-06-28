<?php
require_once 'functions.php';

$query = trim($_GET['q'] ?? '');
$response = [];

if ($query === '') {
    $stmt = $pdo->query('SELECT id, title, description, price FROM courses ORDER BY created_at DESC LIMIT 12');
    $response = $stmt->fetchAll();
} else {
    $like = '%' . $query . '%';
    $stmt = $pdo->prepare('SELECT id, title, description, price FROM courses WHERE title LIKE ? OR description LIKE ? ORDER BY created_at DESC LIMIT 12');
    $stmt->execute([$like, $like]);
    $response = $stmt->fetchAll();
}

header('Content-Type: application/json');
echo json_encode($response);
