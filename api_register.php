<?php
header('Content-Type: application/json');
require_once 'database.php';

$database = new Database();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (!empty($data['username']) && !empty($data['password'])) {
        try {
            $stmt = $db->prepare("INSERT INTO users (username, password, phone) VALUES (?, ?, ?)");
            if ($stmt->execute([$data['username'], $data['password'], $data['phone'] ?? ''])) {
                echo json_encode(['success' => true, 'message' => 'Account created successfully!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to create account.']);
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                echo json_encode(['success' => false, 'message' => 'ឈ្មោះអ្នកប្រើនេះមានរួចហើយ!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            }
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'បញ្ចូលទិន្នន័យមិនគ្រប់គ្រាន់']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
