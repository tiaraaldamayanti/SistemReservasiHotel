<?php
require_once '../includes/db.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['order']) || !is_array($data['order'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Data tidak valid']);
    exit;
}

$order = $data['order'];

foreach ($order as $position => $id) {
    $stmt = $conn->prepare("UPDATE rooms SET position = ? WHERE id = ?");
    $stmt->bind_param("ii", $position, $id);
    $stmt->execute();
}

echo json_encode(['success' => true]);
?>
