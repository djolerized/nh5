<?php
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

if (empty($name) || empty($email) || empty($phone)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Sva obavezna polja moraju biti popunjena']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Neispravna email adresa']);
    exit;
}

// Slanje email-a
$to = 'prodaja@8rooms.rs';
$subject = 'Nikolaja Hartviga 5';

$email_body = "Nova poruka sa sajta NH5\n\n";
$email_body .= "Ime i prezime: " . htmlspecialchars($name) . "\n";
$email_body .= "Email: " . htmlspecialchars($email) . "\n";
$email_body .= "Telefon: " . htmlspecialchars($phone) . "\n";
$email_body .= "Poruka: " . htmlspecialchars($message) . "\n";

$headers = "From: noreply@nikolajahartviga5.rs\r\n";
$headers .= "Reply-To: " . $email . "\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

$emailSent = mail($to, $subject, $email_body, $headers);

// Monday.com API — kreiranje lead-a
$column_values = json_encode([
    'text_mkx04g0' => $phone,
    'lead_email' => ['email' => $email, 'text' => $email],
    'text_mkx7vkqx' => $message,
    'text_mkx074pr' => 'Website',
]);

$mutation = 'mutation { create_item(board_id: ' . MONDAY_BOARD_ID . ', item_name: "' . addslashes($name) . '", column_values: "' . addslashes($column_values) . '") { id } }';

$ch = curl_init('https://api.monday.com/v2');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Authorization: ' . MONDAY_API_TOKEN,
    ],
    CURLOPT_POSTFIELDS => json_encode(['query' => $mutation]),
]);

$mondayResponse = curl_exec($ch);
$mondayError = curl_error($ch);
curl_close($ch);

$mondaySuccess = false;
if (!$mondayError) {
    $mondayData = json_decode($mondayResponse, true);
    if (isset($mondayData['data']['create_item']['id'])) {
        $mondaySuccess = true;
    }
}

if ($emailSent && $mondaySuccess) {
    echo json_encode(['success' => true, 'message' => 'Poruka uspešno poslata']);
} elseif ($emailSent) {
    echo json_encode(['success' => true, 'message' => 'Poruka uspešno poslata']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Greška pri slanju poruke']);
}
?>
