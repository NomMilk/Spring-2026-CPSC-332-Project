<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

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

$vin = isset($_GET['vin']) ? trim($_GET['vin']) : '';

if ($vin === '') {
    echo json_encode(['error' => 'VIN is required']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT
            v.vin,
            v.brand,
            v.model,
            v.year,
            v.mileage,
            v.price,
            v.exterior_color,
            v.interior_color,
            v.seating_capacity,
            v.mpg_city,
            v.mpg_highway,
            v.ev_range,
            bs.style        AS body_style,
            dt.drivetrain   AS drivetrain,
            tr.name         AS transmission,
            ft.name         AS fuel_type,
            s.name          AS store_name,
            a.street_number AS store_street_number,
            a.street_name   AS store_street_name,
            a.city          AS store_city,
            a.state         AS store_state,
            a.zip           AS store_zip
        FROM Vehicle v
        LEFT JOIN Body_Style   bs ON v.body_style_id   = bs.body_style_id
        LEFT JOIN Drivetrain   dt ON v.drivetrain_id   = dt.drivetrain_id
        LEFT JOIN Transmission tr ON v.transmission_id = tr.transmission_id
        LEFT JOIN Fuel_Type    ft ON v.fuel_type_id    = ft.fuel_type_id
        LEFT JOIN Store         s ON v.store_id         = s.store_id
        LEFT JOIN Address       a ON s.address_id      = a.address_id
        WHERE v.vin = ?
    ");
    $stmt->execute([$vin]);
    $vehicle = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$vehicle) {
        echo json_encode(['error' => 'Vehicle not found']);
        exit;
    }

    // Conditions
    $cStmt = $pdo->prepare("
        SELECT c.condition_name FROM Vehicle_Condition vc
        JOIN Vehicle_Condition_Type c ON vc.condition_id = c.condition_id
        WHERE vc.vin = ?
    ");
    $cStmt->execute([$vin]);
    $vehicle['conditions'] = $cStmt->fetchAll(PDO::FETCH_COLUMN);

    // Features
    $fStmt = $pdo->prepare("
        SELECT f.feature_name FROM Vehicle_Feature vf
        JOIN Feature f ON vf.feature_id = f.feature_id
        WHERE vf.vin = ?
    ");
    $fStmt->execute([$vin]);
    $vehicle['features'] = $fStmt->fetchAll(PDO::FETCH_COLUMN);

    $vehicle['image_url'] = 'https://cdn.imagin.studio/getimage?' . http_build_query([
        'customer'    => 'img',
        'make'        => $vehicle['brand'],
        'modelFamily' => $vehicle['model'],
        'modelYear'   => $vehicle['year'],
        'paintId'     => 'default',
    ]);

    echo json_encode(['success' => true, 'vehicle' => $vehicle]);

} catch (PDOException $e) {
    echo json_encode(['error' => 'Query failed: ' . $e->getMessage()]);
}
?>
