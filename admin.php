<?php
header('Content-Type: application/json');

$host = '127.0.0.1';
$port = '3306';
$db   = 'car_dealership';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['error' => 'DB connection failed: ' . $e->getMessage()]);
    exit;
}

$action = trim($_POST['action'] ?? '');

// ── DELETE ──────────────────────────────────────────────────────────────────

if ($action === 'delete') {
    $vin = trim($_POST['vin'] ?? '');
    if ($vin === '') {
        echo json_encode(['error' => 'VIN is required']);
        exit;
    }
    try {
        $pdo->beginTransaction();
        // Remove dependent rows before removing the vehicle
        foreach (['Purchase', 'Vehicle_Condition', 'Vehicle_Feature'] as $tbl) {
            $pdo->prepare("DELETE FROM `$tbl` WHERE vin = ?")->execute([$vin]);
        }
        $rows = $pdo->prepare("DELETE FROM Vehicle WHERE vin = ?");
        $rows->execute([$vin]);
        if ($rows->rowCount() === 0) {
            $pdo->rollBack();
            echo json_encode(['error' => 'Vehicle not found']);
            exit;
        }
        $pdo->commit();
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo json_encode(['error' => 'Delete failed: ' . $e->getMessage()]);
    }
    exit;
}

// ── ADD ─────────────────────────────────────────────────────────────────────

if ($action === 'add') {

    $vin            = trim($_POST['vin']            ?? '');
    $brand          = trim($_POST['brand']          ?? '');
    $model          = trim($_POST['model']          ?? '');
    $year           =       $_POST['year']          ?? '';
    $mileage        =       $_POST['mileage']       ?? '';
    $price          =       $_POST['price']         ?? '';
    $ext_color      = trim($_POST['ext_color']      ?? '') ?: null;
    $int_color      = trim($_POST['int_color']      ?? '') ?: null;
    $seating        =       $_POST['seating']       ?? '';
    $body_style_id  =       $_POST['body_style_id'] ?? '';
    $drivetrain_id  =       $_POST['drivetrain_id'] ?? '';
    $trans_id       =       $_POST['trans_id']      ?? '';
    $fuel_id        =       $_POST['fuel_id']       ?? '';
    $mpg_city       =       $_POST['mpg_city']      ?? '';
    $mpg_hwy        =       $_POST['mpg_hwy']       ?? '';
    $ev_range       =       $_POST['ev_range']      ?? '';
    $store_id       =       $_POST['store_id']      ?? '';
    $conditions     = (array)($_POST['conditions']  ?? []);
    $features       = (array)($_POST['features']    ?? []);

    foreach (['vin' => $vin, 'brand' => $brand, 'model' => $model, 'year' => $year] as $f => $v) {
        if ($v === '') {
            echo json_encode(['error' => "Missing required field: $f"]);
            exit;
        }
    }

    $int  = fn($v) => $v !== '' ? (int)$v    : null;
    $flt  = fn($v) => $v !== '' ? (float)$v  : null;

    try {
        $pdo->beginTransaction();

        $pdo->prepare("
            INSERT INTO Vehicle (
                vin, body_style_id, drivetrain_id, transmission_id, fuel_type_id,
                brand, model, year, mileage, price,
                interior_color, exterior_color, seating_capacity,
                mpg_city, mpg_highway, ev_range, store_id
            ) VALUES (?,?,?,?,?, ?,?,?,?,?, ?,?,?, ?,?,?,?)
        ")->execute([
            $vin,
            $int($body_style_id), $int($drivetrain_id), $int($trans_id), $int($fuel_id),
            $brand, $model, (int)$year,
            $int($mileage), $flt($price),
            $int_color, $ext_color,
            $int($seating),
            $int($mpg_city), $int($mpg_hwy), $int($ev_range),
            $int($store_id),
        ]);

        $cStmt = $pdo->prepare("INSERT INTO Vehicle_Condition (vin, condition_id) VALUES (?,?)");
        foreach ($conditions as $cid) {
            if ($cid !== '') $cStmt->execute([$vin, (int)$cid]);
        }

        $fStmt = $pdo->prepare("INSERT INTO Vehicle_Feature (vin, feature_id) VALUES (?,?)");
        foreach ($features as $fid) {
            if ($fid !== '') $fStmt->execute([$vin, (int)$fid]);
        }

        $pdo->commit();
        echo json_encode(['success' => true]);

    } catch (PDOException $e) {
        $pdo->rollBack();
        echo json_encode([
            'error' => $e->getCode() === '23000'
                ? 'A vehicle with that VIN already exists.'
                : 'Insert failed: ' . $e->getMessage(),
        ]);
    }
    exit;
}

echo json_encode(['error' => 'Unknown action']);
?>
