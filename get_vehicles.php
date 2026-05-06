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

// ── Collect filter params from GET request ──
$brands         = isset($_GET['brands'])         ? $_GET['brands']         : [];
$model          = isset($_GET['model'])          ? trim($_GET['model'])     : '';
$year_min       = isset($_GET['year_min'])       ? (int)$_GET['year_min']  : null;
$year_max       = isset($_GET['year_max'])       ? (int)$_GET['year_max']  : null;
$price_min      = isset($_GET['price_min'])      ? (float)$_GET['price_min'] : null;
$price_max      = isset($_GET['price_max'])      ? (float)$_GET['price_max'] : null;
$mileage_max    = isset($_GET['mileage_max'])    ? (int)$_GET['mileage_max'] : null;
$ext_colors     = isset($_GET['ext_colors'])     ? $_GET['ext_colors']     : [];
$int_colors     = isset($_GET['int_colors'])     ? $_GET['int_colors']     : [];
$seating_min    = isset($_GET['seating_min'])    ? (int)$_GET['seating_min'] : null;
$conditions     = isset($_GET['conditions'])     ? $_GET['conditions']     : [];
$body_styles    = isset($_GET['body_styles'])    ? $_GET['body_styles']    : [];
$fuel_types     = isset($_GET['fuel_types'])     ? $_GET['fuel_types']     : [];
$transmissions  = isset($_GET['transmissions'])  ? $_GET['transmissions']  : [];
$drivetrains    = isset($_GET['drivetrains'])    ? $_GET['drivetrains']    : [];
$mpg_min        = isset($_GET['mpg_min'])        ? (int)$_GET['mpg_min']   : null;
$range_min      = isset($_GET['range_min'])      ? (int)$_GET['range_min'] : null;
$features       = isset($_GET['features'])       ? $_GET['features']       : [];
$zip            = isset($_GET['zip'])            ? trim($_GET['zip'])       : '';

// ── Build query ──
$params = [];

$sql = "
    SELECT DISTINCT
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
        a.zip           AS store_zip
    FROM Vehicle v
    LEFT JOIN Body_Style  bs ON v.body_style_id    = bs.body_style_id
    LEFT JOIN Drivetrain  dt ON v.drivetrain_id     = dt.drivetrain_id
    LEFT JOIN Transmission tr ON v.transmission_id  = tr.transmission_id
    LEFT JOIN Fuel_Type   ft ON v.fuel_type_id      = ft.fuel_type_id
    LEFT JOIN Store        s ON v.at_store_id        = s.store_id
    LEFT JOIN Address      a ON s.address_id         = a.address_id
    WHERE 1=1
";

// Brand filter
if (!empty($brands)) {
    $placeholders = implode(',', array_fill(0, count($brands), '?'));
    $sql .= " AND v.brand IN ($placeholders)";
    $params = array_merge($params, $brands);
}

// Model filter
if ($model !== '') {
    $sql .= " AND v.model LIKE ?";
    $params[] = '%' . $model . '%';
}

// Year filter
if ($year_min !== null) {
    $sql .= " AND v.year >= ?";
    $params[] = $year_min;
}
if ($year_max !== null) {
    $sql .= " AND v.year <= ?";
    $params[] = $year_max;
}

// Price filter
if ($price_min !== null) {
    $sql .= " AND v.price >= ?";
    $params[] = $price_min;
}
if ($price_max !== null) {
    $sql .= " AND v.price <= ?";
    $params[] = $price_max;
}

// Mileage filter
if ($mileage_max !== null) {
    $sql .= " AND v.mileage <= ?";
    $params[] = $mileage_max;
}

// Exterior color filter
if (!empty($ext_colors)) {
    $placeholders = implode(',', array_fill(0, count($ext_colors), '?'));
    $sql .= " AND v.exterior_color IN ($placeholders)";
    $params = array_merge($params, $ext_colors);
}

// Interior color filter
if (!empty($int_colors)) {
    $placeholders = implode(',', array_fill(0, count($int_colors), '?'));
    $sql .= " AND v.interior_color IN ($placeholders)";
    $params = array_merge($params, $int_colors);
}

// Seating filter
if ($seating_min !== null) {
    $sql .= " AND v.seating_capacity >= ?";
    $params[] = $seating_min;
}

// Body style filter
if (!empty($body_styles)) {
    $placeholders = implode(',', array_fill(0, count($body_styles), '?'));
    $sql .= " AND bs.style IN ($placeholders)";
    $params = array_merge($params, $body_styles);
}

// Fuel type filter
if (!empty($fuel_types)) {
    $placeholders = implode(',', array_fill(0, count($fuel_types), '?'));
    $sql .= " AND ft.name IN ($placeholders)";
    $params = array_merge($params, $fuel_types);
}

// Transmission filter
if (!empty($transmissions)) {
    $placeholders = implode(',', array_fill(0, count($transmissions), '?'));
    $sql .= " AND tr.name IN ($placeholders)";
    $params = array_merge($params, $transmissions);
}

// Drivetrain filter
if (!empty($drivetrains)) {
    $placeholders = implode(',', array_fill(0, count($drivetrains), '?'));
    $sql .= " AND dt.drivetrain IN ($placeholders)";
    $params = array_merge($params, $drivetrains);
}

// MPG filter
if ($mpg_min !== null) {
    $sql .= " AND v.mpg_city >= ?";
    $params[] = $mpg_min;
}

// EV range filter
if ($range_min !== null) {
    $sql .= " AND v.ev_range >= ?";
    $params[] = $range_min;
}

// ZIP filter
if ($zip !== '') {
    $sql .= " AND a.zip = ?";
    $params[] = $zip;
}

// Condition filter (junction table)
if (!empty($conditions)) {
    $placeholders = implode(',', array_fill(0, count($conditions), '?'));
    $sql .= " AND v.vin IN (
        SELECT vc.vin FROM Vehicle_Condition vc
        JOIN `Condition` c ON vc.condition_id = c.condition_id
        WHERE c.condition_name IN ($placeholders)
    )";
    $params = array_merge($params, $conditions);
}

// Features filter (junction table - must have ALL selected features)
if (!empty($features)) {
    foreach ($features as $feature) {
        $sql .= " AND v.vin IN (
            SELECT vf.vin FROM Vehicle_Feature vf
            JOIN Feature f ON vf.feature_id = f.feature_id
            WHERE f.feature_name = ?
        )";
        $params[] = $feature;
    }
}

$sql .= " ORDER BY v.price ASC";

// ── Execute ──
try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $vehicles = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Attach conditions and features to each vehicle
    foreach ($vehicles as &$vehicle) {
        $vin = $vehicle['vin'];

        // Conditions
        $cStmt = $pdo->prepare("
            SELECT c.condition_name FROM Vehicle_Condition vc
            JOIN `Condition` c ON vc.condition_id = c.condition_id
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
    }

    echo json_encode(['success' => true, 'count' => count($vehicles), 'vehicles' => $vehicles]);

} catch (PDOException $e) {
    echo json_encode(['error' => 'Query failed: ' . $e->getMessage()]);
}
?>
