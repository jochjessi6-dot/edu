<?php
header('Content-Type: application/json');
require_once 'database.php';

$database = new Database();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    // Method 1: Get credentials using phone number
    if (!empty($data['phone'])) {
        try {
            $stmt = $db->prepare("SELECT id, username, password, phone FROM users WHERE phone = ?");
            $stmt->execute([$data['phone']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                echo json_encode([
                    'success' => true, 
                    'message' => 'ឈ្មោះប្រើប្រាស់ និងលេខសម្ងាត់របស់អ្នក:',
                    'credentials' => [
                        'username' => $user['username'],
                        'password' => $user['password'],
                        'phone' => $user['phone']
                    ]
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'មិនបានរកឃើញលេខទូរស័ព្ទនេះក្នុងប្រព័ន្ធ!']);
            }
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    } 
    // Method 2: Get credentials using username and phone (more secure verification)
    elseif (!empty($data['username']) && !empty($data['phone'])) {
        try {
            $stmt = $db->prepare("SELECT id, username, password, phone FROM users WHERE username = ? AND phone = ?");
            $stmt->execute([$data['username'], $data['phone']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                echo json_encode([
                    'success' => true, 
                    'message' => 'ឈ្មោះប្រើប្រាស់ និងលេខសម្ងាត់របស់អ្នក:',
                    'credentials' => [
                        'username' => $user['username'],
                        'password' => $user['password'],
                        'phone' => $user['phone']
                    ]
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'ឈ្មោះប្រើប្រាស់ ឬលេខទូរស័ព្ទខុស!']);
            }
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    } 
    else {
        echo json_encode(['success' => false, 'message' => 'សូមផ្តល់ឱ្យលេខទូរស័ព្ទ ឬឈ្មោះប្រើប្រាស់ដើម្បីស្វាគមន៍គណនី']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
