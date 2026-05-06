<?php
header('Content-Type: application/json');

$host = '127.0.0.1';
$port = '3306';
$db   = 'car_dealership';
$user = 'root';
$pass = '';

// ── Collect POST fields ──
$fname         = trim($_POST['fname']         ?? '');
$lname         = trim($_POST['lname']         ?? '');
$street_number = trim($_POST['street_number'] ?? '');
$street_name   = trim($_POST['street_name']   ?? '');
$apt_number    = trim($_POST['apt_number']    ?? '') ?: null;
$city          = trim($_POST['city']          ?? '');
$state         = trim($_POST['state']         ?? '');
$zip           = trim($_POST['zip']           ?? '');
$phone         = trim($_POST['phone']         ?? '');
$email         = trim($_POST['email']         ?? '');
$password      =       $_POST['password']     ?? '';

// ── Server-side validation ──
$required = [
    'fname'         => $fname,
    'lname'         => $lname,
    'street_number' => $street_number,
    'street_name'   => $street_name,
    'city'          => $city,
    'state'         => $state,
    'zip'           => $zip,
    'phone'         => $phone,
    'email'         => $email,
    'password'      => $password,
];

foreach ($required as $field => $value) {
    if ($value === '') {
        echo json_encode(['error' => 'Missing required field: ' . $field]);
        exit;
    }
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['error' => 'Invalid email address.']);
    exit;
}

if (strlen($password) < 8) {
    echo json_encode(['error' => 'Password must be at least 8 characters.']);
    exit;
}

// ── DB connection ──
try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['error' => 'DB connection failed: ' . $e->getMessage()]);
    exit;
}

// ── Duplicate email check ──
$dup = $pdo->prepare("SELECT user_id FROM User WHERE email = ?");
$dup->execute([$email]);
if ($dup->fetch()) {
    echo json_encode(['error' => 'An account with that email already exists.']);
    exit;
}

// ── Insert inside a transaction ──
try {
    $pdo->beginTransaction();

    $pdo->prepare("
        INSERT INTO Address (street_number, street_name, apt_number, city, state, zip)
        VALUES (?, ?, ?, ?, ?, ?)
    ")->execute([$street_number, $street_name, $apt_number, $city, $state, $zip]);

    $address_id = $pdo->lastInsertId();

    $pdo->prepare("
        INSERT INTO User (address_id, fname, lname, phone, email, password)
        VALUES (?, ?, ?, ?, ?, ?)
    ")->execute([$address_id, $fname, $lname, $phone, $email,
                 password_hash($password, PASSWORD_DEFAULT)]);

    $pdo->commit();
    echo json_encode(['success' => true]);

} catch (PDOException $e) {
    $pdo->rollBack();
    if ($e->getCode() === '23000') {
        echo json_encode(['error' => 'An account with that email already exists.']);
    } else {
        echo json_encode(['error' => 'Registration failed: ' . $e->getMessage()]);
    }
}
?>
