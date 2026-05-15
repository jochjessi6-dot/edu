<?php
header('Content-Type: application/json');
require_once 'database.php';

$database = new Database();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (!empty($data['username']) && !empty($data['password'])) {
        try {
            $stmt = $db->prepare("SELECT id, username, password, phone FROM users WHERE username = ?");
            $stmt->execute([$data['username']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && $user['password'] === $data['password']) {
                echo json_encode([
                    'success' => true, 
                    'message' => 'Login successful!',
                    'user' => [
                        'id' => $user['id'],
                        'username' => $user['username'],
                        'phone' => $user['phone']
                    ]
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'ឈ្មោះ ឬលេខសម្ងាត់ខុស!']);
            }
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'បញ្ចូលទិន្នន័យមិនគ្រប់គ្រាន់']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
