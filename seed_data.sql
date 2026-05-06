-- ============================
-- SEED DATA for car_dealership
-- ============================

USE car_dealership;

-- ============================
-- LOOKUP TABLES
-- ============================

INSERT INTO Feature (feature_name) VALUES
('Backup Camera'),
('Navigation'),
('Moonroof'),
('Parking Sensors'),
('Third Row Seating'),
('Tow Hitch');

INSERT INTO `Condition` (condition_name) VALUES
('Certified Pre-Owned'),
('No Accidents'),
('Clean Title'),
('One Owner');

INSERT INTO Body_Style (style) VALUES
('Sedan'),
('SUV'),
('Truck'),
('Van'),
('Coupe'),
('Hatchback');

INSERT INTO Drivetrain (drivetrain) VALUES
('4WD'),
('AWD'),
('FWD'),
('RWD');

INSERT INTO Transmission (name) VALUES
('Automatic'),
('CVT'),
('Manual');

INSERT INTO Fuel_Type (name) VALUES
('EV'),
('PHEV'),
('Hybrid'),
('Gas'),
('Diesel'),
('Hydrogen');

INSERT INTO Role (name, permission) VALUES
('Admin', 'full_access'),
('Seller', 'list_vehicles'),
('Buyer', 'purchase_vehicles'),
('Manager', 'manage_store');

-- ============================
-- ADDRESSES
-- ============================

INSERT INTO Address (street_number, street_name, apt_number, city, state, zip) VALUES
('100', 'Main St',       NULL,   'Los Angeles',  'CA', '90001'),
('200', 'Sunset Blvd',   NULL,   'Los Angeles',  'CA', '90028'),
('300', 'Harbor Blvd',   NULL,   'Anaheim',      'CA', '92802'),
('401', 'Beach Blvd',    'B2',   'Huntington Beach', 'CA', '92648'),
('500', 'Ventura Blvd',  NULL,   'Sherman Oaks', 'CA', '91403'),
('12',  'Ocean Ave',     'Apt 3','Santa Monica',  'CA', '90401'),
('88',  'Maple Dr',      NULL,   'Pasadena',     'CA', '91101'),
('77',  'Elm St',        NULL,   'Long Beach',   'CA', '90802'),
('55',  'Pine Ave',      NULL,   'Torrance',     'CA', '90503'),
('33',  'Cedar Rd',      NULL,   'Fullerton',    'CA', '92832');

-- ============================
-- USERS
-- ============================
-- Passwords are placeholder hashes (in production, use PHP password_hash())

INSERT INTO User (address_id, fname, lname, phone, email, password) VALUES
(1, 'Alice',   'Johnson', '213-555-0101', 'alice@example.com',   '$2y$10$examplehashedpassword1'),
(2, 'Bob',     'Smith',   '213-555-0102', 'bob@example.com',     '$2y$10$examplehashedpassword2'),
(3, 'Carlos',  'Rivera',  '714-555-0103', 'carlos@example.com',  '$2y$10$examplehashedpassword3'),
(4, 'Diana',   'Lee',     '657-555-0104', 'diana@example.com',   '$2y$10$examplehashedpassword4'),
(5, 'Ethan',   'Wong',    '818-555-0105', 'ethan@example.com',   '$2y$10$examplehashedpassword5'),
(6, 'Fatima',  'Hassan',  '310-555-0106', 'fatima@example.com',  '$2y$10$examplehashedpassword6'),
(7, 'George',  'Park',    '626-555-0107', 'george@example.com',  '$2y$10$examplehashedpassword7'),
(8, 'Hannah',  'Nguyen',  '562-555-0108', 'hannah@example.com',  '$2y$10$examplehashedpassword8'),
(9, 'Ivan',    'Petrov',  '310-555-0109', 'ivan@example.com',    '$2y$10$examplehashedpassword9'),
(10,'Julia',   'Martinez','657-555-0110', 'julia@example.com',   '$2y$10$examplehashedpassword10');

-- ============================
-- STORES
-- ============================

INSERT INTO Store (manager_id, name, address_id) VALUES
(1, 'GrandTheftDeals LA',          1),
(3, 'GrandTheftDeals Anaheim',     3),
(5, 'GrandTheftDeals Sherman Oaks',5);

-- ============================
-- STAFF
-- ============================

INSERT INTO Staff (user_id, store_id, role_id) VALUES
(1, 1, 4),  -- Alice is Manager at Store 1
(2, 1, 2),  -- Bob is Seller at Store 1
(3, 2, 4),  -- Carlos is Manager at Store 2
(4, 2, 2),  -- Diana is Seller at Store 2
(5, 3, 4),  -- Ethan is Manager at Store 3
(6, 3, 2);  -- Fatima is Seller at Store 3

-- ============================
-- VEHICLES
-- ============================
-- body_style_id: 1=Sedan,2=SUV,3=Truck,4=Van,5=Coupe,6=Hatchback
-- drivetrain_id: 1=4WD,2=AWD,3=FWD,4=RWD
-- transmission_id: 1=Automatic,2=CVT,3=Manual
-- fuel_type_id: 1=EV,2=PHEV,3=Hybrid,4=Gas,5=Diesel,6=Hydrogen

INSERT INTO Vehicle (vin, body_style_id, drivetrain_id, transmission_id, fuel_type_id, at_store_id, brand, model, year, mileage, price, interior_color, exterior_color, seating_capacity, mpg_city, mpg_highway, ev_range) VALUES
('1HGBH41JXMN109186', 1, 3, 1, 4, 1, 'Toyota',   'Camry',        2022, 24000,  22500.00, 'Black',  'White',  5,  28, 39, NULL),
('2T1BURHE0JC037370', 6, 3, 2, 3, 1, 'Honda',    'Civic',        2023, 11000,  24999.00, 'Gray',   'Silver', 5,  40, 50, NULL),
('1C4RJFBG5FC625797', 2, 2, 1, 4, 2, 'Jeep',     'Grand Cherokee',2021,38000,  34500.00, 'Black',  'Black',  5,  19, 26, NULL),
('3VWFE21C04M000001', 2, 2, 1, 2, 2, 'Ford',     'Escape',       2022, 29000,  27800.00, 'Beige',  'Blue',   5,  38, 43, NULL),
('5YJSA1E26MF123456', 1, 2, 1, 1, 1, 'Tesla',    'Model 3',      2023,  8000,  41000.00, 'White',  'Red',    5,  NULL, NULL, 358),
('1FTFW1ET5DFC10312', 3, 1, 1, 4, 3, 'Ford',     'F-150',        2020, 52000,  38000.00, 'Gray',   'Gray',   6,  20, 26, NULL),
('WAUZZZ8V9BA123456', 1, 4, 3, 4, 3, 'BMW',      '3 Series',     2021, 31000,  36500.00, 'Beige',  'Navy',   5,  26, 36, NULL),
('1N4AL3AP8JC123456', 2, 2, 1, 3, 2, 'Toyota',   'RAV4 Hybrid',  2023,  5000,  38900.00, 'Black',  'Green',  5,  41, 38, NULL),
('2HKRM4H77GH123456', 2, 3, 2, 3, 2, 'Honda',    'CR-V',         2022, 19000,  29500.00, 'Tan',    'White',  5,  28, 34, NULL),
('1G1ZD5ST8JF123456', 5, 4, 3, 4, 3, 'Chevrolet','Camaro',       2020, 44000,  31000.00, 'Black',  'Yellow', 4,  16, 24, NULL);

-- ============================
-- VEHICLE CONDITIONS
-- ============================

INSERT INTO Vehicle_Condition (vin, condition_id) VALUES
('1HGBH41JXMN109186', 2),  -- No Accidents
('1HGBH41JXMN109186', 3),  -- Clean Title
('2T1BURHE0JC037370', 1),  -- Certified Pre-Owned
('2T1BURHE0JC037370', 4),  -- One Owner
('1C4RJFBG5FC625797', 3),  -- Clean Title
('3VWFE21C04M000001', 2),  -- No Accidents
('3VWFE21C04M000001', 3),  -- Clean Title
('5YJSA1E26MF123456', 1),  -- Certified Pre-Owned
('5YJSA1E26MF123456', 2),  -- No Accidents
('5YJSA1E26MF123456', 4),  -- One Owner
('1FTFW1ET5DFC10312', 3),  -- Clean Title
('WAUZZZ8V9BA123456', 2),  -- No Accidents
('1N4AL3AP8JC123456', 1),  -- Certified Pre-Owned
('1N4AL3AP8JC123456', 4),  -- One Owner
('2HKRM4H77GH123456', 3),  -- Clean Title
('1G1ZD5ST8JF123456', 2);  -- No Accidents

-- ============================
-- VEHICLE FEATURES
-- ============================

INSERT INTO Vehicle_Feature (vin, feature_id) VALUES
('1HGBH41JXMN109186', 1),  -- Backup Camera
('1HGBH41JXMN109186', 4),  -- Parking Sensors
('2T1BURHE0JC037370', 1),  -- Backup Camera
('2T1BURHE0JC037370', 2),  -- Navigation
('1C4RJFBG5FC625797', 1),  -- Backup Camera
('1C4RJFBG5FC625797', 2),  -- Navigation
('1C4RJFBG5FC625797', 3),  -- Moonroof
('1C4RJFBG5FC625797', 5),  -- Third Row Seating
('3VWFE21C04M000001', 1),  -- Backup Camera
('3VWFE21C04M000001', 4),  -- Parking Sensors
('5YJSA1E26MF123456', 1),  -- Backup Camera
('5YJSA1E26MF123456', 2),  -- Navigation
('5YJSA1E26MF123456', 4),  -- Parking Sensors
('1FTFW1ET5DFC10312', 1),  -- Backup Camera
('1FTFW1ET5DFC10312', 6),  -- Tow Hitch
('WAUZZZ8V9BA123456', 3),  -- Moonroof
('WAUZZZ8V9BA123456', 2),  -- Navigation
('1N4AL3AP8JC123456', 1),  -- Backup Camera
('1N4AL3AP8JC123456', 2),  -- Navigation
('1N4AL3AP8JC123456', 3),  -- Moonroof
('2HKRM4H77GH123456', 1),  -- Backup Camera
('2HKRM4H77GH123456', 4),  -- Parking Sensors
('1G1ZD5ST8JF123456', 1),  -- Backup Camera
('1G1ZD5ST8JF123456', 3);  -- Moonroof

-- ============================
-- PURCHASES
-- ============================

INSERT INTO Purchase (user_id, vin, price, purchase_date) VALUES
(7, '1HGBH41JXMN109186', 22500.00, '2026-03-15'),
(8, '1G1ZD5ST8JF123456', 31000.00, '2026-04-02'),
(9, '1FTFW1ET5DFC10312', 38000.00, '2026-04-20');
